<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image_path',
        'name',
        'brand',
        'price',
        'description',
        'condition',
        'sold_status',
    ];

    // 検索処理
    public static function searchByKeyword($keyword)
    {
        $query = self::query();

        if (!empty($keyword)) {
            $query->where('name', 'LIKE', "%{$keyword}%");
        }

        return $query;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'item_categories');
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'item_likes');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }

    public function completePurchase()
    {
        $this->sold_status = true;
        $this->save();
    }
}