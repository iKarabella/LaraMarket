<script setup>
import PrimaryButton from '@/Components/UI/PrimaryButton.vue';
import UserCartPosition from './Partials/UserCartPosition.vue';
import FullLayout from '@/Layouts/FullLayout.vue'; 
import { ref, onMounted, computed, watch } from 'vue';
import axios from 'axios';
import { usercart, addToCart, removeFromCart, selected_count, positions_count_string } from '@/Mixins/UserCart.js';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modals/MainModal.vue';

const cartPositions = ref([]);

const props = defineProps({
    create_order_failed:{type:Array, default:null}
});

const errorModal = ref(props.create_order_failed);

onMounted(() => {
    if (usercart && usercart.value && usercart.value.length)
    {
        axios.post(route('catalog.usercart.check'), {
            positions:usercart.value
        }).then(e=>{
            usercart.value = e.data;
        });
    }
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

const total_summ = computed(()=>{
    let total = 0;
    usercart.value.filter(arr=>arr.toOrder).forEach(p=>{
        let offer = cartPositions.value.find(arr=>arr.id==p.offer);
        if (offer) total+=offer.price*p.quantity;
    });
    return total.toFixed(2);
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

const modalClose = ()=>{
    errorModal.value=null;
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
                        <div v-if="usercart.length<1">
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
        <Modal :show="errorModal" ref="modalWindow" @close="modalClose">
            <div class="p-6">
                <div>При создании заказа произошла ошибка.</div>
                <div class="text-center mt-2">
                    <PrimaryButton @click="modalClose">Закрыть</PrimaryButton>
                </div>
            </div>
        </Modal>
    </FullLayout>
</template>