<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $table = 'produk';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'deskripsi_produk',
        'foto_produk',
        'harga',
        'stok',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'harga' => 'decimal:2',
        'stok' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'foto_produk_url',
    ];

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (! filled($search)) {
            return $query;
        }

        return $query->where(function (Builder $innerQuery) use ($search) {
            $innerQuery
                ->where('nama', 'like', "%{$search}%")
                ->orWhere('deskripsi_produk', 'like', "%{$search}%")
                ->orWhere('id', 'like', "%{$search}%");
        });
    }

    public function getFotoProdukUrlAttribute(): ?string
    {
        if (! $this->foto_produk) {
            return null;
        }

        return Storage::url($this->foto_produk);
    }
}
