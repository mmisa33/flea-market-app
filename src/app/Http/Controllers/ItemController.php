<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Comment;
use App\Models\Category;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\ItemRequest;
use Illuminate\Support\Facades\Storage;


class ItemController extends Controller
{
    // トップページ表示
    public function index(Request $request)
    {
        $isMyList = $request->query('tab') === 'mylist';
        $isAuth = auth()->check();
        $keyword = $request->input('keyword');
        $tab = $request->query('tab', '');

        // タブの状態をビューに渡す
        $activeTab = $isMyList ? 'mylist' : 'recommended';

        // 初期の検索結果を取得
        $itemsQuery = Item::searchByKeyword($keyword);

        // 未ログインの状態で「マイリスト」タブを開いた場合、空のリストを表示
        if ($isMyList && !$isAuth) {
            return view('index', [
                'items' => collect(),
                'isMyList' => true,
                'isAuth' => false,
                'keyword' => $keyword,
                'tab' => $tab,
                'activeTab' => $activeTab // ここでビューに渡す
            ]);
        }

        // いいねした商品を取得
        if ($isMyList && $isAuth) {
            $likedItems = auth()->user()->likedItems->pluck('id'); // いいねした商品のIDを取得
            $itemsQuery->whereIn('id', $likedItems); // いいねした商品だけを表示
        } else {
            // 他ユーザーの出品商品を取得（検索結果を保持）
            $itemsQuery->where('user_id', '!=', auth()->id()); // 自分の出品した商品を除外
        }

        // クエリビルダを実行して、結果をコレクションに変換
        $items = $itemsQuery->get();

        return view('index', compact('activeTab', 'items', 'isMyList', 'isAuth', 'keyword', 'tab'));
    }

    // 商品詳細ページ表示
    public function show($item_id)
    {
        $item = Item::with(['categories', 'comments.user'])->findOrFail($item_id);

        $isAuth = auth()->check();

        $liked = false;
        if ($isAuth) {
            $user = auth()->user();

            if ($user->likedItems->contains($item->id)) {
                $liked = true;
            }
        }

        $likeCount = $item->likes->count();
        $commentCount = $item->comments->count();

        return view('item.detail', compact('item', 'isAuth', 'liked', 'likeCount', 'commentCount'));
    }

    // コメント機能
    public function comment(CommentRequest $request, $item_id)
    {
        // ログインユーザーのみコメント可能
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $item = Item::findOrFail($item_id);

        // コメントの作成
        $comment = new Comment();
        $comment->user_id = auth()->id();
        $comment->item_id = $item->id;
        $comment->content = $request->content;
        $comment->save();

        return redirect("/item/{$item_id}");
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

        return redirect()->back();
    }

    // 購入ページへ移動
    public function purchase(Item $item)
    {
        $isAuth = auth()->check();
        if (!$isAuth) {
            return redirect()->route('login');
        }

        return view('item.show', compact('item'));
    }

    // 出品ページの表示
    public function create()
    {
        $categories = Category::all();
        return view('item.sell', compact('categories'));
    }

    // 出品処理
    public function store(ItemRequest $request)
    {
        // 商品画像がアップロードされた場合
        if ($request->hasFile('image_path')) {
            // 画像をストレージに保存し、保存されたファイルのパスを取得
            $imagePath = $request->file('image_path')->store('items', 'public');
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

        return redirect('/mypage');
    }
}