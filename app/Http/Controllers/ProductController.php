<?php

namespace App\Http\Controllers;

use App\Exports\ProductsExport;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function publicShow(Product $product): Response
    {
        return Inertia::render('ProductPublicDetail', [
            'product' => $product,
        ]);
    }

    public function index(Request $request): Response
    {
        $filters = $this->validatedFilters($request);

        $products = Product::query()
            ->search($filters['search'])
            ->orderBy($filters['sort'], $filters['direction'])
            ->paginate(10)
            ->appends($request->query());

        return Inertia::render('Products/Index', [
            'products' => $products,
            'filters' => $filters,
        ]);
    }

    public function show(Product $product): Response
    {
        return Inertia::render('Products/Show', [
            'product' => $product,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Products/Create');
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $payload = $request->validated();

        if ($request->hasFile('foto_produk')) {
            $payload['foto_produk'] = $request->file('foto_produk')->store('produk', 'public');
        }

        Product::create($payload);

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product): Response
    {
        return Inertia::render('Products/Edit', [
            'product' => $product,
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
{
    $payload = $request->validated();

    if ($request->hasFile('foto_produk')) {
        if ($product->foto_produk) {
            Storage::disk('public')->delete($product->foto_produk);
        }
        $payload['foto_produk'] = $request->file('foto_produk')->store('produk', 'public');
    } else {
        unset($payload['foto_produk']);
    }

    $product->update($payload);

    return redirect()
        ->route('products.index')
        ->with('success', 'Produk berhasil diperbarui.');
}

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->foto_produk) {
            Storage::disk('public')->delete($product->foto_produk);
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }


    public function exportPdf(Request $request)
    {
        $filters = $this->validatedFilters($request);

        $products = Product::query()
            ->search($filters['search'])
            ->get();

        $pdf = Pdf::loadView('exports.products-pdf', [
            'products' => $products,
            'filters' => $filters,
        ]);

        return $pdf->download('produk-' . now()->format('Ymd-His') . '.pdf');
    }

    /**
     * @return array{search: ?string, sort: string, direction: string}
     */
    private function validatedFilters(Request $request): array
    {
        $allowedSorts = ['id', 'nama', 'harga', 'stok', 'created_at', 'updated_at'];
        $allowedDirections = ['asc', 'desc'];

        $search = $request->string('search')->toString();
        $sort = $request->string('sort')->toString();
        $direction = $request->string('direction')->toString();

        return [
            'search' => $search !== '' ? $search : null,
            'sort' => in_array($sort, $allowedSorts, true) ? $sort : 'created_at',
            'direction' => in_array($direction, $allowedDirections, true) ? $direction : 'desc',
        ];
    }
}
