<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ProductForm from '@/Pages/Products/Partials/ProductForm.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    nama: '',
    deskripsi_produk: '',
    foto_produk: null,
    harga: '',
    stok: '',
});

const submit = () => {
    form.post(route('products.store'), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Tambah Produk" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Produk</h2>
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
                    <div class="p-6 text-gray-900">
                        <ProductForm :form="form" submit-label="Simpan Produk" @submit="submit" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
