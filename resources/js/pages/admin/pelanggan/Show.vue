<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { Edit, ArrowLeft, Trash, User, MapPin, Phone } from 'lucide-vue-next';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pelanggan',
        href: route('pelanggans.index'),
    },
    {
        title: 'Detail Pelanggan',
        href: '#',
    },
];

const props = defineProps<{
    pelanggan: {
        id: number;
        nama: string;
        alamat: string;
        no_hp: string;
        created_at: string;
        updated_at: string;
    }
}>();

// Dialog konfirmasi hapus
const showDeleteDialog = ref(false);

const hapusPelanggan = () => {
    router.delete(route('pelanggans.destroy', props.pelanggan.id), {
        onSuccess: () => {
            toast.success('Pelanggan berhasil dihapus');
            router.visit(route('pelanggans.index'));
        },
        onError: (error) => {
            toast.error(error.message || 'Gagal menghapus pelanggan');
        }
    });
};

// Format tanggal
const formatTanggal = (tanggal: string) => {
    return new Date(tanggal).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <Head title="Detail Pelanggan" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 p-4">
            <!-- Header dengan tombol kembali -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <Button variant="outline" size="icon" @click="router.visit(route('pelanggans.index'))">
                        <ArrowLeft class="size-4" />
                    </Button>
                    <h2 class="text-2xl font-bold tracking-tight">Detail Pelanggan</h2>
                </div>
                <div class="flex items-center gap-2">
                    <Button variant="outline" as-child>
                        <a :href="route('pelanggans.edit', props.pelanggan.id)" class="flex items-center gap-2">
                            <Edit class="size-4" />
                            <span>Edit</span>
                        </a>
                    </Button>
                    <Button variant="destructive" @click="showDeleteDialog = true" class="flex items-center gap-2">
                        <Trash class="size-4" />
                        <span>Hapus</span>
                    </Button>
                </div>
            </div>

            <!-- Detail Pelanggan -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <User class="size-5" />
                        {{ props.pelanggan.nama }}
                    </CardTitle>
                    <CardDescription>
                        ID Pelanggan: {{ props.pelanggan.id }}
                    </CardDescription>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-sm font-medium text-muted-foreground">Informasi Kontak</h3>
                                <Separator class="my-2" />
                                <div class="space-y-3">
                                    <div class="flex items-start gap-2">
                                        <MapPin class="size-4 mt-1 text-muted-foreground" />
                                        <div>
                                            <p class="font-medium">Alamat</p>
                                            <p class="text-muted-foreground whitespace-pre-line">{{ props.pelanggan.alamat }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <Phone class="size-4 text-muted-foreground" />
                                        <div>
                                            <p class="font-medium">Nomor Telepon</p>
                                            <p class="text-muted-foreground">{{ props.pelanggan.no_hp }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-muted-foreground">Informasi Sistem</h3>
                                <Separator class="my-2" />
                                <div class="space-y-3">
                                    <div>
                                        <p class="font-medium">Tanggal Dibuat</p>
                                        <p class="text-muted-foreground">{{ formatTanggal(props.pelanggan.created_at) }}</p>
                                    </div>
                                    <div>
                                        <p class="font-medium">Terakhir Diperbarui</p>
                                        <p class="text-muted-foreground">{{ formatTanggal(props.pelanggan.updated_at) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
                <CardFooter>
                    <p class="text-sm text-muted-foreground">
                        Data pelanggan ini digunakan dalam proses transaksi.
                    </p>
                </CardFooter>
            </Card>
        </div>
    </AppLayout>

    <!-- Dialog Konfirmasi Hapus -->
    <Dialog v-model:open="showDeleteDialog">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Konfirmasi Hapus</DialogTitle>
                <DialogDescription>
                    Apakah Anda yakin ingin menghapus pelanggan "{{ props.pelanggan.nama }}"?
                    Tindakan ini tidak dapat dibatalkan.
                </DialogDescription>
            </DialogHeader>
            <DialogFooter class="flex justify-end gap-2 pt-4">
                <Button variant="outline" @click="showDeleteDialog = false">Batal</Button>
                <Button variant="destructive" @click="hapusPelanggan">Hapus</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template> 