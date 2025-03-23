<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'image_path', 'name', 'brand', 'price', 'description', 'condition', 'sold_status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsToMany(Category::class, 'item_categories');
    }

    public function like()
    {
        return $this->belongsToMany(User::class, 'item_likes');
    }

    public function comment()
    {
        return $this->hasMany(Comment::class);
    }

    public function purchase()
    {
        return $this->hasMany(Purchase::class);
    }
}
