<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { 
    Package2, 
    Users, 
    ReceiptText, 
    CircleDollarSign 
} from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const props = defineProps<{
    stats: {
        productCount: number;
        customerCount: number;
        transactionCount: number;
        totalSales: number;
    }
}>();

// Format angka rupiah
const formatRupiah = (amount: number): string => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <h1 class="text-2xl font-bold tracking-tight mb-6">Dashboard Admin</h1>
            
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <!-- Kartu Produk -->
                <div class="rounded-xl border border-border p-6 bg-background shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Total Produk</p>
                            <h3 class="text-2xl font-bold mt-1">{{ props.stats.productCount }}</h3>
                        </div>
                        <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center">
                            <Package2 class="h-6 w-6 text-primary" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="/admin/produks" class="text-sm text-primary hover:underline">Lihat semua produk</a>
                    </div>
                </div>

                <!-- Kartu Pelanggan -->
                <div class="rounded-xl border border-border p-6 bg-background shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Total Pelanggan</p>
                            <h3 class="text-2xl font-bold mt-1">{{ props.stats.customerCount }}</h3>
                        </div>
                        <div class="h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-950 flex items-center justify-center">
                            <Users class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="/admin/pelanggans" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Lihat semua pelanggan</a>
                    </div>
                </div>

                <!-- Kartu Transaksi -->
                <div class="rounded-xl border border-border p-6 bg-background shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Total Transaksi</p>
                            <h3 class="text-2xl font-bold mt-1">{{ props.stats.transactionCount }}</h3>
                        </div>
                        <div class="h-12 w-12 rounded-full bg-orange-100 dark:bg-orange-950 flex items-center justify-center">
                            <ReceiptText class="h-6 w-6 text-orange-600 dark:text-orange-400" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="/admin/laporan" class="text-sm text-orange-600 dark:text-orange-400 hover:underline">Lihat laporan penjualan</a>
                    </div>
                </div>

                <!-- Kartu Total Penjualan -->
                <div class="rounded-xl border border-border p-6 bg-background shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-muted-foreground">Total Penjualan</p>
                            <h3 class="text-2xl font-bold mt-1">{{ formatRupiah(props.stats.totalSales) }}</h3>
                        </div>
                        <div class="h-12 w-12 rounded-full bg-green-100 dark:bg-green-950 flex items-center justify-center">
                            <CircleDollarSign class="h-6 w-6 text-green-600 dark:text-green-400" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="/admin/laporan" class="text-sm text-green-600 dark:text-green-400 hover:underline">Lihat detail pendapatan</a>
                    </div>
                </div>
            </div>

            <div class="mt-6 rounded-xl border border-border p-6 bg-background shadow-sm">
                <h2 class="text-lg font-semibold mb-4">Ringkasan Sistem</h2>
                <div class="space-y-2">
                    <p class="text-sm text-muted-foreground">
                        Selamat datang di dashboard admin sistem kasir. Dari sini Anda dapat mengelola:
                    </p>
                    <ul class="list-disc pl-5 space-y-1 text-sm text-foreground">
                        <li>Data produk yang tersedia untuk dijual</li>
                        <li>Data pelanggan yang terdaftar dalam sistem</li>
                        <li>Laporan penjualan dan statistik pendapatan</li>
                    </ul>
                    <p class="text-sm text-muted-foreground mt-4">
                        Gunakan menu navigasi di sebelah kiri untuk akses cepat ke semua fitur sistem.
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
