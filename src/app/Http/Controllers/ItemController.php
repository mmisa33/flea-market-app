<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
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

    public function store(Request $request)
    {
        // 画像アップロード
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('items', 'public');
        }

        // アイテムの保存
        Item::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'condition' => $request->condition,
            'image_path' => $imagePath,
            'sold_status' => false,
        ]);

        return redirect('/');
    }

    public function mylist(Request $request)
    {
        // ログインしているユーザーのマイリストの商品を表示
        $items = Item::where('sold_status', 0)
            ->where('user_id', auth()->id())
            ->get();

        return view('mylist', compact('items'));
    }
}