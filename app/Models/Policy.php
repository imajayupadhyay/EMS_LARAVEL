<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Policy extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'content', 'audience', 'audience_value', 'is_active', 'created_by'
    ];

    protected $casts = [
        'audience_value' => 'array',
        'is_active' => 'boolean',
    ];

    public function author()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
