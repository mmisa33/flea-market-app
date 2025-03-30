<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Comment;
use App\Models\User;
use App\Models\Like;
use App\Http\Requests\CommentRequest;

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
}