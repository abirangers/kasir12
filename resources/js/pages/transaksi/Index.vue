<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { 
    Table, 
    TableBody, 
    TableCell, 
    TableHead, 
    TableHeader, 
    TableRow 
} from '@/components/ui/table';
import { 
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle
} from '@/components/ui/card';
import { 
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import { 
    Dialog, 
    DialogContent, 
    DialogHeader, 
    DialogTitle, 
    DialogDescription, 
    DialogFooter
} from '@/components/ui/dialog';
import { toast } from 'vue-sonner';
import { 
    Form, 
    FormDescription, 
    FormField, 
    FormItem, 
    FormLabel
} from '@/components/ui/form';
import { 
    Calculator, 
    CreditCard, 
    MinusCircle, 
    PlusCircle, 
    Search, 
    ShoppingCart, 
    Trash, 
    User
} from 'lucide-vue-next';
import { PageProps } from '@inertiajs/core';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Transaksi',
        href: route('transaksi.index'),
    },
];

// Menerima data dari controller
const props = defineProps<{
    produks: {
        id: number;
        nama: string;
        harga: number;
        stok: number;
    }[];
    pelanggans: {
        id: number;
        nama: string;
        alamat: string;
        no_hp: string;
    }[];
    cart: Record<string, {
        id: number;
        nama: string;
        harga: number;
        jumlah: number;
        subtotal: number;
    }>;
    total: number;
}>();

// State untuk form produk
const produkId = ref('');
const jumlahProduk = ref(1);
const searchQuery = ref('');

// State untuk dialog
const showCheckoutDialog = ref(false);
const isProcessing = ref(false);
const kembalian = computed(() => {
    const bayar = parseFloat(totalBayar.value) || 0;
    return bayar - totalFromCart.value;
});

// State untuk form pelanggan
const isPelangganBaru = ref(false);
const pelangganId = ref('');
const namaPelanggan = ref('');
const alamatPelanggan = ref('');
const noHpPelanggan = ref('');
const totalBayar = ref('');

// Computed untuk daftar produk yang difilter
const filteredProduks = computed(() => {
    if (!searchQuery.value) return props.produks;
    const query = searchQuery.value.toLowerCase();
    return props.produks.filter(produk => 
        produk.nama.toLowerCase().includes(query) || 
        produk.id.toString().includes(query)
    );
});

// Computed untuk total harga dari cart
const totalFromCart = computed(() => {
    let total = 0;
    for (const item of Object.values(props.cart)) {
        total += item.subtotal;
    }
    return total;
});

// Computed untuk item dalam cart
const cartItems = computed(() => {
    return Object.values(props.cart);
});

// Watch untuk kembalian
watch(totalBayar, (newValue) => {
    const bayar = parseFloat(newValue) || 0;
    if (bayar < totalFromCart.value && bayar > 0) {
        toast.error('Jumlah pembayaran kurang dari total belanja!');
    }
});

// Watch untuk pelanggan
watch(pelangganId, (newValue) => {
    if (newValue === 'baru') {
        isPelangganBaru.value = true;
    } else {
        isPelangganBaru.value = false;
    }
});

// Handler untuk menambahkan produk ke cart
const addToCart = (product: any) => {
    if (!product) {
        toast.error('Silakan pilih produk terlebih dahulu!');
        return;
    }

    router.post(route('transaksi.addToCart'), {
        produk_id: product.id,
        jumlah: jumlahProduk.value
    }, {
        preserveScroll: true,
        onSuccess: (page) => {
            // Memproses response JSON yang diterima langsung
            if (!page && window.location.reload) {
                window.location.reload();
                return;
            }
            
            toast.success('Produk berhasil ditambahkan ke keranjang!');
            produkId.value = '';
            jumlahProduk.value = 1;
        },
        onError: (errors) => {
            toast.error(errors.message || 'Gagal menambahkan produk ke keranjang!');
        }
    });
};

// Handler untuk mengupdate jumlah produk di cart
const updateCartItem = (id: number, jumlah: number) => {
    router.post(route('transaksi.updateCart'), {
        produk_id: id,
        jumlah: jumlah
    }, {
        preserveScroll: true,
    });
};

// Handler untuk menghapus produk dari cart
const removeFromCart = (id: number) => {
    router.post(route('transaksi.removeFromCart'), {
        produk_id: id
    }, {
        preserveScroll: true,
    });
};

// Handler untuk mengosongkan cart
const clearCart = () => {
    router.post(route('transaksi.clearCart'), {}, {
        preserveScroll: true,
    });
};

