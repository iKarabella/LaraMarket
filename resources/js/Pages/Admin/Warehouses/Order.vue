<script setup>
import { useForm } from '@inertiajs/vue3';
import SecondaryButton from '@/Components/UI/SecondaryButton.vue';
import WarehouseLayout from './Partials/WarehouseLayout.vue';
import { computed, onBeforeMount, ref } from 'vue';
import DangerButton from '@/Components/UI/DangerButton.vue';
import PrimaryButton from '@/Components/UI/PrimaryButton.vue';
import '@vuepic/vue-datepicker/dist/main.css';
import OrderStatus from './Partials/OrderStatus.vue';
import Comments from './Partials/Comments.vue';
import InputLabel from '@/Components/UI/InputLabel.vue';
import TextInput from '@/Components/UI/TextInput.vue';
import InputError from '@/Components/UI/InputError.vue';
import Modal from '@/Components/Modals/MainModal.vue';
import WriteOffPosition from './Partials/WriteOffPosition.vue';

const props = defineProps({
    warehouses: {type: Object, defalt:[]},
    selectedWh: {type:Number, default:null},
    navigation:{type:Array, default:[]},
    order: {type:Object, default:[]},
    shippingFields:{type:Object, default:{}}
});

const setStatusForm = useForm({
    order_id:props.order.id,
    status:null,
});

let shippingForm = {};
const requiredShippingForm = ref(false);
const sendToShippingModal = ref(null);

onBeforeMount(() => {
    let form = {
        orderId: props.order.id,
    };
    for (var key in props.shippingFields) {
        form[key]=props.shippingFields[key].default;
        requiredShippingForm.value = true;
    }
    shippingForm = useForm(form);
});

const currentWhInfo = computed(()=>{
    return props.warehouses.find(arr=>arr.id==props.selectedWh)??{};
});

const canPickup = computed(()=>{ //статус "готов к выдаче", при самовывозе
    return props.order.status==8 && writeOffCompleted.value;
});

const canSent = computed(()=>{ //при передаче заказа в транспортную компанию или курьеру
    return [8,10].includes(props.order.status) && writeOffCompleted.value;
});

const writeOffCompleted = computed(()=>{
    return 0 > props.order.body.findIndex((position)=>{
        return props.order.reserved_products.findIndex((arr)=> {return arr.product_id == position.position && arr.offer_id == position.offer;} )<0;
    })
});

const readyForPickup = ()=>{
    setStatusForm.status = 10;
    setStatusForm.post(route('admin.warehouses.order.readyForPickup', [currentWhInfo.value.code, props.order.uuid]), {
        preserveScroll: true,
        onSuccess: (e)=>console.log(e),
        onError: (e)=>console.log(e)
    });
};

const orderIssued = ()=>{
    setStatusForm.status = 12;
    setStatusForm.post(route('admin.warehouses.order.issued', [currentWhInfo.value.code, props.order.uuid]), {
        preserveScroll: true,
        onSuccess: (e)=>console.log(e),
        onError: (e)=>console.log(e)
    });
};

const sendToShipping = ()=>{
    if (!canSent.value) return;

    if (requiredShippingForm.value && !sendToShippingModal.value) sendToShippingModal.value=true;
    else 
    {
        shippingForm.post(route('admin.warehouses.order.sentToShipping', [currentWhInfo.value.code, props.order.uuid]), {
            preserveScroll: true,
            onSuccess: () => {
                if (sendToShippingModal.value) sendToShippingModal.value=false;
                shippingForm.reset();
            },
            onError: (e) => console.log(e)
        });
    }
};

</script>

