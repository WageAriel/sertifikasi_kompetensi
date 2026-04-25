<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_displays_produk_catalog(): void
    {
        Product::factory()->count(3)->create();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->has('produk.data', 3)
            ->has('produk.links')
        );
    }

    public function test_public_product_detail_page_is_displayed(): void
    {
        $produk = Product::factory()->create([
            'nama' => 'Monitor 27 Inch',
        ]);

        $response = $this->get(route('produk.detail', $produk));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('ProductPublicDetail')
            ->where('product.nama', 'Monitor 27 Inch')
        );
    }

    public function test_products_index_is_displayed_with_pagination(): void
    {
        Product::factory()->count(12)->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('products.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Products/Index')
            ->has('products.data', 10)
            ->where('filters.sort', 'created_at')
            ->where('filters.direction', 'desc')
        );
    }

    public function test_product_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('products.store'), [
            'nama' => 'Laptop Gaming',
            'deskripsi_produk' => 'Laptop performa tinggi untuk editing dan gaming.',
            'harga' => 12500000,
            'stok' => 8,
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('produk', [
            'nama' => 'Laptop Gaming',
            'stok' => 8,
        ]);
    }

    public function test_product_validation_requires_numeric_price_and_positive_stock(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('products.create'))
            ->post(route('products.store'), [
                'nama' => 'Produk Tidak Valid',
                'deskripsi_produk' => 'Deskripsi.',
                'harga' => 'bukan-angka',
                'stok' => 0,
            ]);

        $response
            ->assertSessionHasErrors(['harga', 'stok'])
            ->assertRedirect(route('products.create'));
    }

    public function test_product_can_be_updated(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->put(route('products.update', $product), [
            'nama' => 'Nama Baru Produk',
            'deskripsi_produk' => 'Deskripsi diperbarui',
            'harga' => 750000,
            'stok' => 15,
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('produk', [
            'id' => $product->id,
            'nama' => 'Nama Baru Produk',
            'stok' => 15,
        ]);
    }

    public function test_product_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->delete(route('products.destroy', $product));

        $response->assertRedirect(route('products.index'));
    $this->assertDatabaseMissing('produk', ['id' => $product->id]);
    }

    public function test_products_can_be_exported_to_xlsx_and_pdf(): void
    {
        $user = User::factory()->create();
        Product::factory()->count(3)->create();

        $xlsxResponse = $this->actingAs($user)->get(route('products.export.xlsx'));
        $xlsxResponse->assertOk();
        $this->assertStringContainsString(
            '.xlsx',
            $xlsxResponse->headers->get('content-disposition', '')
        );

        $pdfResponse = $this->actingAs($user)->get(route('products.export.pdf'));
        $pdfResponse->assertOk();
        $this->assertStringContainsString(
            'application/pdf',
            $pdfResponse->headers->get('content-type', '')
        );
    }

    public function test_product_photo_can_be_uploaded_and_replaced(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $createResponse = $this->actingAs($user)->post(route('products.store'), [
            'nama' => 'Produk Dengan Foto',
            'deskripsi_produk' => 'Deskripsi produk dengan foto.',
            'foto_produk' => UploadedFile::fake()->image('awal.jpg'),
            'harga' => 150000,
            'stok' => 10,
        ]);

        $createResponse->assertRedirect(route('products.index'));

        $produk = Product::query()->latest('id')->firstOrFail();
        $this->assertNotNull($produk->foto_produk);
        Storage::disk('public')->assertExists($produk->foto_produk);

        $fotoLama = $produk->foto_produk;

        $updateResponse = $this->actingAs($user)->put(route('products.update', $produk), [
            'nama' => 'Produk Dengan Foto Update',
            'deskripsi_produk' => 'Deskripsi baru.',
            'foto_produk' => UploadedFile::fake()->image('baru.png'),
            'harga' => 170000,
            'stok' => 8,
        ]);

        $updateResponse->assertRedirect(route('products.index'));

        $produk->refresh();
        $this->assertNotNull($produk->foto_produk);
        $this->assertNotSame($fotoLama, $produk->foto_produk);
        Storage::disk('public')->assertMissing($fotoLama);
        Storage::disk('public')->assertExists($produk->foto_produk);

        $deleteResponse = $this->actingAs($user)->delete(route('products.destroy', $produk));

        $deleteResponse->assertRedirect(route('products.index'));
        Storage::disk('public')->assertMissing($produk->foto_produk);
    }
}
