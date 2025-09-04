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
        'address_id',
        'payment_method',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function completePurchase()
    {
        $this->item->completePurchase();
    }

    public function partner()
    {
        return $this->item->user();
    }

    public function messages() {
        return $this->hasMany(Message::class);
    }

    public function ratings() {
        return $this->hasMany(Review::class);
    }
}