<template>
    <WarehouseLayout :warehouses="warehouses" :navigation="navigation" :selectedWh="selectedWh" :currentBlock="'orders'">
        <Head title="Orders" />
        <div>
            <div class="md:grid md:grid-cols-3 md:gap-2">
                <div class="rounded-md border border-gray-100 bg-gray-100">
                    <div>Клиент:</div>
                    <div>{{ order.customer.surname }} {{ order.customer.name }} {{ order.customer.patronymic }}</div>
                    <div>Телефон: {{ order.customer.phone }}</div>
                </div>
                <div class="rounded-md border border-gray-100 bg-gray-100" v-if="order.delivery.warehouse">
                    <div>Самовывоз: {{ order.delivery.warehouse }}</div>
                </div>
                <div class="rounded-md border border-gray-100 bg-gray-100" v-else>
                    <div>Доставка:</div>
                    <div v-show="order.delivery.region">Регион: {{ order.delivery.region }}</div>
                    <div v-show="order.delivery.city">Город:{{ order.delivery.city }}</div>
                    <div>Улица:{{ order.delivery.street }}</div>
                    <div>Дом:{{ order.delivery.house }}</div>
                    <div>Квартира:{{ order.delivery.apartment }}</div>
                </div>
                <div class="rounded-md border border-gray-100 bg-gray-100">
                    <div class="text-center items-center mx-4 border-b-[1px] p-2 border-gray-400">
                        Статус:
                        <OrderStatus :status="order.status_info"/>
                    </div>
                    <div class="text-center items-center">
                        <div class="font-semibold italic text-gray-700 text-xs">
                            <div class="my-1">Установить в статус в</div>
                            <div class="grid grid-cols-1 mb-2 w-fit mx-auto gap-1 justify-items-center">
                                <PrimaryButton v-show="canPickup" class="w-full" @click="readyForPickup()">Готов к выдаче</PrimaryButton>
                                <PrimaryButton v-show="order.status==10" @click="orderIssued()" class="w-full">Выдан</PrimaryButton>
                            </div>
                            <InputError :message="setStatusForm.errors.order_id" class="mt-2" />
                        </div>
                        <div class="font-semibold italic text-gray-700 text-xs">
                            <div class="my-1">Действия</div>
                            <div class="grid grid-cols-1 mb-2 w-fit mx-auto gap-1 justify-items-center">
                                <PrimaryButton v-show="canSent" class="w-full" @click="sendToShipping()">Передать в доставку</PrimaryButton>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="rounded-md border border-gray-100 bg-gray-100 mt-2 pl-2">
                <div>
                    Состав заказа:
                </div>
                <div>
                    <div v-for="(position, index) in order.body" :key="index" class="md:grid md:grid-cols-9 md:gap-2 mb-2 border rounded-md border-gray-100 hover:border-gray-200">
                        <div>{{ index+1 }}</div>
                        <div class="md:col-span-5">{{ position.product_title }}, {{ position.offer_title }}</div>
                        <div>
                            <div class="grid grid-cols-2 gap-0 w-fit">
                                <div>{{ position.quantity }}</div>
                                <div class="text-left">{{ position.measure }}</div>
                            </div>
                        </div>
                        <div class="px-2 md:col-span-2 relative flex whitespace-nowrap justify-end">
                            <WriteOffPosition :position="position" 
                                              :order="order"
                                              :warehouse="currentWhInfo"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <Comments class="rounded-md border border-gray-100 bg-gray-100 mt-2 pl-2" 
                     :comments="order.comments" 
                     :order="order" 
                     :active="![11,12].includes(order.status)"
            />
        </div>
        <Modal :show="sendToShippingModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Передать заказ в доставку
                </h2>
                
                <div v-for="(field, key) in shippingFields" v-key="key" class="mt-2">
                    <template v-if="field.type=='string'">
                        <InputLabel :for="key" :required="field.required">{{ field.label }}</InputLabel>
                        <TextInput
                            :id="key"
                            :ref="key"
                            :required="field.required"
                            v-model="shippingForm[key]"
                            type="text"
                            class="mt-1 block w-3/4"
                        />
                    </template>
                    <InputError :message="shippingForm.errors[key]" class="mt-2" />
                </div>

                <div class="flex w-full justify-end mt-2">
                    <SecondaryButton @click="sendToShippingModal=false" class="mr-2">Закрыть</SecondaryButton>
                    <PrimaryButton @click="sendToShipping()">Отправить</PrimaryButton>
                </div>
                
                <InputError :message="shippingForm.errors.orderId" class="mt-2" />
            </div>
        </Modal>
    </WarehouseLayout>
</template>
