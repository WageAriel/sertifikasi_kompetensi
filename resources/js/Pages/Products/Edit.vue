<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ProductForm from '@/Pages/Products/Partials/ProductForm.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    nama: props.product.nama,
    deskripsi_produk: props.product.deskripsi_produk,
    foto_produk: null,
    harga: props.product.harga,
    stok: props.product.stok,
    _method: 'put', // <-- Tambahkan di sini
});

const submit = () => {
    // Parameter kedua cukup berisi opsi forceFormData saja
    form.post(route('products.update', props.product.id), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head :title="`Edit ${product.nama}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Produk</h2>
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
                        <ProductForm
                            :form="form"
                            :existing-photo-url="product.foto_produk_url"
                            submit-label="Update Produk"
                            @submit="submit"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
