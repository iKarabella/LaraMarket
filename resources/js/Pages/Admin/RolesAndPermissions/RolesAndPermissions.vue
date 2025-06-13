<script setup>
import PrimaryButton from '@/Components/UI/PrimaryButton.vue';
import SecondaryButton from '@/Components/UI/SecondaryButton.vue';
import InputError from '@/Components/UI/InputError.vue';
import TextInput from '@/Components/UI/TextInput.vue';
import FullLayout from '@/Layouts/FullLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modals/MainModal.vue';
import { ref } from 'vue';
import Checkbox from '@/Components/UI/Checkbox.vue';
import DangerButton from '@/Components/UI/DangerButton.vue';
</script>
<script>
    export default {
        props: {
            status: {type: String}, 
            roles: {type: Array}, 
            permissions: {type: Array}
        },
        data() { return {
            editRoleForm: ref(false),
            roleForm: {},
        }},
        methods:{
            closeModal: function() {
                this.roleForm = {};
                this.editRoleForm = false;
            },
            removeRole: function(){
                let conf = confirm("Вы действительно хотите удалить роль? \n Это действие необратимо!");

                if(conf){
                    this.roleForm.delete(route('admin.roles_and_permissions.delete'), {
                        preserveScroll: true,
                        onSuccess: () => this.closeModal(),
                        onError: (e) => console.log('error', e),
                        onFinish: () => console.log('finish'),
                    });
                }
                else return false;
            },
            storeRole: function(){
                this.roleForm.patch(route('admin.roles_and_permissions.update'), {onSuccess: () => this.closeModal()});
            },
            roleEdit: function(rid=null) 
            {
                let role = false;
                
                if (rid) role = this.roles.find(r=>r.id == rid);
                if (rid && !role) return false;

                this.roleForm = useForm({
                    name: role?role.name:'',
                    role_id: role?role.id:null,
                    description: role?role.description:'',
                    permissions: role?role.permissions:[]
                });
                
                this.editRoleForm = true;
            },
            setPermissions(pid){

                let index = this.roleForm.permissions.findIndex(f => f.id==pid);

                if (index>=0) this.roleForm.permissions.splice(index, 1);
                else{
                    this.roleForm.permissions.push({
                        id: pid
                    });
                }
            }
        }
    }
</script>
<template>
    <Head title="Роли и Права" />

    <FullLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Роли и права</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="w-full md:grid md:grid-cols-2">
                    <div>
                        <PrimaryButton @click="roleEdit()">Новая роль</PrimaryButton>
                    </div>
                </div>
                <div>
                    <div v-for="role in roles" :key="role.id" class="flex pt-2">
                        <div class="w-full">{{ role.name }}</div>
                        <div>
                            <SecondaryButton @click="roleEdit(role.id)">Редактировать</SecondaryButton>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Modal :show="editRoleForm" @close="closeModal">
            <div class="p-6">
                <div>
                    <TextInput
                        id="name"
                        v-model="roleForm.name"
                        type="text"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="roleForm.errors.name" class="mt-2"/>
                </div>
    
                <div class="mt-2">
                    <span class="block font-medium text-sm text-gray-700 dark:text-gray-300">Описание:</span>
                    <textarea id="name"
                              v-model="roleForm.description"
                              type="text"
                              class="mt-1 block w-full"
                    />
                    <InputError :message="roleForm.errors.name" class="mt-2"/>
                </div>
    
                <div class="mt-2">
                    <span class="block font-medium text-sm text-gray-700 dark:text-gray-300">Права:</span>
                    <div class="md:grid md:grid-cols-2 md:gap-2">
                        <div v-for="permission in permissions" class="p-2 rounded hover:bg-gray-200">
                            <label>
                                <Checkbox :checked="roleForm.permissions.findIndex(f=>f.id==permission.id)>=0" 
                                          @change="setPermissions(permission.id)" 
                                          class="mr-4"/> 
                                {{ permission.name }}
                            </label>
                        </div>
                    </div>
                </div>
    
                <div class="flex w-full mt-4">
                    <div class="flex w-full">
                        <DangerButton v-show="roleForm.role_id"
                            @click="removeRole()" 
                            class="mr-4">Удалить роль</DangerButton>
                        <PrimaryButton @click="storeRole()" :disabled="roleForm.processing">Сохранить</PrimaryButton>
                    </div>
                    <SecondaryButton @click="closeModal">Закрыть</SecondaryButton>
                </div>
    
                <div class="h-6">
                    <Transition
                        enter-active-class="transition ease-in-out"
                        enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out"
                        leave-to-class="opacity-0"
                    >
                        <p v-if="roleForm.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">Сохранено</p>
                    </Transition>
                </div>
            </div>
        </Modal>
    </FullLayout>
</template>