// Handler untuk checkout
const openCheckout = () => {
    if (cartItems.value.length === 0) {
        toast.error('Keranjang belanja masih kosong!');
        return;
    }
    
    // Reset form
    if (!isPelangganBaru.value) {
        namaPelanggan.value = '';
        alamatPelanggan.value = '';
        noHpPelanggan.value = '';
    }
    
    totalBayar.value = totalFromCart.value.toString();
    showCheckoutDialog.value = true;
};

// Handler untuk proses pembayaran
const processPayment = () => {
    console.log('Processing payment...');
    console.log('isPelangganBaru:', isPelangganBaru.value);
    console.log('pelangganId:', pelangganId.value);
    console.log('namaPelanggan:', namaPelanggan.value);
    console.log('alamatPelanggan:', alamatPelanggan.value);
    console.log('noHpPelanggan:', noHpPelanggan.value);
    console.log('totalBayar:', totalBayar.value);

    if (parseFloat(totalBayar.value) < totalFromCart.value) {
        toast.error('Jumlah pembayaran kurang dari total belanja!');
        return;
    }

    if (isPelangganBaru.value) {
        if (!namaPelanggan.value || !alamatPelanggan.value || !noHpPelanggan.value) {
            toast.error('Data pelanggan tidak lengkap!');
            return;
        }
    } else if (!pelangganId.value) {
        toast.error('Silakan pilih pelanggan terlebih dahulu!');
        return;
    }

    isProcessing.value = true;

    const formData = {
        pelanggan_id: isPelangganBaru.value ? null : pelangganId.value,
        nama_pelanggan: namaPelanggan.value,
        alamat_pelanggan: alamatPelanggan.value,
        no_hp_pelanggan: noHpPelanggan.value,
        total_bayar: parseFloat(totalBayar.value),
    };

    console.log('Sending formData:', formData);

    router.post(route('transaksi.store'), formData, {
        preserveScroll: true,
        onSuccess: () => {
            isProcessing.value = false;
            showCheckoutDialog.value = false;
            toast.success('Transaksi berhasil!');
            
            // Reset form
            pelangganId.value = '';
            namaPelanggan.value = '';
            alamatPelanggan.value = '';
            noHpPelanggan.value = '';
            totalBayar.value = '';
            isPelangganBaru.value = false;
        },
        onError: (errors) => {
            isProcessing.value = false;
            console.error('Errors:', errors);
            toast.error(errors.message || 'Gagal melakukan transaksi!');
        }
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

// Get stok status
const getStokStatus = (stok: number) => {
    if (stok <= 5) return 'destructive';
    if (stok <= 10) return 'secondary';
    return 'default';
};

// Tipe untuk page props
interface CustomPageProps extends PageProps {
    flash: {
        success?: string;
        error?: string;
    };
}

const page = usePage<CustomPageProps>();

// Menampilkan flash message
onMounted(() => {
    const flashSuccess = page.props.flash.success;
    const flashError = page.props.flash.error;
    
    if (flashSuccess) {
        toast.success(flashSuccess);
    }
    
    if (flashError) {
        toast.error(flashError);
    }
});

// Handler untuk mengganti mode pelanggan
const addNewCustomer = () => {
    isPelangganBaru.value = true;
    pelangganId.value = '';
};

const useExistingCustomer = () => {
    isPelangganBaru.value = false;
    pelangganId.value = '';
    namaPelanggan.value = '';
    alamatPelanggan.value = '';
    noHpPelanggan.value = '';
};
</script>

<template>
    <Head title="Transaksi" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col lg:flex-row gap-4 p-4">
            <!-- Kolom kiri: Katalog Produk -->
            <div class="w-full lg:w-2/3 space-y-4">
                <Card>
                    <CardHeader>
                        <CardTitle>Katalog Produk</CardTitle>
                        <CardDescription>Pilih produk untuk ditambahkan ke keranjang</CardDescription>
                        
                        <!-- Form tambah produk -->
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mt-4">
                            <div class="md:col-span-6">
                                <Select v-model="produkId">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Pilih produk" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="produk in props.produks" 
                                                 :key="produk.id" 
                                                 :value="produk.id.toString()"
                                                 :disabled="produk.stok <= 0">
                                            {{ produk.nama }} - {{ formatRupiah(produk.harga) }} 
                                            <Badge v-if="produk.stok <= 0" variant="destructive">Habis</Badge>
                                            <Badge v-else :variant="getStokStatus(produk.stok)">Stok: {{ produk.stok }}</Badge>
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="md:col-span-2">
                                <Input 
                                    type="number" 
                                    v-model="jumlahProduk" 
                                    min="1" 
                                    placeholder="Jumlah"
                                />
                            </div>
                            <div class="md:col-span-4">
                                <Button class="w-full" @click="addToCart(props.produks.find(p => p.id.toString() === produkId))">
                                    <ShoppingCart class="mr-2 size-4" />
                                    Tambah ke Keranjang
                                </Button>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <!-- Pencarian produk -->
                        <div class="relative mb-4">
                            <Search class="absolute left-2 top-2.5 size-4 text-muted-foreground" />
                            <Input
                                v-model="searchQuery"
                                placeholder="Cari produk..."
                                class="pl-8"
                            />
                        </div>

                        <!-- Daftar produk dalam bentuk grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <Card v-for="produk in filteredProduks" :key="produk.id" class="overflow-hidden">
                                <CardHeader class="p-4">
                                    <CardTitle class="text-base">{{ produk.nama }}</CardTitle>
                                    <CardDescription>{{ formatRupiah(produk.harga) }}</CardDescription>
                                </CardHeader>
                                <CardFooter class="flex justify-between p-4 pt-0">
                                    <Badge :variant="getStokStatus(produk.stok)">
                                        Stok: {{ produk.stok }}
                                    </Badge>
                                    <Button 
                                        size="sm" 
                                        @click="produkId = produk.id.toString(); addToCart(produk)"
                                        :disabled="produk.stok <= 0"
                                        :variant="produk.stok <= 0 ? 'outline' : 'default'"
                                    >
                                        <ShoppingCart class="mr-1 size-4" />
                                        <span v-if="produk.stok <= 0">Habis</span>
                                        <span v-else>Tambah</span>
                                    </Button>
                                </CardFooter>
                            </Card>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Kolom kanan: Keranjang & Checkout -->
            <div class="w-full lg:w-1/3 space-y-4">
                <Card>
                    <CardHeader>
                        <div class="flex justify-between items-center">
                            <CardTitle>Keranjang Belanja</CardTitle>
                            <Button variant="outline" size="sm" @click="clearCart" :disabled="cartItems.length === 0">
                                <Trash class="mr-1 size-4" />
                                Kosongkan
                            </Button>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div v-if="cartItems.length === 0" class="text-center py-6 text-muted-foreground">
                            Keranjang belanja masih kosong.
                        </div>
                        <div v-else>
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Produk</TableHead>
                                        <TableHead class="text-right">Harga</TableHead>
                                        <TableHead class="text-center">Jumlah</TableHead>
                                        <TableHead class="text-right">Subtotal</TableHead>
                                        <TableHead></TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="item in cartItems" :key="item.id">
                                        <TableCell>{{ item.nama }}</TableCell>
                                        <TableCell class="text-right">{{ formatRupiah(item.harga) }}</TableCell>
                                        <TableCell>
                                            <div class="flex items-center justify-center">
                                                <Button 
                                                    variant="ghost" 
                                                    size="icon" 
                                                    @click="updateCartItem(item.id, Math.max(1, item.jumlah - 1))"
                                                >
                                                    <MinusCircle class="size-4" />
                                                </Button>
                                                <span class="w-8 text-center">{{ item.jumlah }}</span>
                                                <Button 
                                                    variant="ghost" 
                                                    size="icon" 
                                                    @click="updateCartItem(item.id, item.jumlah + 1)"
                                                >
                                                    <PlusCircle class="size-4" />
                                                </Button>
                                            </div>
                                        </TableCell>
                                        <TableCell class="text-right">{{ formatRupiah(item.subtotal) }}</TableCell>
                                        <TableCell>
                                            <Button 
                                                variant="ghost" 
                                                size="icon" 
                                                @click="removeFromCart(item.id)"
                                            >
                                                <Trash class="size-4 text-destructive" />
                                            </Button>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                            
                            <!-- Total dan tombol checkout -->
                            <div class="mt-4 space-y-4">
                                <div class="flex justify-between items-center text-lg font-semibold border-t pt-4">
                                    <span>Total</span>
                                    <span>{{ formatRupiah(totalFromCart) }}</span>
                                </div>
                                <Button class="w-full" size="lg" @click="openCheckout" :disabled="cartItems.length === 0">
                                    <CreditCard class="mr-2 size-5" />
                                    Lanjutkan ke Pembayaran
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>

    <!-- Dialog Checkout -->
    <Dialog v-model:open="showCheckoutDialog">
        <DialogContent class="sm:max-w-[600px]">
            <DialogHeader>
                <DialogTitle>Checkout Pembayaran</DialogTitle>
                <DialogDescription>
                    Lengkapi data pelanggan dan pembayaran untuk menyelesaikan transaksi.
                </DialogDescription>
            </DialogHeader>
            
            <Form @submit="processPayment" class="space-y-6">
                <!-- Data Pelanggan -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium flex items-center">
                        <User class="mr-2 size-5" />
                        Data Pelanggan
                    </h3>
                    
                    <div v-if="!isPelangganBaru">
                        <FormField name="pelanggan_select">
                            <FormItem>
                                <FormLabel>Pilih Pelanggan</FormLabel>
                                <Select v-model="pelangganId">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Pilih pelanggan" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="pelanggan in props.pelanggans" :key="pelanggan.id" :value="pelanggan.id.toString()">
                                            {{ pelanggan.nama }} - {{ pelanggan.no_hp }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <FormDescription>
                                    Pilih pelanggan dari daftar atau 
                                    <Button type="button" variant="link" class="p-0 h-auto" @click="addNewCustomer">
                                        tambah pelanggan baru
                                    </Button>
                                </FormDescription>
                            </FormItem>
                        </FormField>
                    </div>
                    
                    <div v-if="isPelangganBaru" class="space-y-4">
                        <!-- Form untuk pelanggan baru -->
                        <FormField name="nama_pelanggan">
                            <FormItem>
                                <FormLabel>Nama Pelanggan</FormLabel>
                                <Input v-model="namaPelanggan" required placeholder="Masukkan nama pelanggan" />
                            </FormItem>
                        </FormField>
                        
                        <FormField name="alamat_pelanggan">
                            <FormItem>
                                <FormLabel>Alamat</FormLabel>
                                <Input v-model="alamatPelanggan" required placeholder="Masukkan alamat pelanggan" />
                            </FormItem>
                        </FormField>
                        
                        <FormField name="no_hp_pelanggan">
                            <FormItem>
                                <FormLabel>Nomor Telepon</FormLabel>
                                <Input v-model="noHpPelanggan" required placeholder="Masukkan nomor telepon" />
                            </FormItem>
                        </FormField>
                        
                        <div class="pt-2">
                            <Button type="button" variant="outline" @click="useExistingCustomer">
                                Pilih Pelanggan yang Sudah Ada
                            </Button>
                        </div>
                    </div>
                </div>
                
                <!-- Data Pembayaran -->
                <div class="space-y-4 border-t pt-4">
                    <h3 class="text-lg font-medium flex items-center">
                        <Calculator class="mr-2 size-5" />
                        Data Pembayaran
                    </h3>
                    
                    <!-- Ringkasan belanja -->
                    <div class="rounded-lg bg-muted p-4">
                        <div class="space-y-1">
                            <div class="flex justify-between text-sm">
                                <span class="text-muted-foreground">Jumlah Item:</span>
                                <span>{{ cartItems.length }} produk</span>
                            </div>
                            <div class="flex justify-between font-medium">
                                <span>Total Belanja:</span>
                                <span>{{ formatRupiah(totalFromCart) }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Input jumlah bayar -->
                    <FormField name="total_bayar">
                        <FormItem>
                            <FormLabel>Jumlah Pembayaran</FormLabel>
                            <Input 
                                v-model="totalBayar" 
                                type="number" 
                                required 
                                min="0"
                                placeholder="Masukkan jumlah yang dibayarkan" 
                            />
                        </FormItem>
                    </FormField>
                    
                    <!-- Kalkulasi kembalian -->
                    <div v-if="kembalian >= 0 && totalBayar" class="flex justify-between items-center p-3 rounded-lg bg-green-50 dark:bg-green-950 text-green-900 dark:text-green-100">
                        <span class="font-medium">Kembalian:</span>
                        <span class="text-lg font-bold">{{ formatRupiah(kembalian) }}</span>
                    </div>
                    
                    <div v-else-if="kembalian < 0 && totalBayar" class="flex justify-between items-center p-3 rounded-lg bg-red-50 dark:bg-red-950 text-red-900 dark:text-red-100">
                        <span class="font-medium">Kurang Bayar:</span>
                        <span class="text-lg font-bold">{{ formatRupiah(Math.abs(kembalian)) }}</span>
                    </div>
                </div>
                
                <DialogFooter>
                    <Button type="button" variant="outline" @click="showCheckoutDialog = false">Batal</Button>
                    <Button type="submit" :disabled="isProcessing">
                        <span v-if="isProcessing">Memproses...</span>
                        <span v-else>Selesaikan Pembayaran</span>
                    </Button>
                </DialogFooter>
            </Form>
        </DialogContent>
    </Dialog>
</template>
