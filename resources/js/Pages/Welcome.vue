<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    produk: {
        type: Object,
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

const keyword = ref(props.search ?? '');

const cariProduk = () => {
    router.get('/', {
        search: keyword.value,
    }, {
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 2,
    }).format(Number(value));
};
</script>

<template>
    <Head title="Katalog Produk" />

    <div class="min-h-screen bg-slate-50">
        <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/90 backdrop-blur">
            <div class="mx-auto flex w-full max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">Toko Elektronik Palur</p>
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

        <main class="mx-auto w-full max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
            <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
                <form @submit.prevent="cariProduk" class="grid gap-3 md:grid-cols-[1fr_auto]">
                    <input
                        v-model="keyword"
                        type="text"
                        placeholder="Cari nama atau deskripsi produk..."
                        class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                    <button
                        type="submit"
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
                    >
                        Cari
                    </button>
                </form>
            </section>

            <section>
                <div v-if="produk.data.length === 0" class="rounded-xl border border-dashed border-slate-300 bg-white p-10 text-center">
                    <p class="text-sm text-slate-500">Belum ada produk yang ditampilkan.</p>
                </div>

                <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <Link
                        v-for="item in produk.data"
                        :key="item.id"
                        :href="route('produk.detail', item.id)"
                        class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                    >
                        <div class="aspect-video bg-slate-100 p-2">
                            <img
                                v-if="item.foto_produk_url"
                                :src="item.foto_produk_url"
                                :alt="item.nama"
                                class="h-full w-full object-contain"
                            />
                            <div v-else class="flex h-full items-center justify-center text-xs text-slate-500">
                                Tidak ada foto
                            </div>
                        </div>

                        <div class="space-y-2 p-4">
                            <h2 class="line-clamp-1 text-sm font-semibold text-slate-900">{{ item.nama }}</h2>
                            <!-- <p class="line-clamp-2 text-xs text-slate-600">{{ item.deskripsi_produk }}</p> -->
                            <div class="flex items-center justify-between pt-2">
                                <p class="text-sm font-bold text-indigo-700">{{ formatCurrency(item.harga) }}</p>
                                <span class="rounded-full bg-slate-100 px-2 py-1 text-xs text-slate-600">Stok: {{ item.stok }}</span>
                            </div>
                        </div>
                    </Link>
                </div>

                <div v-if="produk.links?.length > 3" class="mt-6 flex flex-wrap gap-2">
                    <Link
                        v-for="link in produk.links"
                        :key="`${link.label}-${link.url}`"
                        :href="link.url || '#'"
                        class="rounded-md border px-3 py-1.5 text-sm"
                        :class="[
                            link.active
                                ? 'border-indigo-400 bg-indigo-50 text-indigo-700'
                                : 'border-slate-300 bg-white text-slate-600 hover:bg-slate-50',
                            !link.url ? 'pointer-events-none opacity-50' : '',
                        ]"
                        v-html="link.label"
                    />
                </div>
            </section>
        </main>

        <footer class="border-t border-slate-200 bg-white py-4 text-center text-xs text-slate-500">
            Laravel v{{ laravelVersion }} • PHP v{{ phpVersion }}
        </footer>
    </div>
</template>
