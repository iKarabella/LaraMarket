<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import FullLayout from '@/Layouts/FullLayout.vue';
import Pagination from '@/Components/Pagination';
import User from './Partials/User.vue';
import MainModal from '@/Components/Modals/MainModal.vue';
import PrimaryButton from '@/Components/UI/PrimaryButton.vue';
import SecondaryButton from '@/Components/UI/SecondaryButton.vue';
import InputError from '@/Components/UI/InputError.vue';
import InputLabel from '@/Components/UI/InputLabel.vue';
import Checkbox from '@/Components/UI/Checkbox.vue';
import TextInput from '@/Components/UI/TextInput.vue';
import {TextTranslit} from '@/Mixins/TextFormat.js';
import { ref } from 'vue';

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
    users: {type: Object, default:{}},
    roles: {type: Array, default:[]}
});

const params = useForm({
    phone: props.filter.phone??'',
    name: props.filter.name??'',
    surname: props.filter.surname??'',
    nickname: props.filter.nickname??'',
    order: props.filter.order??'',
    desc: props.filter.desc??false,
    page: props.filter.page??null
});


const userEdit = (id)=>{
    let user = props.users.data.find(arr=>arr.id==id);
    if (!user) return false;
                
    editForm.id = user.id;
    editForm.name = user.name,
    editForm.patronymic = user.patronymic,
    editForm.surname = user.surname,
    editForm.nickname = user.nickname,
    editForm.roles = user.roles,

    editUserForm.value = true;
};

const updateFilters = ()=>{
    params.page=null;
    reloadList();
};

const emitPaginate = (e)=>{
    params.page=e;
    reloadList();
};

const editForm = useForm({
    id: null,
    name: null,
    patronymic: null,
    surname: null,
    login: null,
    roles: [],
});

const editUserForm = ref(false);

const reloadList = ()=>{
    params.post(route('admin.users.manage'), {
        preserveScroll: true,
        //onSuccess: () => console.log('response'),
        // onError: () => {
        //     console.log('error response');
        // }
    });
}

const closeModal = ()=>{
    editUserForm.value = false;
};

const setRole = (rid)=>{
    if (editForm.id===null) return false;

    let index = editForm.roles.findIndex(f => f.id == rid);

    if (index>=0) editForm.roles.splice(index, 1);
    else{
        editForm.roles.push({
            id:rid
        });
    }
};

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
        <MainModal :show="editUserForm" @close="closeModal">
            <div class="p-6"><form @submit.prevent="editForm.patch(route('admin.users.update'))">
                <div>
                    <InputLabel for="name" value="Имя"/>

                    <TextInput
                        id="name"
                        ref="refName"
                        v-model="editForm.name"
                        type="text"
                        class="mt-1 block w-full"
                    />

                    <InputError :message="editForm.errors.name" class="mt-2" />
                </div>
                <div>
                    <InputLabel for="patronymic" value="Отчество" />

                    <TextInput
                        id="patronymic"
                        v-model="editForm.patronymic"
                        type="text"
                        class="mt-1 block w-full"
                    />

                    <InputError :message="editForm.errors.patronymic" class="mt-2" />
                </div>
                <div>
                    <InputLabel for="surname" value="Фамилия" />

                    <TextInput
                        id="surname"
                        v-model="editForm.surname"
                        type="text"
                        class="mt-1 block w-full"
                    />

                    <InputError :message="editForm.errors.surname" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="login" value="Псевдоним" />

                    <TextInput
                        id="login"
                        v-model="editForm.login"
                        :keyup="editForm.login=TextTranslit(editForm.login, true)"
                        type="text"
                        class="mt-1 block w-full"
                    />

                    <InputError :message="editForm.errors.login" class="mt-2" />
                </div>

                <div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="block font-medium text-sm text-gray-700 dark:text-gray-300">Роли:</div>
                    </div>
                    <div class="md:grid md:grid-cols-3">
                        <div v-for="role in roles" class="p-2 rounded hover:bg-gray-200">
                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                                <Checkbox :checked="editForm.roles.findIndex(f=>f.id == role.id)>=0" 
                                          @change="setRole(role.id)"
                                          class="mr-4" /> 
                                {{ role.name }}
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-4 mt-2">
                    <InputError :message="editForm.errors.id" class="mt-2" />
                    <Transition
                        enter-active-class="transition ease-in-out"
                        enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out"
                        leave-to-class="opacity-0"
                    >
                        <p v-if="editForm.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">Сохранено</p>
                    </Transition>
                    <PrimaryButton :disabled="editForm.processing">Сохранить</PrimaryButton>
                    <SecondaryButton @click="closeModal">Закрыть</SecondaryButton>
                </div>
            </form></div>
        </MainModal>
    </FullLayout>
</template>
