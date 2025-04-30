<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsFeed extends Model
{
    // Specify the custom primary key
    protected $primaryKey = 'post_id';

    // Laravel expects plural table names, but yours is singular-ish
    protected $table = 'news_feed';

    // Mass assignable fields
    protected $fillable = [
        'center_id',
        'title',
        'content',
        'image_url',
    ];

    // Define relationship with ReliefCenter
    public function reliefCenter()
    {
        return $this->belongsTo(ReliefCenter::class, 'center_id', 'center_id');
    }
}
