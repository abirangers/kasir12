<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { LayoutGrid, Package, ShoppingCart, File, Users } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const page = usePage<SharedData>();
const user = page.props.auth.user;
const isAdmin = user.role === 'admin';

// Menu yang hanya muncul untuk admin
const adminNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/admin/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Produk',
        href: '/admin/produks',
        icon: Package,
    },
    {
        title: 'Pelanggan',
        href: '/admin/pelanggans',
        icon: Users,
    },
];

// Menu transaksi untuk semua pengguna
const commonNavItems: NavItem[] = [
    {
        title: 'Transaksi',
        href: '/transaksi',
        icon: ShoppingCart,
    },
];

// Menu laporan hanya untuk admin
const reportNavItems: NavItem[] = [
    {
        title: 'Laporan',
        href: '/admin/laporan',
        icon: File,
    },
];

// Gabungkan menu berdasarkan peran pengguna
const mainNavItems = isAdmin 
    ? [...adminNavItems, ...commonNavItems, ...reportNavItems] 
    : commonNavItems;

</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
