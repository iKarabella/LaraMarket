<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/UI/PrimaryButton.vue';
import WarehouseLayout from './Partials/WarehouseLayout.vue';
import Pagination from '@/Components/Pagination';
import { computed, } from 'vue';
import WarehouseAct from './Partials/WarehouseAct.vue';

const props = defineProps({
    warehouses: {type: Array, defalt:[]},
    navigation:{type:Array, default:[]},
    selectedWh: {type:Number, default:null},
    acts: {type:Object, default:{}}
});

const currentWhInfo = computed(()=>{
    return props.warehouses.find(arr=>arr.id==props.selectedWh)??{};
});

</script>

<template>
    <WarehouseLayout :warehouses="warehouses" :navigation="navigation" :selectedWh="selectedWh" :currentBlock="'writeOff'">
        <div class="mt-2">
            <div class="md:grid md:grid-cols-2 md:gap-2">
                <div>Списания со склада</div>
                <div class="text-right">
                    <Link :href="route('admin.warehouses.newwriteoff', [currentWhInfo.code])">
                        <PrimaryButton>
                            Списать товары
                        </PrimaryButton>
                    </Link>
                </div>
            </div>
            <div class="mt-2">
                <div>
                    <WarehouseAct v-for="(act, index) in acts.data" :act="act" v-key="index" class="mt-2"/>
                </div>
                <Pagination :paginator="acts.meta"/>
            </div>
        </div>
    </WarehouseLayout>
    
</template>
