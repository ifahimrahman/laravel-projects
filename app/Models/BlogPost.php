<?php

namespace App\Models;
use App\Scopes\LatestScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{

    use HasFactory;
    use SoftDeletes;
    // protected $table = 'blogposts';
    protected $fillable = ['title','content','user_id'];

    public function comments()
    {
        return $this->hasMany('App\Models\Comment')->latest();
    }

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    // public function tags()
    // {
    //     return $this->belongsToMany('App\Tag')->withTimestamps();
    // }

    public function image()
    {
        return $this->hasOne('App\Models\Image');
    }

    public function scopeLatest(Builder $query)
    {
        return $query->orderBy(static::CREATED_AT, 'desc');
    }

    public function scopeMostCommented(Builder $query)
    {
        // comments_count
        return $query->withCount('comments')->orderBy('comments_count', 'desc');
    }

    public static function boot()
    {
        parent::boot();

        // static::addGlobalScope(new LatestScope);

        static::deleting(function (BlogPost $blogPost) {
            $blogPost->comments()->delete();
            // $blogPost->image()->delete();
        });
        
        static::restoring(function (BlogPost $blogPost) {
            $blogPost->comments()->restore();
        });
    }
}
