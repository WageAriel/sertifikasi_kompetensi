<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    product: {
        type: Object,
        required: true,
    },
    canLogin: {
        type: Boolean,
        default: false,
    }, 
    canRegister: {
        type: Boolean,
        default: false,
    },
   produk: {
        type: Array,
        required: true,
    },
    search: {
        type: String,
        default: '',
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
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
</script>

<template>
    <Head :title="`Detail ${product.nama}`" />

    <div class="min-h-screen bg-slate-50">
        <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/90 backdrop-blur">
            <div class="mx-auto flex w-full max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">Toko Elektronik</p>
                    <h1 class="text-lg font-bold text-slate-900">Daftar Produk</h1>
                </div>

                <div v-if="canLogin" class="flex items-center gap-3 text-sm">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="rounded-md border border-slate-300 px-3 py-2 font-medium text-slate-700 hover:bg-slate-100"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="rounded-md border border-slate-300 px-3 py-2 font-medium text-slate-700 hover:bg-slate-100"
                        >
                            Login
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="rounded-md bg-indigo-600 px-3 py-2 font-medium text-white hover:bg-indigo-700"
                        >
                            Register
                        </Link>
                    </template>
                </div>
            </div>
        </header>
        <main class="mx-auto w-full max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="mb-4">
                <Link
                    href="/"
                    class="inline-flex items-center rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100"
                >
                    ← Kembali ke katalog
                </Link>
            </div>

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="grid grid-cols-1 gap-0 lg:grid-cols-2">
                    <div class="flex min-h-80 items-center justify-center bg-slate-100 p-4">
                        <img
                            v-if="product.foto_produk_url"
                            :src="product.foto_produk_url"
                            :alt="product.nama"
                            class="max-h-[420px] w-full object-contain"
                        >
                        <div v-else class="text-sm text-slate-500">Tidak ada foto produk</div>
                    </div>

                    <div class="space-y-5 p-6">
                        <h1 class="text-2xl font-bold text-slate-900">{{ product.nama }}</h1>
                        <p class="text-sm leading-relaxed text-slate-600">{{ product.deskripsi_produk }}</p>

                        <div class="space-y-2 border-t border-slate-200 pt-4">
                            <p class="text-xl font-bold text-indigo-700">{{ formatCurrency(product.harga) }}</p>
                            <p class="text-sm text-slate-600">Stok tersedia: {{ product.stok }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
