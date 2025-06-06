<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import FullLayout from '@/Layouts/FullLayout.vue';
import Pagination from '@/Components/Pagination';
import User from './Partials/User.vue';

const props = defineProps({
    filter:{type:Object, default:{
        phone: '',
        name: '',
        surname: '',
        login: '',
        order: '',
        desc: false,
        page: null,
    }},
    users: {type: Array, default:[]}
});

const params = useForm({
    phone: props.filter.phone??'',
    name: props.filter.name??'',
    surname: props.filter.surname??'',
    login: props.filter.login??'',
    order: props.filter.order??'',
    desc: props.filter.desc??false,
    page: props.filter.page??null
});

const emitPaginate = (e)=>{
    params.page=e;
    params.post(route('admin.users.manage'), {
        preserveScroll: true,
        //onSuccess: () => console.log('response'),
        // onError: () => {
        //     console.log('error response', this.editAchievementForm.errors);
        // }
    });
}
const userEdit = (e)=>{
    console.log('userEdit', e);
}

</script>

<template>
    <Head title="Users Manage">
    </Head>
    <FullLayout>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <table class="w-full table-auto">
                    <User @update-params="updateFilters" :params="params"/>
                    <User v-for="user in users.data" :key="user.id" :user="user" @update="emitPaginate" @user-edit="userEdit"/>
                </table>
                <div class="w-full flex justify-center">
                    <Pagination :paginator="users.meta" :href="false" @getPage="emitPaginate"/>
                </div>
            </div>
        </div>
    </FullLayout>
</template>
