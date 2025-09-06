<script setup>
import MarketLayout from '@/Layouts/MarketLayout.vue';
import { nextTick, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination';
import SecondaryButton from '@/Components/UI/SecondaryButton.vue';
import PrimaryButton from '@/Components/UI/PrimaryButton.vue';
import DangerButton from '@/Components/UI/DangerButton.vue';
import InputError from '@/Components/UI/InputError.vue';
import InputLabel from '@/Components/UI/InputLabel.vue';
import Checkbox from '@/Components/UI/Checkbox.vue';
import Modal from '@/Components/Modals/MainModal.vue';
import { Link } from '@inertiajs/vue3';
import '@vuepic/vue-datepicker/dist/main.css';

const props = defineProps({
    orders: {type:Array, default:[]},
    navigation:{type:Array, default:[]},
});

const showOrderBodyId = ref(null);
const showFinishModal = ref(false);
const shippingForm = useForm({id:null, comment:null, type:null, returned:true});
const commentArea = ref(null);

const closeModal = ()=>{
    showFinishModal.value=false;
    shippingForm.reset();
    shippingForm.errors=[];
};

const takeShipping = (id) => {
    let shipping = props.orders.data.find(arr=>arr.id==id);
    if (!shipping || shipping.courier) return; 

    shippingForm.id = shipping.id;
    shippingForm.post(route('admin.delivery.takeToDelivery'), {
        preserveScroll: true,
        onSuccess: () => shippingForm.reset(),
        onError: (e) => console.log(e)
    });
};

const finiSend = ()=>{
    let sendTo = null;

    if (!shippingForm.id || !shippingForm.type) return;
    else if(shippingForm.type=='delivered') sendTo = route('admin.delivery.delivered');
    else if(shippingForm.type=='cancelled') sendTo = route('admin.delivery.cancelled');
    else if(shippingForm.type=='comment') sendTo = route('admin.delivery.addComment');
    else return;

    shippingForm.post(sendTo, {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: (e) => console.log(e)
    });
};

const finiShipping = (id, type) => {
    let shipping = props.orders.data.find(arr=>arr.id==id);
    if (!shipping || !shipping.courier) return;

    shippingForm.id = id;
    shippingForm.type = type;
    showFinishModal.value=true;

    nextTick(() => {
        commentArea.value.focus();
    });
};
</script>

<template>
    <Head title="Orders Manage">
    </Head>
    <MarketLayout :navigation="navigation">
        <div>
            <div>
                <div v-for="order in orders.data" :key="order.id" class="hover:bg-gray-200 rounded-md mb-2">
                    <div class="grid grid-cols-7 gap-2">
                        <div>{{ order.id }}</div>
                        <div class="col-span-2">
                            <b>{{ order.warehouse_info.code }}</b>
                            {{ order.warehouse_info.address }}
                        </div>
                        <div class="col-span-2">{{ order.address }}</div>
                        <div>
                            <SecondaryButton @click="showOrderBodyId=order.id">Заказ</SecondaryButton>
                        </div>
                        <div v-if="!order.delivered && !order.cancelled">
                            <PrimaryButton v-if="!order.courier" @click="takeShipping(order.id)">Взять в работу</PrimaryButton>
                        </div>
                    </div>
                    <div v-show="showOrderBodyId==order.id">
                        <div v-for="(position, index) in order.order_info.body" :key="index" class="grid grid-cols-9 gap-2">
                            <div>{{ index+1 }}</div>
                            <div class="col-span-5">{{ position.product_title }}, {{ position.offer_title }}</div>
                            <div>{{ (position.price/100).toFixed(2) }} ₽</div>
                            <div>{{ position.quantity }} {{ position.measure }}</div>
                            <div>{{ (position.total/100).toFixed(2) }} ₽</div>
                        </div>
                        <div class="md:flex md:justify-end gap-2" v-show="order.courier && !order.delivered && !order.cancelled">
                            <DangerButton @click="finiShipping(order.id, 'cancelled')">Отменен</DangerButton>
                            <SecondaryButton @click="finiShipping(order.id, 'comment')">Оставить комментарий</SecondaryButton>
                            <PrimaryButton @click="finiShipping(order.id, 'delivered')">Доставлен</PrimaryButton>
                        </div>
                    </div>
                </div>
            </div>
            <Pagination :paginator="orders.meta"/>
        </div>
        <Modal :show="showFinishModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    <span v-show="shippingForm.type=='delivered'">Завершение заказа</span>
                    <span v-show="shippingForm.type=='cancelled'">Отмена заказа</span>
                    <span v-show="shippingForm.type=='comment'">Комментарий к заказу</span>
                </h2>

                <div class="mt-2">
                    <span v-show="shippingForm.type=='delivered'">Подтвердите доставку заказа.</span>
                    <InputLabel for="comment" 
                                v-show="shippingForm.type=='comment' || shippingForm.type=='delivered'" 
                                :value="shippingForm.type=='delivered'?'Укажите причину отмены':'Комментарий'" 
                                class="sr-only"
                    />
                    <textarea id="comment" 
                              ref="commentArea"
                              class="border-1 border-gray-400 w-full p-1"
                              v-model="shippingForm.comment" 
                              v-show="shippingForm.type=='comment' || shippingForm.type=='cancelled'"
                    >
                    </textarea>
                    <InputError :message="shippingForm.errors.comment" class="mt-2" />
                    <InputError :message="shippingForm.errors.action" class="mt-2" />
                    <InputError :message="shippingForm.errors.id" class="mt-2" />
                </div>

                <div class="mt-2 flex gap-2" v-show="shippingForm.type=='cancelled'">
                    <Checkbox name="returned" id="returned" v-model="shippingForm.returned" :checked="shippingForm.returned" title="Заказ возвращен на склад"/>
                    <InputLabel class="ml-2" for="returned" value=" - возвращен на склад"/>
                </div>

                <div class="flex w-full justify-end mt-2">
                    <SecondaryButton @click="closeModal" class="mr-2">Закрыть</SecondaryButton>
                    <PrimaryButton @click="finiSend">{{ shippingForm.type=='delivered'?'Подтвердить':'Сохранить' }}</PrimaryButton>
                </div>
            </div>
        </Modal>
    </MarketLayout>
</template>