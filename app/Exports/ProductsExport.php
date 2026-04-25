<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    /**
     * @param array{search: ?string, sort: string, direction: string} $filters
     */
    public function __construct(private readonly array $filters)
    {
    }

    /**
     * @return Builder<Product>
     */
    public function query(): Builder
    {
        return Product::query()
            ->search($this->filters['search'])
            ->orderBy($this->filters['sort'], $this->filters['direction']);
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'ID Produk',
            'Nama Produk',
            'Deskripsi Produk',
            'Harga',
            'Stok Barang',
            'Tanggal Dibuat',
            'Tanggal Diperbarui',
        ];
    }

    /**
     * @return array<int, string>
     */
    public function map($product): array
    {
        return [
            (string) $product->id,
            $product->nama,
            $product->deskripsi_produk,
            number_format((float) $product->harga, 2, '.', ''),
            (string) $product->stok,
            $product->created_at?->format('Y-m-d H:i:s') ?? '-',
            $product->updated_at?->format('Y-m-d H:i:s') ?? '-',
        ];
    }
}
