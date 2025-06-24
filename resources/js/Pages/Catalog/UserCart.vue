<script setup>
import PrimaryButton from '@/Components/UI/PrimaryButton.vue';
import UserCartPosition from './Partials/UserCartPosition.vue';
import FullLayout from '@/Layouts/FullLayout.vue'; 
import { ref, onMounted, computed, watch } from 'vue';
import axios from 'axios';
import { usercart, addToCart, removeFromCart } from '@/Mixins/UserCart.js';
import { useForm } from '@inertiajs/vue3';

const cartPositions = ref([]);

onMounted(() => {
    axios.post(route('catalog.usercart.check'), {
        positions:usercart.value
    }).then(e=>{
        usercart.value = e.data;
        getCartPositions();
    });
});

watch(usercart, async () => {
   getCartPositions();
})

const getCartPositions = () => {
    if(!usercart || !usercart.value || usercart.value.length<1) return false;

    axios.post(route('catalog.usercart.getPosition'), {
        positions:usercart.value
    })
    .then(e=>cartPositions.value = e.data);
};

const selected_count = computed(()=>{
    return usercart.value.filter(arr=>arr.toOrder).length;
});

const total_summ = computed(()=>{
    let total = 0;
    usercart.value.filter(arr=>arr.toOrder).forEach(p=>{
        let offer = cartPositions.value.find(arr=>arr.id==p.offer);
        if (offer) total+=offer.price*p.quantity;
    });
    return total.toFixed(2);
});

const positions_count_string = computed(()=>{
    let ret = '0 товаров', pos = usercart.value.filter(arr=>arr.toOrder);

    if(pos.length){
        let ld = pos.length % 10;
        if (ld>5 || ld==0 || (pos.length>10 && pos.length<16)) ret = pos.length+' товаров';
        else if (ld>1 && ld <5) ret = pos.length+' товара';
        else ret = pos.length+' товар'
    }

    return ret;
});

const orderCreate = ()=>{
    useForm({
        positions: usercart.value.filter(arr=>arr.toOrder)
    }).post(route('order.create'), {
        preserveScroll:true,
        // onSuccess:(e)=>{
        //     productForm.reset();
        // }, 
        onError:(e)=>console.log(e)
    });
};

</script>
<template>
    <Head title="Marketzone" />

    <FullLayout>
        <div class="py-12">
            <div class="sm:px-6 lg:px-8">
                <div class="md:flex md:min-w-[80rem] mx-auto w-fit">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2 mr-2 md:w-9/12">
                        <UserCartPosition v-for="(item, index) in usercart" 
                                         :key="index" 
                                         :positions="cartPositions"
                                         :cartItem="item"
                                         @addToCart="addToCart"
                                         @removeFromCart="removeFromCart"
                        />
                        <div v-if="cartPositions.length<1">
                            В корзине пока нет товаров :-(
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg md:w-3/12 h-fit">
                        <div class="text-center m-4">
                            <PrimaryButton class="w-full" :disabled="selected_count<1" @click="orderCreate()">Оформить заказ</PrimaryButton>
                            <div>Доступные способы доставки можно будет выбрать при оформлении заказа.</div>
                        </div>
                        <div v-show="selected_count>0" class="w-full border-t-gray-200 p-4">
                            <div class="flex whitespace-nowrap justify-between">
                                <div>Ваша корзина</div>
                                <div>{{ positions_count_string }}</div>
                            </div>
                            <div class="flex whitespace-nowrap justify-between">
                                <div>Товары ({{selected_count}})</div>
                                <div>{{total_summ}} ₽</div>
                            </div>
                        </div>
                        <div v-show="selected_count<1" class="w-full p-4 text-gray-600">
                            <div class="flex items-start justify-start rounded-md bg-gray-100">
                                <i class="ri-information-2-line"></i>
                                <div class="ml-2">Выберите товары, чтобы перейти к оформлению заказа</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </FullLayout>
</template>