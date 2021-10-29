<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Like extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable
     * @var array
     */
    protected $fillable = [
        'user_id',
        'likeable_id',
        'likeable_type',
        'liked',
    ];

    /** Get the user that likes a blog post
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function isLikedBy()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
    * Get the owning likeable model.
    * @return \Illuminate\Database\Eloquent\Relations\morphTo
    */
    public function likeable()
    {
        return $this->morphTo();
    }
}
