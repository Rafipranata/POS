<?php

namespace App\Models;

use App\Models\InvoiceProduct;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function InvoiceProduct(): HasMany
    {
        return $this->hasMany(InvoiceProduct::class);
    }
}
