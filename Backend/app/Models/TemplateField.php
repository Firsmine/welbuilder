<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateField extends Model
{
    protected $table = 'template_fields';
    protected $fillable = [
        'template_id',
        'name',
        'slug',
        'type'
    ];

    public function template(){
        return $this->belongsTo(Template::class);
    }
}
