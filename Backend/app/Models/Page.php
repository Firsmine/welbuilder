<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'summary'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function sections(){
        return $this->hasMany(PageSection::class)->orderBy('position');
    }
}
