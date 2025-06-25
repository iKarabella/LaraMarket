import { createGlobalState, useStorage } from '@vueuse/core';
import { computed } from 'vue';

export const global_user_cart = createGlobalState(
    () => useStorage('user_cart', []),
);

export const usercart = global_user_cart();

export const addToCart = (i) => {
    let find = usercart.value.findIndex(a=>a.position == i.position && a.offer == i.offer);

    if (find>-1) usercart.value[find].quantity++;
    else usercart.value.push({position:i.position, offer:i.offer, quantity:1, toOrder:true});
};

export const removeFromCart = (i)=>{ 
    let find = usercart.value.findIndex(a=>a.position == i.position && a.offer == i.offer);

    if (find>-1) {
        if (usercart.value[find].quantity>1) usercart.value[find].quantity--;
        else usercart.value.splice(find, 1);
    }
};

export const selected_count = computed(()=>{
    return usercart.value.filter(arr=>arr.toOrder).length;
});

export const positions_count_string = computed(()=>{
    let ret = '0 товаров', pos = usercart.value.filter(arr=>arr.toOrder);

    if(pos.length){
        let ld = pos.length % 10;
        if (ld>5 || ld==0 || (pos.length>10 && pos.length<16)) ret = pos.length+' товаров';
        else if (ld>1 && ld <5) ret = pos.length+' товара';
        else ret = pos.length+' товар'
    }

    return ret;
});