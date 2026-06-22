<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageSection extends Model
{
    protected $table = 'page_sections';
    protected $fillable = [
        'page_id',
        'template_id',
        'position',
        'name'
    ];

    public function page(){
        return $this->belongsTo(Page::class);
    }
    public function template(){
        return $this->belongsTo(Template::class);
    }
    public function fieldValues(){
        return $this->hasMany(SectionFieldValue::class);
    }
}
