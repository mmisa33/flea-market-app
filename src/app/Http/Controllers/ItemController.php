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
        $item = Item::with(['categories', 'comments.user'])
            ->withCount(['likes', 'comments'])
            ->findOrFail($item_id);

        // 商品状態のリストを定義
        $conditions = [
            'good' => '良好',
            'no_damage' => '目立った傷や汚れなし',
            'some_damage' => 'やや傷や汚れあり',
            'bad' => '状態が悪い'
        ];

        // ユーザー情報の取得
        $user = auth()->user();
        $liked = false;
        $isOwnItem = false;
        $likeCount = $item->likes_count;   // いいね数
        $commentCount = $item->comments_count;  // コメント数

        if ($user) {
            $liked = $user->likedItems->contains($item->id);
            $isOwnItem = $user->id === $item->user_id;
        }

        $conditionLabel = $conditions[$item->condition];

        return view('item.detail', compact('item', 'liked', 'isOwnItem', 'conditionLabel', 'likeCount', 'commentCount'));
    }

    // コメント機能
    public function comment(CommentRequest $request, $item_id)
    {
        $item = Item::findOrFail($item_id);

        $comment = new Comment();
        $comment->user_id = auth()->id();
        $comment->item_id = $item->id;
        $comment->content = $request->content;
        $comment->save();

        return redirect()->route('item.show', $item->id);
    }

    // いいね機能
    public function like(Item $item)
    {
        $user = auth()->user();

        if ($user->likedItems()->where('item_id', $item->id)->exists()) {
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

        // 商品状態のリストを定義
        $conditions = [
            'good' => '良好',
            'no_damage' => '目立った傷や汚れなし',
            'some_damage' => 'やや傷や汚れあり',
            'bad' => '状態が悪い'
        ];

        return view('item.sell', compact('categories', 'conditions'));
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