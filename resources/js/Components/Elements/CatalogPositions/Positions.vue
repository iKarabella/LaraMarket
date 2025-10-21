<script setup>
import Position from './Position.vue';
import { ref } from 'vue';
import NotifyAboutAdmission from '@/Components/Elements/NotifyAboutAdmission.vue';
import { usercart, addToCart, removeFromCart } from '@/Mixins/UserCart.js';

const props = defineProps({
    positions:{type:Array, default:[]}
});

const showNotifyAboutAdmission = ref(null);

const notifyAboutAdmission = (i) => {
    if (!i.offer || !i.position) return false;

    showNotifyAboutAdmission.value = {
        offer_id:i.offer,
        product_id:i.position
    };
};
const closeNotify = ()=>{
    showNotifyAboutAdmission.value = null;
};
</script>

<template>
    <div class="p-2 lg:grid lg:grid-cols-2 xl:grid-cols-3 lg:gap-2">
        <Position v-for="position in positions" 
                 :key="position.id" 
                 :position="position" 
                 :usercart="usercart" 
                 @addToCart="addToCart" 
                 @removeFromCart="removeFromCart"
                 @notifyAboutAdmission="notifyAboutAdmission"
        />
    </div>
    <NotifyAboutAdmission :show="showNotifyAboutAdmission" @closeNotify="closeNotify" :auth="$page.props.auth"/>
</template>