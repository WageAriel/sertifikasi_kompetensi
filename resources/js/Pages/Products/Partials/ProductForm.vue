<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { computed, onBeforeUnmount, ref } from 'vue';

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    existingPhotoUrl: {
        type: String,
        default: null,
    },
    submitLabel: {
        type: String,
        default: 'Simpan',
    },
});

const emit = defineEmits(['submit']);

const selectedPhotoUrl = ref(null);

const previewPhotoUrl = computed(() => selectedPhotoUrl.value ?? props.existingPhotoUrl ?? null);

const onFotoChange = (event) => {
    const file = event.target.files?.[0] ?? null;
    props.form.foto_produk = file;

    if (selectedPhotoUrl.value) {
        URL.revokeObjectURL(selectedPhotoUrl.value);
        selectedPhotoUrl.value = null;
    }

    if (file) {
        selectedPhotoUrl.value = URL.createObjectURL(file);
    }
};

onBeforeUnmount(() => {
    if (selectedPhotoUrl.value) {
        URL.revokeObjectURL(selectedPhotoUrl.value);
    }
});
</script>

<template>
    <form @submit.prevent="emit('submit')" class="space-y-6">
        <div>
            <InputLabel for="nama" value="Nama Produk" />
            <TextInput
                id="nama"
                type="text"
                class="mt-1 block w-full"
                v-model="form.nama"
                required
                autofocus
            />
            <InputError class="mt-2" :message="form.errors.nama" />
        </div>

        <div>
            <InputLabel for="deskripsi_produk" value="Deskripsi Produk" />
            <textarea
                id="deskripsi_produk"
                v-model="form.deskripsi_produk"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                rows="4"
                required
            />
            <InputError class="mt-2" :message="form.errors.deskripsi_produk" />
        </div>

        <div>
            <InputLabel for="foto_produk" value="Foto Produk" />
            <input
                id="foto_produk"
                type="file"
                accept="image/png,image/jpeg,image/jpg,image/webp"
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                @change="onFotoChange"
            />
            <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, WEBP. Maksimal 2MB.</p>
            <InputError class="mt-2" :message="form.errors.foto_produk" />

            <div v-if="previewPhotoUrl" class="mt-3">
                <img
                    :src="previewPhotoUrl"
                    alt="Preview foto produk"
                    class="h-32 w-48 rounded-md border border-gray-200 object-cover"
                />
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <InputLabel for="harga" value="Harga" />
                <TextInput
                    id="harga"
                    type="number"
                    step="0.01"
                    min="0"
                    class="mt-1 block w-full"
                    v-model="form.harga"
                    required
                />
                <InputError class="mt-2" :message="form.errors.harga" />
            </div>

            <div>
                <InputLabel for="stok" value="Stok Barang" />
                <TextInput
                    id="stok"
                    type="number"
                    min="1"
                    class="mt-1 block w-full"
                    v-model="form.stok"
                    required
                />
                <InputError class="mt-2" :message="form.errors.stok" />
            </div>
        </div>

        <div class="flex justify-end">
            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                {{ submitLabel }}
            </PrimaryButton>
        </div>
    </form>
</template>
