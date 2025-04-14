<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
    Table, 
    TableBody, 
    TableCaption, 
    TableCell, 
    TableHead, 
    TableHeader, 
    TableRow 
} from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { 
    Dialog, 
    DialogContent, 
    DialogHeader, 
    DialogTitle, 
    DialogDescription, 
    DialogFooter
} from '@/components/ui/dialog';
import { 
    DropdownMenu, 
    DropdownMenuContent, 
    DropdownMenuItem, 
    DropdownMenuTrigger 
} from '@/components/ui/dropdown-menu';
import { PlusCircle, Edit, Trash, MoreHorizontal } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Produk',
        href: route('produks.index'),
    },
];

// Menggunakan data dari controller
const props = defineProps<{
    produks: Array<{
        id: number;
        nama: string;
        harga: number;
        stok: number;
        created_at: string;
        updated_at: string;
    }>
}>();

// Status stok (untuk menentukan warna badge)
const getStokStatus = (stok: number): 'default' | 'destructive' | 'secondary' => {
    if (stok <= 5) return 'destructive';
    if (stok <= 10) return 'secondary';
    return 'default';
};

// Format harga dalam rupiah
const formatRupiah = (angka: number): string => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(angka);
};

// Dialog konfirmasi hapus
const showDeleteDialog = ref(false);
const produkToDelete = ref<{id: number, nama: string} | null>(null);

const confirmDelete = (produk: {id: number, nama: string}) => {
    produkToDelete.value = produk;
    showDeleteDialog.value = true;
};

const hapusProduk = () => {
    if (produkToDelete.value) {
        router.delete(route('produks.destroy', produkToDelete.value.id), {
            onSuccess: () => {
                showDeleteDialog.value = false;
                produkToDelete.value = null;
            }
        });
    }
};
</script>

<template>
    <Head title="Produk" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header dengan tombol tambah -->
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold tracking-tight">Daftar Produk</h2>
                <Button class="flex items-center gap-2" as-child>
                    <a :href="route('produks.create')">
                        <PlusCircle class="size-4" />
                        <span>Tambah Produk</span>
                    </a>
                </Button>
            </div>

            <!-- Tabel Produk -->
            <div class="rounded-lg border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-12">No</TableHead>
                            <TableHead>Nama Produk</TableHead>
                            <TableHead>Harga</TableHead>
                            <TableHead>Stok</TableHead>
                            <TableHead class="w-[150px]">Aksi</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="(produk, index) in props.produks" :key="produk.id">
                            <TableCell class="font-medium">{{ index + 1 }}</TableCell>
                            <TableCell>{{ produk.nama }}</TableCell>
                            <TableCell>{{ formatRupiah(produk.harga) }}</TableCell>
                            <TableCell>
                                <Badge :variant="getStokStatus(produk.stok)">
                                    {{ produk.stok }}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <DropdownMenu>
                                    <DropdownMenuTrigger asChild>
                                        <Button variant="ghost" size="icon">
                                            <MoreHorizontal class="size-4" />
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end" class="w-40">
                                        <DropdownMenuItem asChild>
                                            <a :href="route('produks.edit', produk.id)" class="flex items-center gap-2">
                                                <Edit class="size-4" />
                                                <span>Edit</span>
                                            </a>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem 
                                            class="flex items-center gap-2 text-destructive focus:text-destructive"
                                            @click="confirmDelete(produk)"
                                        >
                                            <Trash class="size-4" />
                                            <span>Hapus</span>
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="props.produks.length === 0">
                            <TableCell colspan="5" class="text-center py-10 text-muted-foreground">
                                Tidak ada data produk
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>

    <!-- Dialog Konfirmasi Hapus -->
    <Dialog v-model:open="showDeleteDialog">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>Konfirmasi Hapus</DialogTitle>
                <DialogDescription>
                    Apakah Anda yakin ingin menghapus produk "{{ produkToDelete?.nama }}"?
                    Tindakan ini tidak dapat dibatalkan.
                </DialogDescription>
            </DialogHeader>
            <DialogFooter class="flex justify-end gap-2 pt-4">
                <Button variant="outline" @click="showDeleteDialog = false">Batal</Button>
                <Button variant="destructive" @click="hapusProduk">Hapus</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
