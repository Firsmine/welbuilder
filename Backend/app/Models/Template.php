<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = [
        'name',
        'slug'
    ];

    public function fields(){
        return $this->hasMany(TemplateField::class);
    }
}
