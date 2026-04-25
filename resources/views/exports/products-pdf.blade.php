<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Produk</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111827;
        }

        h1 {
            margin: 0 0 8px;
            font-size: 18px;
        }

        .meta {
            margin-bottom: 14px;
            color: #4b5563;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #d1d5db;
            padding: 8px;
            vertical-align: top;
        }

        th {
            background: #f3f4f6;
            text-align: left;
        }

        /* Tambahan style untuk gambar agar rapi di PDF */
        .img-preview {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <h1>Laporan Inventaris Produk</h1>
    <p class="meta">
        Dicetak: {{ now()->format('d-m-Y H:i') }}<br>
        Kata kunci: {{ $filters['search'] ?? '-' }}
    </p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Foto</th> <th>Nama Produk</th>
                <th>Deskripsi</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Dibuat</th>
                <th>Diperbarui</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    
                    <td style="text-align: center;">
                        @if ($product->foto_produk)
                            {{-- Gunakan public_path() agar dibaca dari file system lokal oleh DomPDF --}}
                            <img src="{{ public_path('storage/' . $product->foto_produk) }}" class="img-preview" alt="Foto">
                        @else
                            <span style="color: #9ca3af; font-size: 10px;">Tidak ada foto</span>
                        @endif
                    </td>

                    <td>{{ $product->nama }}</td>
                    <td>{{ $product->deskripsi_produk }}</td>
                    <td>Rp {{ number_format((float) $product->harga, 2, ',', '.') }}</td>
                    <td>{{ $product->stok }}</td>
                    <td>{{ optional($product->created_at)->format('d-m-Y H:i') }}</td>
                    <td>{{ optional($product->updated_at)->format('d-m-Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">Tidak ada data produk.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>