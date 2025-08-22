<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import MarketLayout from '@/Layouts/MarketLayout.vue';
import SecondaryButton from '@/Components/UI/SecondaryButton.vue';
import {computed, ref, watch} from 'vue';

const props = defineProps({
    warehouses: {type: Array, defalt:[]},
    navigation:{type:Array, default:[]},
    selectedWh: {type:Number, default:null},
    currentBlock:{type:String, default:null},
});

const selectedWh = ref(props.selectedWh);

const selectedWhInfo = computed(()=>{
    let wh = props.warehouses.find((arr)=>{return arr.id==selectedWh.value;});
    return wh ? wh : {code:null};
});

const relocation = (destignation)=>{
    if(destignation=='receipt' && selectedWh!=null){
        router.get(route('admin.warehouses.receipt', [selectedWhInfo.value.code]));
    }
    else if(destignation=='editWh' && selectedWh!=null) {
        router.get(route('admin.warehouses.edit', [selectedWhInfo.value.code]));
    }
    else if(destignation=='orders' && selectedWh!=null) {
        router.get(route('admin.warehouses.orders', [selectedWhInfo.value.code]));
    }
    else if(destignation=='stockIn' && selectedWh!=null) {
        router.get(route('admin.warehouses.stock_in', [selectedWhInfo.value.code]));
    }
};

watch(selectedWh, async (newVal) => {
    if (props.currentBlock==null) router.get(route('admin.warehouses.manage', [selectedWhInfo.value.code]));
    else if (props.currentBlock=='receipt') router.get(route('admin.warehouses.receipt', [selectedWhInfo.value.code]));
})
</script>

<template>
    <Head title="Warehouse" />
    <MarketLayout :navigation="navigation">
        <div>
            <div class="flex w-full">
                <div class="mt-2 w-full">
                    <div>
                        <SecondaryButton @click="relocation('receipt')" :disabled="selectedWh==null" :selected="currentBlock=='receipt'" class="mr-2">
                            Поступление
                        </SecondaryButton>
                        <SecondaryButton :disabled="selectedWh==null" :selected="currentBlock=='writeOff'" class="mr-2">
                            Списание
                        </SecondaryButton>
                        <SecondaryButton @click="relocation('stockIn')" :disabled="selectedWh==null" :selected="currentBlock=='stockIn'" class="mr-2">
                            Наличие
                        </SecondaryButton>
                        <SecondaryButton @click="relocation('orders')" :disabled="selectedWh==null" :selected="currentBlock=='orders'">
                            Заказы
                        </SecondaryButton>
                    </div>
                </div>
                <div class="mt-2 whitespace-nowrap">
                    <select v-model="selectedWh" class="mr-2 whitespace-nowrap px-4 py-2 border rounded-md font-semibold text-xs uppercase tracking-widest shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150  bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-500 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                        <option v-for="wh in warehouses" :key="wh.id" :value="wh.id">{{wh.title}}</option>
                    </select>
                    <SecondaryButton :disabled="selectedWh==null" @click="relocation('editWh')" class="mr-2"><i class="ri-edit-2-fill"></i></SecondaryButton>
                </div>
            </div>  
            <div class="mt-2">
                <slot></slot>
            </div>
        </div>
    </MarketLayout>
</template>