<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    product: {
        type: Object,
        required: true,
    },
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 2,
    }).format(Number(value));
};

const formatDateTime = (value) => {
    return new Intl.DateTimeFormat('id-ID', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(new Date(value));
};
</script>

<template>
    <Head :title="`Detail ${product.nama}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Produk</h2>
                <Link
                    :href="route('products.index')"
                    class="text-sm text-indigo-600 hover:text-indigo-800 font-medium"
                >
                    Kembali
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 space-y-4">
                        <div v-if="product.foto_produk_url">
                            <p class="text-sm text-gray-500 mb-2">Foto Produk</p>
                            <div class="w-full max-w-md rounded-md border border-gray-200 bg-gray-50 p-2">
                                <img
                                    :src="product.foto_produk_url"
                                    :alt="product.nama"
                                    class="h-52 w-full object-contain"
                                />
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">ID Produk</p>
                            <p class="font-medium">{{ product.id }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Nama Produk</p>
                            <p class="font-medium">{{ product.nama }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Deskripsi Produk</p>
                            <p class="font-medium whitespace-pre-line">{{ product.deskripsi_produk }}</p>
                        </div>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <p class="text-sm text-gray-500">Harga</p>
                                <p class="font-medium">{{ formatCurrency(product.harga) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Stok Barang</p>
                                <p class="font-medium">{{ product.stok }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <p class="text-sm text-gray-500">Tanggal Dibuat</p>
                                <p class="font-medium">{{ formatDateTime(product.created_at) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Tanggal Diperbarui</p>
                                <p class="font-medium">{{ formatDateTime(product.updated_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
