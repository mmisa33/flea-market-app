<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Comment;
use App\Models\Category;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\ItemRequest;

class ItemController extends Controller
{
    // 商品一覧ページ表示
    public function index(Request $request)
    {
        // タブの切り替え
        $isMyList = $request->query('page') === 'mylist';
        $activePage = $isMyList ? 'mylist' : 'recommended';

        // 検索機能
        $keyword = $request->input('keyword');
        $itemsQuery = Item::searchByKeyword($keyword);

        // ログアウト状態ではマイリスト非表示
        if ($isMyList && !auth()->check()) {
            return view('index', [
                'items' => collect(),
                'activePage' => $activePage,
                'isAuth' => false
            ]);
        }

        // ログイン状態ではマイリストにいいね商品を表示
        if ($isMyList && auth()->check()) {
            $likedItems = auth()->user()->likedItems->pluck('id');
            $itemsQuery->whereIn('id', $likedItems);
        } else {
            // 自分の出品した商品を一覧から除外
            $itemsQuery->where('user_id', '!=', auth()->id());
        }

        $items = $itemsQuery->get();

        return view('index', compact('activePage', 'items', 'keyword'));
    }

    // 商品詳細ページ表示
    public function show($item_id)
    {
        $item = Item::with(['categories', 'comments.user'])->findOrFail($item_id);

        // いいね機能の設定
        $liked = false;
        if (auth()->check()) {
            $user = auth()->user();
            $liked = $user->likedItems->contains($item->id);
        }

        // コメント数
        $likeCount = $item->likes->count();
        $commentCount = $item->comments->count();

        $isOwnItem = auth()->check() && auth()->id() === $item->user_id;

        return view('item.detail', compact('item', 'liked', 'likeCount', 'commentCount', 'isOwnItem'));
    }

    // コメント機能
    public function comment(CommentRequest $request, $item_id)
    {
        // ログインユーザーのみコメント可能
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $item = Item::findOrFail($item_id);

        $comment = new Comment();
        $comment->user_id = auth()->id();
        $comment->item_id = $item->id;
        $comment->content = $request->content;
        $comment->save();

        return redirect()->route('item.show', ['item_id' => $item_id]);
    }

    // いいね機能
    public function like(Item $item)
    {
        $user = auth()->user();

        if ($user->likedItems->contains($item->id)) {
            $user->likedItems()->detach($item->id);
        } else {
            $user->likedItems()->attach($item->id);
        }

        return redirect()->route('item.show', ['item_id' => $item->id]);
    }

    // 出品ページ表示
    public function create()
    {
        $categories = Category::all();
        return view('item.sell', compact('categories'));
    }

    // 出品処理
    public function store(ItemRequest $request)
    {
        // 商品画像のアップデート処理
        if ($request->hasFile('image_path')) {
            $imagePath = $request->file('image_path')->store('images/items', 'public');
        }

        // アイテムをデータベースに保存
        $item = new Item();
        $item->user_id = auth()->user()->id;
        $item->name = $request->name;
        $item->brand = $request->brand;
        $item->description = $request->description;
        $item->price = $request->price;
        $item->condition = $request->condition;
        $item->image_path = $imagePath;

        $item->save();

        // カテゴリが選択されていれば紐づけ
        if ($request->has('category')) {
            $item->categories()->attach($request->category);
        }

        return redirect()->route('profile.show');
    }
}