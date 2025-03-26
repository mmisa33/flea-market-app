<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\ProfileRequest;
use App\Models\Profile;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit');
    }

    public function update(AddressRequest $addressRequest, ProfileRequest $profileRequest)
    {
        $user = auth()->user();

        // ユーザー名の更新
        $user->name = $profileRequest->name;
        $user->save();

        // プロフィールが存在する場合は更新、ない場合は新規作成
        $profile = $user->profile;
        if (!$profile) {
            $profile = new Profile();
            $profile->user_id = $user->id;
        }

        // 住所情報の更新（AddressRequest を使用）
        $profile->postal_code = $addressRequest->postal_code;
        $profile->address = $addressRequest->address;
        $profile->building = $addressRequest->building;

        // プロフィール画像の更新（ProfileRequest を使用）
        if ($profileRequest->hasFile('profile_image')) {
            $imagePath = $profileRequest->file('profile_image')->store('profile_images', 'public');

            if ($profile->profile_image) {
                Storage::disk('public')->delete($profile->profile_image);
            }

            $profile->profile_image = $imagePath;
        }

        $profile->save();

        return redirect('/?tab=mylist');
    }
}