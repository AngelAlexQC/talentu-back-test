<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    use HasFactory, Searchable;

    protected $fillable = [
        'name',
        'status',
    ];


    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
