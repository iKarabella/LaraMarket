import { createGlobalState, useStorage } from '@vueuse/core';

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