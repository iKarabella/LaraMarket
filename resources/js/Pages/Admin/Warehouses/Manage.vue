<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MarketLayout from '@/Layouts/MarketLayout.vue';
import SecondaryButton from '@/Components/UI/SecondaryButton.vue';
import { ref } from 'vue';

const props = defineProps({
    warehouses: {type: Array, defalt:[]},
    navigation:{type:Array, default:[]},
    selectedWh: {type:Number, default:null},
});

const selectedWh = ref(props.selectedWh);

</script>

<template>
    <Head title="Warehouse" />
    <MarketLayout :navigation="navigation">
        <div>
            <div class="flex w-full">
                <div class="mt-2 w-full">
                    <div>
                        <SecondaryButton :disable="selectedWh==null" class="mr-2">
                            Поступление
                        </SecondaryButton>
                        <SecondaryButton :disable="selectedWh==null" class="mr-2">
                            Списание
                        </SecondaryButton>
                        <SecondaryButton :disable="selectedWh==null" class="mr-2">
                            Наличие
                        </SecondaryButton>
                        <SecondaryButton :disable="selectedWh==null">
                            Заказы
                        </SecondaryButton>
                    </div>
                </div>
                <div class="mt-2 whitespace-nowrap">
                    <select v-model="selectedWh" class="mr-2 whitespace-nowrap px-4 py-2 border rounded-md font-semibold text-xs uppercase tracking-widest shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150  bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-500 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                        <option v-for="wh in warehouses" :key="wh.id" :value="wh.id">{{wh.title}}</option>
                    </select>
                    <SecondaryButton :disable="selectedWh==null" @click="editWh" class="mr-2"><i class="ri-edit-2-fill"></i></SecondaryButton>
                    <Link :href="route('admin.warehouses.edit')" title="Создать новый склад">
                        <SecondaryButton :disable="selectedWh==null"><i class="ri-add-line"></i></SecondaryButton>
                    </Link>
                </div>
            </div>  
            <div class="mt-2">
                //
            </div>
        </div>
    </MarketLayout>
</template>
