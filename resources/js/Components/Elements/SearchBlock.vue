<script setup>
import ApplicationLogo from '@/Components/UI/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import { usercart } from '@/Mixins/UserCart.js';

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
                <Link :href="route('user.cart')" class="rounded-md hover:bg-indigo-50 text-indigo-700 p-2 relative" title="Открыть корзину">
                    <i class="ri-shopping-basket-2-line text-xl m-2"></i>
                    <div v-show="usercart.length>0" class="absolute top-0 right-0">
                        {{ usercart.length }}
                    </div>
                </Link>
            </div>
        </div>
    </div>
</template>