<script setup>
import ApplicationLogo from '@/Components/UI/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import { createGlobalState, useStorage } from '@vueuse/core'

// const user_cart = useStorage('user_cart', []);

const global_user_cart = createGlobalState(
  () => useStorage('user_cart', []),
);

const user_cart = global_user_cart();

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
                basket: {{ user_cart.length }}
            </div>
        </div>
    </div>
</template>