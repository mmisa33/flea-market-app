<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Comment;
use App\Models\Category;
use App\Http\Requests\CommentRequest;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    // トップページ表示
    public function index(Request $request)
    {
        $isMyList = $request->query('tab') === 'mylist';
        $isAuth = auth()->check();

        // 未ログインの状態で「マイリスト」タブを開いた場合、空のリストを表示
        if ($isMyList && !$isAuth) {
            return view('index', ['items' => collect(), 'isMyList' => true, 'isAuth' => false]);
        }

        // いいねした商品を表示
        if ($isMyList) {
            $items = auth()->user()->likedItems;
        } else {
            // 他ユーザーの出品商品を取得
            $items = Item::where('user_id', '!=', auth()->id()) // 自分の出品した商品は除外
                ->when($request->filled('keyword'), function ($query) use ($request) {
                    return $query->where('name', 'like', '%' . $request->keyword . '%');
                })
                ->get();
        }

        return view('index', compact('items', 'isMyList', 'isAuth'));
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
    public function store(Request $request)
    {
        // バリデーション
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'category' => 'required|array',
            'condition' => 'required|in:new,used',
            'price' => 'required|numeric|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // 商品画像がアップロードされた場合
        if ($request->hasFile('image')) {
            // 画像をストレージに保存
            $imagePath = $request->file('image')->store('public/items');

            // 保存された画像のパスを取得
            $imageUrl = Storage::url($imagePath);
        } else {
            // 画像が選択されなかった場合、デフォルト値に設定
            $imageUrl = null;
        }

        // アイテムをデータベースに保存
        $item = new Item();
        $item->name = $request->name;
        $item->description = $request->description;
        $item->price = $request->price;
        $item->image = $imageUrl; // 商品画像のパスを保存
        $item->save();

        // 保存後、リダイレクトやメッセージなど
        return redirect()->route('items.index')->with('success', '商品が出品されました！');
    }
}