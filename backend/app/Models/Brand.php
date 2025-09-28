<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    protected $fillable = ['nameOfBranch', 'phone', 'address', 'email'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'id_branch');
    }
}
