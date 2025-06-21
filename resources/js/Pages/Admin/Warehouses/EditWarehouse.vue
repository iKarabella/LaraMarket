<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import MarketLayout from '@/Layouts/MarketLayout.vue';
import InputLabel from '@/Components/UI/InputLabel.vue';
import TextInput from '@/Components/UI/TextInput.vue';
import InputError from '@/Components/UI/InputError.vue';
import SecondaryButton from '@/Components/UI/SecondaryButton.vue';
import Tiptap from '@/Components/Tiptap/Tiptap.vue';
import PrimaryButton from '@/Components/UI/PrimaryButton.vue';
import Checkbox from '@/Components/UI/Checkbox.vue';
import { ref } from 'vue';

const props = defineProps({
    navigation:{type:Array, default:[]},
    warehouse: {type:Object, default:{}}
});

const warehouseForm = useForm({
    id:props.warehouse.id??null,
    title:props.warehouse.title??null,
    code:props.warehouse.code??null,
    address:props.warehouse.address??'',
    phone:props.warehouse.phone??'',
    caschier:props.warehouse.caschier??null,                   
    description:props.warehouse.description??'',
});

const storeWarehouse = ()=>{
    warehouseForm.post(route('admin.warehouses.store'), {
        preserveScroll:true,
        onSuccess:(e)=>console.log(warehouseForm.errors), 
        onError:(e)=>console.log(e)
    });
};

const updateContent = (content) => {
    warehouseForm.description=content;
};
</script>

<template>
    <Head title="Warehouse"/>
    <MarketLayout :navigation="navigation">
        <div>
            <div class="md:grid md:grid-cols-2 md:gap-2">
                <div class="mt-2">
                    <InputLabel for="title" value="Название" />
                    <TextInput
                        type="text"
                        id="title"
                        name="title"
                        class="w-full mt-1 block "
                        required
                        v-model="warehouseForm.title"
                        autocomplete="off"
                    />
                    <InputError class="ml-2" :message="warehouseForm.errors.title" />
                </div>
                <div class="mt-2">
                    <InputLabel for="code" value="Код" />
                    <TextInput
                        type="text"
                        id="code"
                        name="code"
                        class="w-full mt-1 block"
                        required
                        v-model="warehouseForm.code"
                        autocomplete="off"
                    />
                    <InputError class="ml-2" :message="warehouseForm.errors.code" />
                </div>
            </div>
            <div class="md:grid md:grid-cols-2 md:gap-2">
                <div class="mt-2">
                    <InputLabel for="address" value="Адрес" />
                    <TextInput
                        type="text"
                        id="address"
                        name="address"
                        class="w-full mt-1 block"
                        required
                        v-model="warehouseForm.address"
                        autocomplete="off"
                    />
                    <InputError class="ml-2" :message="warehouseForm.errors.address" />
                </div>
                <div class="mt-2">
                    <InputLabel for="phone" value="Телефон" />
                    <TextInput
                        type="text"
                        id="phone"
                        name="phone"
                        class="w-full mt-1 block"
                        required
                        v-model="warehouseForm.phone"
                        autocomplete="off"
                    />
                    <InputError class="ml-2" :message="warehouseForm.errors.phone" />
                </div>
            </div>
            <div class="mt-2">
                <span>Описание</span>
                <Tiptap :content="warehouseForm.description" @updateContent="updateContent"/>
                <InputError class="ml-2" :message="warehouseForm.errors.description" />
            </div>
            
            <div class="text-right mt-2">
                <Link :href="route('admin.warehouses.manage')" class="mr-2">
                    <SecondaryButton>Отменить</SecondaryButton>
                </Link>
                <PrimaryButton @click="storeWarehouse">Сохранить</PrimaryButton>
            </div>
        </div>
    </MarketLayout>
</template>
