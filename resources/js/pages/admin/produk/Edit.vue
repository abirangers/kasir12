<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { watch } from 'vue';
import { Button } from '@/components/ui/button';
import { 
    Form, 
    FormControl, 
    FormDescription, 
    FormField, 
    FormItem, 
    FormLabel, 
    FormMessage 
} from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Produk',
        href: route('produk.index'),
    },
    {
        title: 'Edit Produk',
        href: '#',
    },
];

const props = defineProps<{
    produk: {
        id: number;
        nama: string;
        harga: number;
        stok: number;
    }
}>();

// Form state dengan data yang sudah ada
const form = useForm({
    nama: props.produk.nama,
    harga: props.produk.harga.toString(),
    stok: props.produk.stok.toString(),
});

// Handle validation errors
watch(() => form.errors, (newErrors) => {
    if (Object.keys(newErrors).length > 0) {
        const errorMessages = Object.values(newErrors).join(', ');
        toast.error('Validasi gagal', {
            description: errorMessages,
        });
    }
}, { deep: true });

// Submit handler untuk update
const submit = () => {
    form.put(route('produk.update', props.produk.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Produk berhasil diperbarui', {
                description: `Produk "${form.nama}" telah diperbarui`,
            });
            router.visit(route('produk.index'));
        },
    });
};

// Handler untuk form submit
const handleSubmit = (e) => {
    e.preventDefault();
    submit();
};
</script>

<template>
    <Head title="Edit Produk" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold tracking-tight">Edit Produk</h2>
            </div>

            <div class="rounded-lg border p-6">
                <Form class="space-y-6" @submit="handleSubmit">
                    <!-- Nama Produk -->
                    <FormField
                        name="nama"
                        :error="form.errors.nama"
                    >
                        <FormItem>
                            <FormLabel>Nama Produk</FormLabel>
                            <FormControl>
                                <Input 
                                    type="text" 
                                    placeholder="Masukkan nama produk" 
                                    v-model="form.nama"
                                    required
                                    maxlength="255"
                                />
                            </FormControl>
                            <FormMessage>{{ form.errors.nama }}</FormMessage>
                            <FormDescription>
                                Masukkan nama produk yang dijual
                            </FormDescription>
                        </FormItem>
                    </FormField>

                    <!-- Harga Produk -->
                    <FormField
                        name="harga"
                        :error="form.errors.harga"
                    >
                        <FormItem>
                            <FormLabel>Harga (Rp)</FormLabel>
                            <FormControl>
                                <Input 
                                    type="number" 
                                    placeholder="Masukkan harga produk" 
                                    v-model="form.harga"
                                    min="0"
                                    step="0.01"
                                    required
                                />
                            </FormControl>
                            <FormMessage>{{ form.errors.harga }}</FormMessage>
                            <FormDescription>
                                Masukkan harga produk dalam Rupiah
                            </FormDescription>
                        </FormItem>
                    </FormField>

                    <!-- Stok Produk -->
                    <FormField
                        name="stok"
                        :error="form.errors.stok"
                    >
                        <FormItem>
                            <FormLabel>Stok</FormLabel>
                            <FormControl>
                                <Input 
                                    type="number" 
                                    placeholder="Masukkan jumlah stok" 
                                    v-model="form.stok"
                                    min="0"
                                    step="1"
                                    required
                                />
                            </FormControl>
                            <FormMessage>{{ form.errors.stok }}</FormMessage>
                            <FormDescription>
                                Masukkan jumlah stok produk yang tersedia
                            </FormDescription>
                        </FormItem>
                    </FormField>

                    <!-- Tombol Submit dan Cancel -->
                    <div class="flex items-center justify-end gap-4">
                        <Button
                            type="button"
                            variant="outline"
                            @click="router.get(route('produk.index'))"
                        >
                            Batal
                        </Button>
                        <Button
                            type="submit"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                        </Button>
                    </div>
                </Form>
            </div>
        </div>
    </AppLayout>
</template> 