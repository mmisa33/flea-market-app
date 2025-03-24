<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        // どのタブを表示するか判定
        $isMyList = $request->tab === 'mylist' || $request->is('mylist');

        if ($isMyList) {
            // マイリストならログインユーザーの商品を取得
            if (auth()->check()) {
                $items = Item::where('user_id', auth()->id())->where('sold_status', 0)->get();
            } else {
                $items = collect(); // 未ログインなら空データ
            }
        } else {
            // おすすめ（他のユーザーの商品を取得）
            $items = Item::where('sold_status', 0)
                ->where(function ($query) {
                    if (auth()->check()) {
                        $query->where('user_id', '!=', auth()->id());
                    }
                })
                ->get();
        }

        // 検索処理（共通）
        if ($request->has('keyword')) {
            $items = $items->filter(function ($item) use ($request) {
                return str_contains($item->name, $request->keyword);
            });
        }

        return view('index', compact('items', 'isMyList'))->with('isAuthenticated', auth()->check());
    }


    public function store(Request $request)
    {
        // 画像アップロード
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('items', 'public');
        }

        // アイテムの保存
        Item::create([
            'user_id' => auth()->id(), // 現在のユーザーIDを設定
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'condition' => $request->condition,
            'image_path' => $imagePath, // 画像パスを保存
            'sold_status' => false,
        ]);

        return redirect()->route('items.index');
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