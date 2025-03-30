<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['purchase_id', 'item_id', 'payment_method', 'status'];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }
}