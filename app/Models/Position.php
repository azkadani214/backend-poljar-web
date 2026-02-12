<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Position extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'division_id',
        'name',
        'level',
    ];

    protected static function booted()
    {
        static::creating(function ($position) {
            $position->id = Str::uuid();
        });
    }

    /** RELATIONS */

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }
}
