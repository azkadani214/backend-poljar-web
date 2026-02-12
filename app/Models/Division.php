<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Division extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['name'];

    protected static function booted()
    {
        static::creating(function ($division) {
            $division->id = Str::uuid();
        });
    }

    /** RELATIONS */

    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'memberships');
    }
}
