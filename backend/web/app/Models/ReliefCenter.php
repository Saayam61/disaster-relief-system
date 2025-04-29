<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReliefCenter extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'relief_centers';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'center_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'name',
        'location',
        'address',
        'capacity',
        'current_occupancy',
        'total_volunteers',
        'total_supplies',
        'contact_numbers',
        'is_active',
        'latitude',
        'longitude',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'double',
        'longitude' => 'double',
        'capacity' => 'integer',
        'current_occupancy' => 'integer',
        'total_volunteers' => 'integer',
    ];

    /**
     * Get the user that owns the relief center.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the volunteers associated with the relief center.
     */
    public function volunteers()
    {
        return $this->hasMany(Volunteer::class, 'center_id', 'center_id');
    }

    /**
     * Get the news feed items associated with the relief center.
     */
    public function newsFeed()
    {
        return $this->hasMany(NewsFeed::class, 'center_id', 'center_id');
    }

    /**
     * Get the contributions associated with the relief center.
     */
    public function contributions()
    {
        return $this->hasMany(Contribution::class, 'center_id', 'center_id');
    }
}