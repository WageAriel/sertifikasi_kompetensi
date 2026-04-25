<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, reactive } from 'vue';

const props = defineProps({
    products: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        required: true,
    },
});

const page = usePage();

const query = reactive({
    search: props.filters.search ?? '',
    sort: props.filters.sort ?? 'created_at',
    direction: props.filters.direction ?? 'desc',
});

const sortIndicator = (column) => {
    if (query.sort !== column) {
        return '↕';
    }

    return query.direction === 'asc' ? '↑' : '↓';
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 2,
    }).format(Number(value));
};

const applyQuery = () => {
    router.get(route('products.index'), query, {
        preserveState: true,
        replace: true,
    });
};

const sortBy = (column) => {
    if (query.sort === column) {
        query.direction = query.direction === 'asc' ? 'desc' : 'asc';
    } else {
        query.sort = column;
        query.direction = 'asc';
    }

    applyQuery();
};

const resetSearch = () => {
    query.search = '';
    applyQuery();
};

const deleteProduct = (productId, productName) => {
    if (!window.confirm(`Yakin ingin menghapus produk \"${productName}\"?`)) {
        return;
    }

    router.delete(route('products.destroy', productId), {
        preserveScroll: true,
    });
};

const exportParams = computed(() => ({
    search: query.search,
    sort: query.sort,
    direction: query.direction,
}));
</script>

<template>
    <Head title="Daftar Produk" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Inventaris Produk</h2>
                <div class="flex items-center gap-2">
                    
                    <Link
                        :href="route('products.export.pdf', exportParams)"
                        class="inline-flex items-center rounded-md border border-rose-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-rose-700 transition hover:bg-rose-50"
                    >
                        Export PDF
                    </Link>
                    <Link
                        :href="route('products.create')"
                        class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700"
                    >
                        Tambah Produk
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
                <div
                    v-if="page.props.flash?.success"
                    class="rounded-md border border-green-200 bg-green-50 p-3 text-sm text-green-700"
                >
                    {{ page.props.flash.success }}
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 sm:p-6">
                    <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                        <div class="w-full md:max-w-lg">
                            <label for="search" class="block text-sm font-medium text-gray-700">Cari Produk</label>
                            <input
                                id="search"
                                v-model="query.search"
                                type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Cari nama, deskripsi produk, atau ID..."
                                @keyup.enter="applyQuery"
                            />
                        </div>
                        <div class="flex gap-2">
                            <PrimaryButton @click="applyQuery">Cari</PrimaryButton>
                            <button
                                type="button"
                                class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 transition hover:bg-gray-50"
                                @click="resetSearch"
                            >
                                Reset
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        <button type="button" class="hover:text-gray-700" @click="sortBy('id')">
                                            ID {{ sortIndicator('id') }}
                                        </button>
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Foto
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        <button type="button" class="hover:text-gray-700" @click="sortBy('nama')">
                                            Nama Produk {{ sortIndicator('nama') }}
                                        </button>
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        <button type="button" class="hover:text-gray-700" @click="sortBy('harga')">
                                            Harga {{ sortIndicator('harga') }}
                                        </button>
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        <button type="button" class="hover:text-gray-700" @click="sortBy('stok')">
                                            Stok {{ sortIndicator('stok') }}
                                        </button>
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        <button type="button" class="hover:text-gray-700" @click="sortBy('updated_at')">
                                            Diperbarui {{ sortIndicator('updated_at') }}
                                        </button>
                                    </th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-if="products.data.length === 0">
                                    <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">
                                        Belum ada produk.
                                    </td>
                                </tr>
                                <tr v-for="product in products.data" :key="product.id" class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ product.id }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        <img
                                            v-if="product.foto_produk_url"
                                            :src="product.foto_produk_url"
                                            :alt="product.nama"
                                            class="h-12 w-16 rounded border border-gray-200 object-cover"
                                        >
                                        <span v-else class="text-xs text-gray-400">-</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ product.nama }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ formatCurrency(product.harga) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ product.stok }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">
                                        {{ new Date(product.updated_at).toLocaleString('id-ID') }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="inline-flex gap-2">
                                            <Link
                                                :href="route('products.show', product.id)"
                                                class="inline-flex items-center rounded-md border border-gray-300 px-3 py-1.5 text-xs font-semibold uppercase tracking-widest text-gray-700 hover:bg-gray-50"
                                            >
                                                Detail
                                            </Link>
                                            <Link
                                                :href="route('products.edit', product.id)"
                                                class="inline-flex items-center rounded-md border border-indigo-300 px-3 py-1.5 text-xs font-semibold uppercase tracking-widest text-indigo-700 hover:bg-indigo-50"
                                            >
                                                Edit
                                            </Link>
                                            <DangerButton @click="deleteProduct(product.id, product.nama)">
                                                Hapus
                                            </DangerButton>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="border-t bg-gray-50 px-4 py-3 sm:px-6">
                        <div class="flex flex-wrap gap-2">
                            <Link
                                v-for="link in products.links"
                                :key="`${link.label}-${link.url}`"
                                :href="link.url || '#'"
                                class="rounded-md border px-3 py-1.5 text-sm"
                                :class="[
                                    link.active
                                        ? 'border-indigo-400 bg-indigo-50 text-indigo-700'
                                        : 'border-gray-300 bg-white text-gray-600 hover:bg-gray-50',
                                    !link.url ? 'pointer-events-none opacity-50' : '',
                                ]"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
