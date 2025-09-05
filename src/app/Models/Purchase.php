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
        'buyer_completed',
        'seller_completed',
    ];

    protected $casts = [
        'buyer_completed' => 'boolean',
        'seller_completed' => 'boolean',
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

    public function isCompleted(): bool
    {
        return $this->buyer_completed && $this->seller_completed;
    }

    public function isTrading(): bool
    {
        return ! $this->isCompleted();
    }

    public function partner()
    {
        return $this->item->user();
    }

    public function messages() {
        return $this->hasMany(Message::class);
    }

    public function reviews() {
        return $this->hasMany(Review::class);
    }
}