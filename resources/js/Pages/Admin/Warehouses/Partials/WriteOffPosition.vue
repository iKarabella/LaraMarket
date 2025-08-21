<script setup>
import SecondaryButton from '@/Components/UI/SecondaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    position:{type:Object, default:{}},
    order:{type:Object, default:null},
    warehouse:{type:Object, default:null},
});

const writeOff = computed(()=>{
    return props.order.reserved_products.findIndex((arr)=> {return arr.product_id == props.position.position && arr.offer_id == props.position.offer} )>-1 ? true : false;
});

const markWh = ()=>{
    if (writeOff.value) return false;
    useForm({
        order_id: props.order.id,
        offer_id: props.position.offer,
        product_id: props.position.position,
        quantity: props.position.quantity,
        product_title: 'Purina ProPlan Sterilised Adult',
        warehouse_id: props.warehouse.id
    }).post(route('admin.warehouses.order.markWh', [props.warehouse.code, props.order.uuid]), {
        preserveScroll: true,
        onSuccess: (e)=>console.log(e),
        onError: (e)=>console.log(e)
    });
};
</script> 

<template>
    <div>
        <SecondaryButton v-if="writeOff" :disabled="true">Списано</SecondaryButton>
        <SecondaryButton v-else @click="markWh()">Списать</SecondaryButton>
    </div>
</template>