<script setup>
import { Link } from '@inertiajs/vue3';
import SecondaryButton from '@/Components/UI/SecondaryButton.vue';
import WarehouseLayout from './Partials/WarehouseLayout.vue';

const props = defineProps({
    warehouses: {type: Array, defalt:[]},
    navigation:{type:Array, default:[]},
    selectedWh: {type:Number, default:null},
});

</script>

<template>
    <WarehouseLayout :warehouses="warehouses" :navigation="navigation" :selectedWh="selectedWh">
        <Link :href="route('admin.warehouses.new')" title="Создать новый склад">
            <SecondaryButton><i class="ri-add-line"></i> Новый склад</SecondaryButton>
        </Link>
    </WarehouseLayout>
</template>
