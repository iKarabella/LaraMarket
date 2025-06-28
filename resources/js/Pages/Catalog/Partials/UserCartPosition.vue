<script setup>
import Checkbox from '@/Components/UI/Checkbox.vue';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    positions: {type: Array, default:[]}, 
    cartItem: {type: Object, default:{}}
});

const emit = defineEmits(['addToCart', 'removeFromCart']);

const changeCart = (remove=false) => {
    if(!remove && position.value.available<=props.cartItem.quantity) return false;
    emit(remove?'removeFromCart':'addToCart', {position:position.value.product_id, offer:position.value.id});
}

const noMore = computed(()=>{
    return position.value.available<=props.cartItem.quantity;
});

const position = computed(()=>{
    let find = props.positions.find(arr=>{return arr.id == props.cartItem.offer;});

    if (!find) return false;

    return {
        id:find.id,
        media:[...find.media, ...find.product.media],
        product_title: find.product.title,
        product_id:find.product.id,
        title:find.title,
        product_code:find.product.code,
        product_offersign:find.product.offersign,
        price:find.price,
        measure:find.product.measure.value,
        available:find.available
    };
});

const total_pos = computed(()=>{
    let total = position.value.price*props.cartItem.quantity;
    return total%100 != 0 ? (total/100).toFixed(2) : (total/100);
});
</script>

<template>
    <div class="w-full md:grid md:grid-cols-6 p-2" v-if="position">
        <div v-if="position.media" class="max-w-[100px]">
            <label class="relative">
                <img :src="position.media[0].preview" width="100"/>
                <Checkbox v-model="cartItem.toOrder" name="checkToOrder" class="absolute top-0 left-0" :checked="cartItem.toOrder==true"/>
            </label>
        </div>
        <div class="w-full md:col-span-3">
            <Link :href="route('catalog.product', [position.product_code])">
                {{ position.product_title }}
            </Link>
            <div>{{ position.product_offersign }}: {{ position.title }}</div>
        </div>
        <div>
            {{ total_pos }} ₽
        </div>
        <div>
            <div class="flex justify-between items-center relative whitespace-nowrap font-semibold tracking-widest 
                        transition ease-in-out duration-150 min-w-32">
                <div class="rounded-md cursor-pointer w-9 h-9 flex justify-center items-center bg-indigo-200 text-indigo-700 hover:bg-indigo-100" 
                    @click="changeCart(true)" 
                     title="-1"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"><path fill="currentColor" d="M5 11a1 1 0 1 0 0 2h14a1 1 0 1 0 0-2z"></path></svg>
                </div>
                <span>{{ cartItem.quantity }}</span>
                <div class="rounded-md w-9 h-9 flex justify-center items-center"
                    :class="{'bg-gray-100 text-gray-500':noMore, 'bg-indigo-200 hover:bg-indigo-100 text-indigo-700 cursor-pointer':!noMore}"
                    @click="changeCart()" title="+1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"><path fill="currentColor" d="M12 4a1 1 0 0 0-1 1v6H5a1 1 0 1 0 0 2h6v6a1 1 0 1 0 2 0v-6h6a1 1 0 1 0 0-2h-6V5a1 1 0 0 0-1-1"></path></svg>
                </div>
            </div>
            <div v-show="position.quantity>1">
                {{ position.price }} ₽ / {{ position.measure }}
            </div>
        </div>
    </div>
</template>