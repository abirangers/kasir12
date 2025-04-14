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
import { Textarea } from '@/components/ui/textarea';
import { useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pelanggan',
        href: route('pelanggans.index'),
    },
    {
        title: 'Edit Pelanggan',
        href: '#',
    },
];

const props = defineProps<{
    pelanggan: {
        id: number;
        nama: string;
        alamat: string;
        no_hp: string;
    }
}>();

// Form state dengan data yang sudah ada
const form = useForm({
    nama: props.pelanggan.nama,
    alamat: props.pelanggan.alamat,
    no_hp: props.pelanggan.no_hp,
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

// Submit handler
const submit = () => {
    form.put(route('pelanggans.update', props.pelanggan.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Pelanggan berhasil diperbarui', {
                description: `Data pelanggan "${form.nama}" telah diperbarui`,
            });
            router.visit(route('pelanggans.index'));
        },
    });
};
</script>

<template>
    <Head title="Edit Pelanggan" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold tracking-tight">Edit Pelanggan</h2>
            </div>

            <div class="rounded-lg border p-6">
                <Form class="space-y-6" @submit="submit">
                    <!-- Nama Pelanggan -->
                    <FormField
                        name="nama"
                        :error="form.errors.nama"
                    >
                        <FormItem>
                            <FormLabel>Nama Pelanggan</FormLabel>
                            <FormControl>
                                <Input 
                                    type="text" 
                                    placeholder="Masukkan nama pelanggan" 
                                    v-model="form.nama"
                                    required
                                    maxlength="255"
                                />
                            </FormControl>
                            <FormMessage>{{ form.errors.nama }}</FormMessage>
                            <FormDescription>
                                Masukkan nama lengkap pelanggan
                            </FormDescription>
                        </FormItem>
                    </FormField>

                    <!-- Alamat Pelanggan -->
                    <FormField
                        name="alamat"
                        :error="form.errors.alamat"
                    >
                        <FormItem>
                            <FormLabel>Alamat</FormLabel>
                            <FormControl>
                                <Textarea 
                                    placeholder="Masukkan alamat lengkap" 
                                    v-model="form.alamat"
                                    required
                                    rows="3"
                                />
                            </FormControl>
                            <FormMessage>{{ form.errors.alamat }}</FormMessage>
                            <FormDescription>
                                Masukkan alamat lengkap pelanggan
                            </FormDescription>
                        </FormItem>
                    </FormField>

                    <!-- Nomor Telepon Pelanggan -->
                    <FormField
                        name="no_hp"
                        :error="form.errors.no_hp"
                    >
                        <FormItem>
                            <FormLabel>Nomor Telepon</FormLabel>
                            <FormControl>
                                <Input 
                                    type="text" 
                                    placeholder="Contoh: 08123456789" 
                                    v-model="form.no_hp"
                                    required
                                    maxlength="15"
                                />
                            </FormControl>
                            <FormMessage>{{ form.errors.no_hp }}</FormMessage>
                            <FormDescription>
                                Masukkan nomor telepon aktif pelanggan
                            </FormDescription>
                        </FormItem>
                    </FormField>

                    <!-- Tombol Submit dan Cancel -->
                    <div class="flex items-center justify-end gap-4">
                        <Button
                            type="button"
                            variant="outline"
                            @click="router.get(route('pelanggans.index'))"
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