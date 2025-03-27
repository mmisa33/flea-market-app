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
        // 'tab'パラメータが'mylist'の場合、マイリストタブを表示
        $isMyList = $request->query('tab') === 'mylist';

        // ログインしていない場合、マイリストを表示するにはログインが必要
        if ($isMyList && !auth()->check()) {
            return redirect()->route('login');
        }

        // 商品データを取得
        $itemsQuery = Item::where('sold_status', 0);

        // マイリストの場合、ユーザーIDでフィルタリング
        if ($isMyList) {
            $itemsQuery->where('user_id', auth()->id());
        } else {
            $itemsQuery->where('user_id', '!=', auth()->id());
        }

        // 検索処理
        if ($request->has('keyword')) {
            $itemsQuery->where('name', 'like', '%' . $request->keyword . '%');
        }

        $items = $itemsQuery->get();

        return view('index', compact('items', 'isMyList'))->with('isAuth', auth()->check());
    }

    // マイリスト表示
    public function mylist(Request $request)
    {
        // ログインしているユーザーのマイリストの商品を表示
        $items = Item::where('sold_status', 0)
            ->where('user_id', auth()->id())
            ->get();

        return view('mylist', compact('items'));
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


    // public function purchase($item_id)
    // {
    //     $item = Item::findOrFail($item_id);

    //     if ($item->sold_status) {
    //         return redirect()->route('item.detail', ['item_id' => $item_id])->with('error', 'この商品はすでに売り切れです。');
    //     }

    //     $item->sold_status = true;
    //     $item->save();

    //     return redirect()->route('item.detail', ['item_id' => $item_id])->with('success', '購入が完了しました！');
    // }

    // public function store(Request $request)
    // {
    //     // 画像アップロード
    //     if ($request->hasFile('image')) {
    //         $imagePath = $request->file('image')->store('items', 'public');
    //     }

    //     // アイテムの保存
    //     Item::create([
    //         'user_id' => auth()->id(),
    //         'name' => $request->name,
    //         'price' => $request->price,
    //         'description' => $request->description,
    //         'condition' => $request->condition,
    //         'image_path' => $imagePath,
    //         'sold_status' => false,
    //     ]);

    //      $item->categories()->attach($request->categories); 

    // //     return redirect('/');
    // }
}