<?php

namespace App\Models;

use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;
use App\Traits\HasCreatorAndUpdater;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class File extends Model
{
    use HasFactory , NodeTrait ,HasCreatorAndUpdater, SoftDeletes;


    public function user()
    {
        return $this->belongsTo(User::class,'created_by');
    }
    public function parent()
    {
        return $this->belongsTo(File::class,'parent_id');
    }
    public function owner():Attribute
    {
        return Attribute::make(
            get:function(mixed $value , array $attributes) {
                return $attributes['created_by'] == Auth::id() ? 'me' : $this->user->name;
            });
    }
        
    

    public function isOwnedBy($userId)
    {
        return $this->created_by == $userId;
    
    }
    public function isRoot()
    {
        return $this->parent_id == null ;
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if(!$model->parent){
                return;
            }
            $model->path = (!$model ->parent->isRoot() ? $model->parent->path .'/' : '') . Str::slug( $model->name);
        });
    }
}
