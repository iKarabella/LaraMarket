<script setup>
import ApplicationLogo from '@/Components/UI/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>
<template>
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between">
            <Link :href="route('home')">
                <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200"/>
            </Link>
            <Link href="#">
                <i class="ri-menu-line"></i>
                Каталог
            </Link>
            <div>
                search
            </div>
            <div>
                basket
            </div>
        </div>
    </div>
</template>