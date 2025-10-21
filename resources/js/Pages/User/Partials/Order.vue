<script setup>
import { ref } from 'vue';

defineProps({
    order:{type:Object, default:{}},
});

const openBody = ref(false);

</script>
<template>
    <div>
        <div class="md:grid md:grid-cols-3 md:gap-2 cursor-pointer" title="Открыть" @click="openBody=!openBody">
            <div>{{ order.created_at }}</div>
            <div>{{ (order.amount/100).toFixed(2) }}</div>
            <div>{{ order.status.value }}</div>
        </div>
        <div v-show="openBody">
            <div v-for="(position, index) in order.body" :key="index" class="md:grid md:grid-cols-6 gap-2">
                <div class="md:col-span-3">{{ position.product_title }} {{ position.offer_title }}</div>
                <div>{{ position.quantity }} {{ position.measure }}</div>
                <div>{{ (position.price/100).toFixed(2) }} ₽</div>
                <div>{{ (position.total/100).toFixed(2) }} ₽</div>
            </div>
        </div>
    </div>
</template>