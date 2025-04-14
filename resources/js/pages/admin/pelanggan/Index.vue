<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
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
import { PlusCircle, Edit, Eye, Trash, MoreHorizontal } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pelanggan',
        href: route('pelanggans.index'),
    },
];

const props = defineProps<{
  pelanggans: Array<{
    id: number;
    nama: string;
    alamat: string;
    no_hp: string;
    created_at: string;
  }>
}>();

// Delete confirmation
const showDeleteDialog = ref(false);
const pelangganToDelete = ref<{id: number, nama: string} | null>(null);

// Fungsi untuk format tanggal
function formatDate(dateString: string): string {
  const date = new Date(dateString);
  return date.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
}

function confirmDelete(pelanggan: {id: number, nama: string}) {
  pelangganToDelete.value = pelanggan;
  showDeleteDialog.value = true;
}

function deletePelanggan() {
  if (pelangganToDelete.value) {
    router.delete(route('pelanggans.destroy', pelangganToDelete.value.id), {
      onSuccess: () => {
        showDeleteDialog.value = false;
        pelangganToDelete.value = null;
      },
    });
  }
}
</script>

<template>
  <Head title="Pelanggan" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4">
      <!-- Header dengan tombol tambah -->
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold tracking-tight">Daftar Pelanggan</h2>
        <Button class="flex items-center gap-2" as-child>
          <Link :href="route('pelanggans.create')">
            <PlusCircle class="size-4" />
            <span>Tambah Pelanggan</span>
          </Link>
        </Button>
      </div>

      <!-- Tabel Pelanggan -->
      <div class="rounded-lg border">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Nama</TableHead>
              <TableHead>Alamat</TableHead>
              <TableHead>No. HP</TableHead>
              <TableHead>Tanggal Dibuat</TableHead>
              <TableHead class="w-[150px]">Aksi</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="pelanggan in props.pelanggans" :key="pelanggan.id">
              <TableCell class="font-medium">{{ pelanggan.nama }}</TableCell>
              <TableCell>{{ pelanggan.alamat }}</TableCell>
              <TableCell>{{ pelanggan.no_hp }}</TableCell>
              <TableCell>{{ formatDate(pelanggan.created_at) }}</TableCell>
              <TableCell>
                <DropdownMenu>
                  <DropdownMenuTrigger asChild>
                    <Button variant="ghost" size="icon">
                      <MoreHorizontal class="size-4" />
                    </Button>
                  </DropdownMenuTrigger>
                  <DropdownMenuContent align="end" class="w-40">
                    <DropdownMenuItem asChild>
                      <Link :href="route('pelanggans.show', pelanggan.id)" class="flex items-center gap-2">
                        <Eye class="size-4" />
                        <span>Detail</span>
                      </Link>
                    </DropdownMenuItem>
                    <DropdownMenuItem asChild>
                      <Link :href="route('pelanggans.edit', pelanggan.id)" class="flex items-center gap-2">
                        <Edit class="size-4" />
                        <span>Edit</span>
                      </Link>
                    </DropdownMenuItem>
                    <DropdownMenuItem 
                      class="flex items-center gap-2 text-destructive focus:text-destructive"
                      @click="confirmDelete({id: pelanggan.id, nama: pelanggan.nama})"
                    >
                      <Trash class="size-4" />
                      <span>Hapus</span>
                    </DropdownMenuItem>
                  </DropdownMenuContent>
                </DropdownMenu>
              </TableCell>
            </TableRow>
            <TableRow v-if="props.pelanggans.length === 0">
              <TableCell colspan="5" class="text-center py-10 text-muted-foreground">
                Tidak ada data pelanggan
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
          Apakah Anda yakin ingin menghapus pelanggan "{{ pelangganToDelete?.nama }}"?
          Tindakan ini tidak dapat dibatalkan.
        </DialogDescription>
      </DialogHeader>
      <DialogFooter class="flex justify-end gap-2 pt-4">
        <Button variant="outline" @click="showDeleteDialog = false">Batal</Button>
        <Button variant="destructive" @click="deletePelanggan">Hapus</Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template> 