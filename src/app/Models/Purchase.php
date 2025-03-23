<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_id',
        'payment_method',
        'postal_code',
        'address',
        'building',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function profile()
    {
        return $this->user->profile;
    }

    // 配送先住所をプロフィールから取得して保存
    public function updateShippingAddressFromProfile()
    {
        $profile = $this->profile;
        $this->postal_code = $profile->postal_code;
        $this->address = $profile->address;
        $this->building = $profile->building;
        $this->save();
    }

    // 商品の購入処理
    public function completePurchase()
    {
        $this->item->sold_status = true;
        $this->item->save();
        $this->save();
    }

    // 配送先住所を手動で更新する場合
    public function updateShippingAddress($postalCode, $address, $building = null)
    {
        $this->postal_code = $postalCode;
        $this->address = $address;
        $this->building = $building;
        $this->save();
    }
}
