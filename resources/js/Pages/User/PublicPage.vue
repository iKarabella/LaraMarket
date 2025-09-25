<script setup>
import FullLayout from '@/Layouts/FullLayout.vue';
import { Head } from '@inertiajs/vue3';
import UserColumn from './Partials/UserColumn.vue';
defineProps({
    userInfo:{type:Object, default:{}},
    orders:{type:Array, default:[]},
});
</script>
<template>
    <Head>
        <title>@{{userInfo.login}}</title>
        <meta name="description" :content="'Страница пользователя @'+userInfo.login"/>
    </Head>

    <FullLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Привет</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="text-gray-900 dark:text-gray-100">
                    <div class="block md:flex">
                        <UserColumn class="md:w-72 md:mr-4 py-6 pl-6" :userInfo="userInfo" :edit="$page.props.auth && $page.props.auth.user.id == userInfo.id"/>
                        <div class="px-4 border-t border-t-gray-300 pt-2 mt-2 md:border-t-0 md:border-l md:border-l-gray-300 md:m-0 md:w-9/12">
                            <div v-for="order in orders">
                                {{ order }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </FullLayout>
</template>