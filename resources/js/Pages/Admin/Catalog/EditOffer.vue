<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import MarketLayout from '@/Layouts/MarketLayout.vue';
import InputLabel from '@/Components/UI/InputLabel.vue';
import TextInput from '@/Components/UI/TextInput.vue';
import InputError from '@/Components/UI/InputError.vue';
import SecondaryButton from '@/Components/UI/SecondaryButton.vue';
import PrimaryButton from '@/Components/UI/PrimaryButton.vue';
import Checkbox from '@/Components/UI/Checkbox.vue';
import MediaManage from './Partials/MediaManage.vue';

const props = defineProps({
    product: {type: Object, default:{}},
    navigation:{type:Array, default:[]},
    offer: {type: Object, default:{}},
});

const tradeOfferForm = useForm({
    id:props.offer.id??null,
    product_id:props.offer.product_id??null,
    title:props.offer.title??'',
    baseprice:((props.offer.baseprice??0)/100).toFixed(2),
    price:((props.offer.price??0)/100).toFixed(2),
    barcode:props.offer.barcode??'',
    art:props.offer.art??'',
    visibility:props.offer.visibility??true,
    to_caschier:props.offer.to_caschier??false,
    weight:props.offer.weight??null,
    length:props.offer.length??null,
    height:props.offer.height??null,
    width:props.offer.width??null,
    media:props.offer.media??[],
});

const toFloat = (val)=>{
    return parseFloat(val).toFixed(2);
};

const saveOffer = () => {
    tradeOfferForm.price = parseInt(tradeOfferForm.price*100);
    tradeOfferForm.baseprice = parseInt(tradeOfferForm.baseprice*100);
    tradeOfferForm.post(route('admin.products.offers.store'), {
        preserveScroll:true,
        // onSuccess:(e)=>console.log(e), 
        // onError:(e)=>console.log(e)
    });
};
</script>

<template>
    <MarketLayout :navigation="navigation">
        <Head title="Offer" />
        <div>
            <div class="md:grid md:grid-cols-2 md:gap-2">
                <div class="mt-2">
                    <InputLabel for="title" value="Название" />
                    <TextInput
                        type="text"
                        id="title"
                        name="title"
                        class="w-full mt-1 block"
                        required
                        v-model="tradeOfferForm.title"
                        autocomplete="off"
                    />
                    <InputError class="ml-2" :message="tradeOfferForm.errors.title" />
                </div>
                <div class="mt-2">
                    <InputLabel for="art" value="Артикул" />
                    <TextInput
                        type="text"
                        id="art"
                        name="art"
                        class="w-full mt-1 block"
                        required
                        v-model="tradeOfferForm.art"
                        autocomplete="off"
                    />
                    <InputError class="ml-2" :message="tradeOfferForm.errors.barcode" />
                </div>
                <div class="mt-2">
                    <InputLabel for="barcode" value="Штрихкод" />
                    <TextInput
                        type="text"
                        id="barcode"
                        name="barcode"
                        class="w-full mt-1 block"
                        required
                        v-model="tradeOfferForm.barcode"
                        autocomplete="off"
                    />
                    <InputError class="ml-2" :message="tradeOfferForm.errors.barcode" />
                </div>
                <div class="md:flex"> 
                    <div class="flex whitespace-nowrap mt-2 pt-6 md:mr-2">
                        <Checkbox name="visibility" id="visibility" v-model="tradeOfferForm.visibility" :checked="tradeOfferForm.visibility" title="Будет доступен в каталоге"/>
                        <InputLabel class="ml-2" for="visibility" value=" - Доступен в каталоге"/>
                        <InputError class="ml-2" :message="tradeOfferForm.errors.visibility" />
                    </div>
                    <div class="flex whitespace-nowrap mt-2 pt-6 md:ml-2">
                        <Checkbox name="sendToCaschier" id="sendToCaschier" v-model="tradeOfferForm.to_caschier" :checked="tradeOfferForm.to_caschier" title="Передавать информацию о позиции в кассовую систему"/>
                        <InputLabel class="ml-2" for="sendToCaschier" value=" - Передавать в кассу"/>
                        <InputError class="ml-2" :message="tradeOfferForm.errors.sendToCaschier" />
                    </div>
                </div>
            </div>
            <div class="md:grid md:grid-cols-2 md:gap-2">
                <div class="mt-2">
                    <InputLabel for="baseprice" value="Стоимость" title="Закупочная цена/себестоимость"/>
                    <TextInput
                        type="text"
                        id="baseprice"
                        name="price"
                        step="0.01"
                        class="w-full mt-1 block"
                        required
                        v-model="tradeOfferForm.baseprice"
                        autocomplete="off"
                        v-on:keyup="tradeOfferForm.baseprice=toFloat(tradeOfferForm.baseprice)"
                    />
                    <InputError class="ml-2" :message="tradeOfferForm.errors.baseprice" />
                </div>
                <div class="mt-2">
                    <InputLabel for="price" value="Цена"/>
                    <TextInput
                        type="text"
                        step="0.01"
                        id="price"
                        name="price"
                        class="w-full mt-1 block"
                        required
                        v-model="tradeOfferForm.price"
                        v-on:keyup="tradeOfferForm.price=toFloat(tradeOfferForm.price)"
                        autocomplete="off"
                    />
                    <InputError class="ml-2" :message="tradeOfferForm.errors.price" />
                </div>
            </div>
            <div class="md:grid md:grid-cols-2 md:gap-2">
                <div class="mt-2">
                    <InputLabel for="weight" value="Масса (г)"/>
                    <TextInput
                        type="number"
                        id="weight"
                        name="weight"
                        class="w-full mt-1 block"
                        v-model="tradeOfferForm.weight"
                        autocomplete="off"
                    />
                    <InputError class="ml-2" :message="tradeOfferForm.errors.weight" />
                </div>
                <div class="mt-2">
                    <InputLabel for="length" value="Длина (мм)"/>
                    <TextInput
                        type="number"
                        id="length"
                        name="length"
                        class="w-full mt-1 block"
                        v-model="tradeOfferForm.length"
                        autocomplete="off"
                    />
                    <InputError class="ml-2" :message="tradeOfferForm.errors.length" />
                </div>
                <div class="mt-2">
                    <InputLabel for="heigth" value="Высота (мм)"/>
                    <TextInput
                        type="number"
                        id="heigth"
                        name="heigth"
                        class="w-full mt-1 block"
                        v-model="tradeOfferForm.heigth"
                        autocomplete="off"
                    />
                    <InputError class="ml-2" :message="tradeOfferForm.errors.heigth" />
                </div>
                <div class="mt-2">
                    <InputLabel for="width" value="Ширина (мм)"/>
                    <TextInput
                        type="number"
                        id="width"
                        name="width"
                        class="w-full mt-1 block"
                        v-model="tradeOfferForm.width"
                        autocomplete="off"
                    />
                    <InputError class="ml-2" :message="tradeOfferForm.errors.width" />
                </div>
            </div>
            <div class="mt-2">
                <MediaManage :media="offer.media" :offer="offer.id"/>
            </div>
            <div class="text-right mt-2">
                <Link class="mr-2" :href="route('admin.products.edit', [props.product.code])">
                    <SecondaryButton>Отменить</SecondaryButton>
                </Link>
                <PrimaryButton class="mr-2" @click="saveOffer()">Сохранить</PrimaryButton>
            </div>
        </div>
    </MarketLayout>
</template>
