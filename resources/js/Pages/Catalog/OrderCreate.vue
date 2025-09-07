<script setup>
import InputError from '@/Components/UI/InputError.vue';
import InputLabel from '@/Components/UI/InputLabel.vue';
import PrimaryButton from '@/Components/UI/PrimaryButton.vue';
import TextInput from '@/Components/UI/TextInput.vue';
import FullLayout from '@/Layouts/FullLayout.vue';
import { useForm } from '@inertiajs/vue3';
import {PhoneFormat} from '@/Mixins/PhoneFormat.js';
import { computed, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { usercart } from '@/Mixins/UserCart.js';

const props = defineProps({
    order:{type:Object},
    order_uuid:{type:String, default:null},
    remove_from_cart:{type:Array, default:[]}
});

watch(props, async () => {
    if (props.remove_from_cart && props.remove_from_cart.length)
    {
        usercart.value = usercart.value.filter((arr)=>{
            return !props.remove_from_cart.includes(arr.offer);
        });
    }
    if (props.order_uuid!=null) router.get(route('order.show', [props.order_uuid]));
});

const orderForm = useForm({
    delivery:{
        region:props.order.delivery.region??'',
        city:props.order.delivery.city??'',
        street:props.order.delivery.street??'',
        house:props.order.delivery.house??'',
        apartment:props.order.delivery.apartment??'',
        comment:props.order.delivery.comment??'',
    },
    customer:{
        name:props.order.customer.name??'',
        patronymic:props.order.customer.patronymic??'',
        surname:props.order.customer.surname??'',
        phone:PhoneFormat(props.order.customer.phone)??'',
    },
    comment:props.order.comment??'',
    code:props.order.code??''
});

const order_total_sum = computed(()=>{
    return props.order.total_sum%100 != 0 ? (props.order.total_sum/100).toFixed(2) : props.order.total_sum/100;
});

const positions_count_string = computed(()=>{
    let ld = props.order.positions.length % 10;
                    
    if (ld>5 || ld==0 || (props.order.positions.length>10 && this.order.positions.length<16)) return props.order.positions.length+' товаров';
    else if (ld>1 && ld <5) return props.order.positions.length+' товара';
    else return props.order.positions.length+' товар';
});

const checkFields = computed(()=>{
    return (
        orderForm.customer.name.length < 2 ||
        orderForm.customer.surname.length < 2 ||
        orderForm.customer.phone.length < 10 ||
        orderForm.delivery.city.length < 2 ||
        orderForm.delivery.street.length < 2 ||
        orderForm.delivery.house.length < 2
    );
});

const createOrder = ()=>{
    if (checkFields.value) return false;
    orderForm.post(route('order.store'), {
        preserveScroll:true,
        onError:(e)=>router.get(route('user.cart'))
    });
};
</script>
<template>
    <Head title="Создание заказа" />

    <FullLayout>
        <div class="py-4">
            <div class="sm:px-6 lg:px-8">
                <div class="md:flex md:min-w-[80rem] mx-auto w-fit" v-if="order">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-2 md:w-9/12 md:mr-2">
                        <div class="p-2 border border-gray-200 rounded-md relative mb-2">
                            <span class="px-2 absolute -top-2 left-4 rounded bg-white text-sm text-gray-500 font-semibold italic">Адрес для доставки:</span>
                            <div class="md:grid md:grid-cols-2 md:gap-2">
                                <div class="mt-4">
                                    <InputLabel for="region" value="Регион" />
                
                                    <TextInput
                                        type="text"
                                        class="w-full"
                                        id="region"
                                        name="region"
                                        v-model="orderForm.delivery.region"
                                        required
                                        autocomplete="off"
                                    />
                                    <InputError class="ml-2" :message="orderForm.errors['delivery.region']" />
                                </div>
                                
                                <div class="mt-4">
                                    <InputLabel for="city" value="Город" />
                
                                    <TextInput
                                        type="text"
                                        class="w-full"
                                        id="city"
                                        name="city"
                                        v-model="orderForm.delivery.city"
                                        required
                                        autocomplete="off"
                                    />
                                    <InputError class="ml-2" :message="orderForm.errors['delivery.city']" />
                                </div>
                            </div>
                            
                            <div class="md:grid md:grid-cols-4 md:gap-2">
                                <div class="mt-4 col-span-2">
                                    <InputLabel for="street" value="Улица" />
                
                                    <TextInput
                                        type="text"
                                        class="w-full"
                                        id="street"
                                        name="street"
                                        v-model="orderForm.delivery.street"
                                        required
                                        autocomplete="off"
                                    />
                                    <InputError class="ml-2" :message="orderForm.errors['delivery.region.street']" />
                                </div>
                                
                                <div class="mt-4">
                                    <InputLabel for="house" value="Дом" />
                
                                    <TextInput
                                        type="text"
                                        class="w-full"
                                        id="house"
                                        name="house"
                                        v-model="orderForm.delivery.house"
                                        required
                                        autocomplete="off"
                                    />
                                    <InputError class="ml-2" :message="orderForm.errors['delivery.house']" />
                                </div>
                                
                                <div class="mt-4">
                                    <InputLabel for="apartment" value="Квартира" />
                
                                    <TextInput
                                        type="text"
                                        class="w-full"
                                        id="apartment"
                                        name="apartment"
                                        v-model="orderForm.delivery.apartment"
                                        required
                                        autocomplete="off"
                                    />
    
                                    <InputError class="ml-2" :message="orderForm.errors['delivery.apartment']" />
                                </div>
                            </div>                            
                        </div>
                        <div class="p-2 border border-gray-200 relative rounded-md">
                            <span class="px-2 absolute -top-2 left-4 rounded bg-white text-sm text-gray-500 font-semibold italic">Получатель:</span>
                            <div class="md:grid md:grid-cols-2 md:gap-2">
                                <div class="mt-4">
                                    <InputLabel for="phone" value="Телефон" />
                
                                    <TextInput
                                        type="text"
                                        class="w-full"
                                        id="phone"
                                        name="phone"
                                        v-model="orderForm.customer.phone"
                                        v-on:keyup="orderForm.customer.phone=PhoneFormat(orderForm.customer.phone)"
                                        required
                                        autocomplete="off"
                                    />
                                    <InputError class="ml-2" :message="orderForm.errors['customer.phone']" />
                                </div>

                                <div class="mt-4">
                                    <InputLabel for="surname" value="Фамилия" />
                
                                    <TextInput
                                        type="text"
                                        class="w-full"
                                        id="surname"
                                        name="surname"
                                        v-model="orderForm.customer.surname"
                                        required
                                        autocomplete="off"
                                    />
                                    <InputError class="ml-2" :message="orderForm.errors['customer.surname']" />
                                </div>
    
                                <div class="mt-4">
                                    <InputLabel for="name" value="Имя" />
                
                                    <TextInput
                                        type="text"
                                        class="w-full"
                                        id="name"
                                        name="name"
                                        v-model="orderForm.customer.name"
                                        required
                                        autocomplete="off"
                                    />
                                    <InputError class="ml-2" :message="orderForm.errors['customer.name']" />
                                </div>
                                
                                <div class="mt-4">
                                    <InputLabel for="patronymic" value="Отчество" />
                
                                    <TextInput
                                        type="text"
                                        class="w-full"
                                        id="patronymic"
                                        name="patronymic"
                                        v-model="orderForm.customer.patronymic"
                                        required
                                        autocomplete="off"
                                    />
                                    <InputError class="ml-2" :message="orderForm.errors['customer.patronymic']" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg md:w-3/12 h-fit">
                        <div class="text-center m-4">
                            <PrimaryButton class="w-full" :disabled="checkFields" @click="createOrder">Оформить заказ</PrimaryButton>
                        </div>
                        
                        <div class="w-full border-t-gray-200 p-4">
                            <div class="flex whitespace-nowrap justify-between">
                                <div>Ваш заказ</div>
                                <div>{{ positions_count_string }}</div>
                            </div>
                            <div class="flex whitespace-nowrap justify-between">
                                <div>Товары ({{ order.positions.length }})</div>
                                <div>{{ order_total_sum }} ₽</div>
                            </div>
                            <InputError class="text-right" :message="orderForm.errors['order']"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </FullLayout>
</template>