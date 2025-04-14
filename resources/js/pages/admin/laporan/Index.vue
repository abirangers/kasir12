<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { 
    Table, 
    TableBody, 
    TableCell, 
    TableHead, 
    TableHeader, 
    TableRow 
} from '@/components/ui/table';
import { 
    Dialog, 
    DialogContent, 
    DialogHeader, 
    DialogTitle, 
    DialogDescription, 
    DialogFooter
} from '@/components/ui/dialog';
import { 
    Printer, 
    Eye
} from 'lucide-vue-next';
import { toast } from 'vue-sonner';

// Definisi tipe untuk detail penjualan
interface DetailPenjualan {
    id: number;
    tanggal_penjualan: string;
    total_harga: number;
    kasir: string;
    pelanggan: {
        id: number;
        nama: string;
    };
    detail_penjualan: Array<{
        produk: {
            id: number;
            nama: string;
            harga: number;
        };
        jumlah_produk: number;
        subtotal: number;
    }>;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Laporan',
        href: route('laporan.index'),
    },
];

// Menerima data dari controller
const props = defineProps<{
    penjualan: Array<{
        id: number;
        tanggal_penjualan: string;
        total_harga: number;
        kasir: string;
        pelanggan: {
            id: number;
            nama: string;
        };
    }>;
}>();

// State untuk dialog detail
const showDetailDialog = ref(false);
const selectedPenjualan = ref<null | number>(null);
const detailPenjualan = ref<DetailPenjualan | null>(null);

// Handler untuk detail
const viewDetail = (id: number) => {
    selectedPenjualan.value = id;
    router.get(route('laporan.show', id), {}, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: (page: any) => {
            detailPenjualan.value = page.props.detail as DetailPenjualan;
            showDetailDialog.value = true;
        },
        onError: () => {
            toast.error('Gagal memuat detail penjualan');
        }
    });
};

// Handler untuk print
const printReport = () => {
    window.open(route('laporan.print'), '_blank');
};

// Handler untuk print detail
const printDetail = (id: number) => {
    window.open(route('laporan.print_detail', id), '_blank');
};

// Format tanggal
const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

// Format currency
const formatRupiah = (amount: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
};
</script>

<template>
    <Head title="Laporan Penjualan" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col space-y-4 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold tracking-tight">Laporan Penjualan</h1>
                <Button @click="printReport" variant="outline" class="gap-2">
                    <Printer class="size-4" />
                    Cetak Laporan
                </Button>
            </div>

            <!-- Tabel Penjualan -->
            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>No.</TableHead>
                            <TableHead>Tanggal</TableHead>
                            <TableHead>Pelanggan</TableHead>
                            <TableHead class="text-right">Total</TableHead>
                            <TableHead>Kasir</TableHead>
                            <TableHead class="text-center">Aksi</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="(item, index) in props.penjualan" :key="item.id">
                            <TableCell>{{ index + 1 }}</TableCell>
                            <TableCell>{{ formatDate(item.tanggal_penjualan) }}</TableCell>
                            <TableCell>{{ item.pelanggan.nama }}</TableCell>
                            <TableCell class="text-right font-medium">{{ formatRupiah(item.total_harga) }}</TableCell>
                            <TableCell>{{ item.kasir }}</TableCell>
                            <TableCell>
                                <div class="flex justify-center gap-2">
                                    <Button @click="viewDetail(item.id)" size="sm" variant="ghost" class="h-8 w-8 p-0">
                                        <Eye class="size-4" />
                                    </Button>
                                    <Button @click="printDetail(item.id)" size="sm" variant="ghost" class="h-8 w-8 p-0">
                                        <Printer class="size-4" />
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                        
                        <TableRow v-if="props.penjualan.length === 0">
                            <TableCell colspan="6" class="text-center py-10 text-muted-foreground">
                                Tidak ada data penjualan yang ditemukan
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </div>
    </AppLayout>

    <!-- Dialog Detail Penjualan -->
    <Dialog v-model:open="showDetailDialog">
        <DialogContent class="sm:max-w-[600px]">
            <DialogHeader>
                <DialogTitle>Detail Penjualan #{{ detailPenjualan?.id }}</DialogTitle>
                <DialogDescription>
                    Tanggal: {{ detailPenjualan ? formatDate(detailPenjualan.tanggal_penjualan) : '-' }}
                </DialogDescription>
            </DialogHeader>

            <div v-if="detailPenjualan" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-muted-foreground">Pelanggan:</p>
                        <p class="font-medium">{{ detailPenjualan.pelanggan.nama }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-muted-foreground">Kasir:</p>
                        <p class="font-medium">{{ detailPenjualan.kasir }}</p>
                    </div>
                </div>

                <div class="border rounded-lg overflow-hidden">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Produk</TableHead>
                                <TableHead class="text-right">Harga</TableHead>
                                <TableHead class="text-center">Qty</TableHead>
                                <TableHead class="text-right">Subtotal</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="item in detailPenjualan.detail_penjualan" :key="item.produk.id">
                                <TableCell>{{ item.produk.nama }}</TableCell>
                                <TableCell class="text-right">{{ formatRupiah(item.produk.harga) }}</TableCell>
                                <TableCell class="text-center">{{ item.jumlah_produk }}</TableCell>
                                <TableCell class="text-right">{{ formatRupiah(item.subtotal) }}</TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <div class="flex justify-between items-center border-t pt-4">
                    <p class="font-semibold">Total</p>
                    <p class="font-bold text-xl">{{ formatRupiah(detailPenjualan.total_harga) }}</p>
                </div>
            </div>

            <DialogFooter>
                <Button type="button" variant="outline" @click="showDetailDialog = false">Tutup</Button>
                <Button type="button" @click="printDetail(detailPenjualan?.id as number)">
                    <Printer class="mr-2 size-4" />
                    Cetak
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
