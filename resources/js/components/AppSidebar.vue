<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { BookOpen, LayoutGrid, Trophy, TrendingUp, Activity, Image, BarChart3, Smile, HelpCircle } from 'lucide-vue-next';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { index as lettersIndex } from '@/routes/letters';
import { index as badgesIndex } from '@/routes/badges';
import { index as leaderboardIndex } from '@/routes/leaderboard';
import { index as activitiesIndex } from '@/routes/activities';
import { index as photosIndex } from '@/routes/photos';
import { index as statsIndex } from '@/routes/stats';
import { type NavItem } from '@/types';
import AppLogo from './AppLogo.vue';

// Rotas para memes e quizzes (serão geradas automaticamente)
const memesIndex = () => '/memes';
const quizzesIndex = () => '/quizzes';

const mainNavItems: NavItem[] = [
    {
        title: 'Painel',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Cartas',
        href: lettersIndex(),
        icon: BookOpen,
    },
    {
        title: 'Minhas Conquistas',
        href: badgesIndex(),
        icon: Trophy,
    },
    {
        title: 'Leaderboard',
        href: leaderboardIndex(),
        icon: TrendingUp,
    },
    {
        title: 'Feed de Atividades',
        href: activitiesIndex(),
        icon: Activity,
    },
    {
        title: 'Mural de Fotos',
        href: photosIndex(),
        icon: Image,
    },
    {
        title: 'Estatísticas',
        href: statsIndex(),
        icon: BarChart3,
    },
    {
        title: 'Mural de Memes',
        href: memesIndex(),
        icon: Smile,
    },
    {
        title: 'Quiz Semanal',
        href: quizzesIndex(),
        icon: HelpCircle,
    },
];

const footerNavItems: NavItem[] = [
    // {
    //     title: 'Github Repo',
    //     href: 'https://github.com/laravel/vue-starter-kit',
    //     icon: Folder,
    // }
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
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
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
