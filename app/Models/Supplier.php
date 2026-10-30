<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'nombre',
        'correo',
        'telefono',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'supplier_id');
    }
}
