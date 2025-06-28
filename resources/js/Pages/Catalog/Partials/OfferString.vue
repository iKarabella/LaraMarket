<script setup>
import { computed } from 'vue';

const props = defineProps({
    offer: {type:Object, default:{}},
    active: {type:Boolean, default:false}
});

const offer_price = computed(()=>{
    return props.offer.price%100 != 0 ? (props.offer.price/100).toFixed(2) : (props.offer.price/100);
});
</script>

<template>
    <div :class="{'border-gray-200':active, 'border-gray-200/10':!active}"
          class="flex items-center justify-between p-1 mb-2 hover:bg-gray-200 rounded border cursor-pointer"
          title="Выбрать"
    >
        <div>{{ offer.title }}</div>
        <div class="whitespace-nowrap">{{ offer_price }} ₽</div>
    </div>
</template>