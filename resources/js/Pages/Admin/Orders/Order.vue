<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import MarketLayout from '@/Layouts/MarketLayout.vue';
import SecondaryButton from '@/Components/UI/SecondaryButton.vue';
import { computed, ref } from 'vue';
import DangerButton from '@/Components/UI/DangerButton.vue';
import PrimaryButton from '@/Components/UI/PrimaryButton.vue';
import Modal from '@/Components/Modals/MainModal.vue';
import TextInput from '@/Components/UI/TextInput.vue';
import InputError from '@/Components/UI/InputError.vue';
import OrderStatus from './Partials/OrderStatus.vue';
import Comments from './Partials/Comments.vue';
import Checkbox from '@/Components/UI/Checkbox.vue';

const props = defineProps({
    order: {type:Object, default:{}},
    warehouses: {type:Array, default:[]},
    navigation:{type:Array, default:[]},
});

const showWriteOffEditForm = ref(null);
const showEditPositionForm = ref(null);
const showCancelForm = ref(null);
const writeOffResult = ref(null);
const orderBody = ref(props.order.body);
const orderWh = ref(props.order.warehouse_id);

const cancelForm = useForm({
    order_id: props.order.id,
    goods_returned:false,
    password:'',
    comment:''
});
const editPositionForm = useForm({
    order_id:props.order.id,
    offer_id:null,
    product_id:null,
    title: '',
    quantity:null,
    amount:null,
});

const setStatusForm = useForm({
    order_id:props.order.id,
    toAssembly:[],
});

const canCancel = computed(()=>{
    return [5,6,9,10].includes(props.order.status);
});

const canWriteOffForm = computed(()=>{
    return orderWh.value>0 && [5,6,7].includes(props.order.status);
});

const totalAmount = computed(()=>{
    return (props.order.amount/100).toFixed(2);
});

const closeModal = ()=>{
    showCancelForm.value=false;
    showEditPositionForm.value=false;
    editPositionForm.reset();
    cancelForm.reset();
};

const editPosition = (index)=>{
    if (props.order.status!=5) return false;
    editPositionForm.offer_id = orderBody.value[index].offer;
    editPositionForm.product_id = orderBody.value[index].position;
    editPositionForm.name = `${orderBody.value[index].product_title}, ${orderBody.value[index].offer_title}`;
    editPositionForm.quantity = orderBody.value[index].quantity;
    editPositionForm.price = orderBody.value[index].price;
    editPositionForm.amount = orderBody.value[index].total;
    showEditPositionForm.value = true;
};

const cancelOrder = ()=>{
    if (canCancel.value) cancelForm.post(route('admin.order.cancel', [props.order.uuid]), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: (e) => console.log(e)
    });
};

const deletePosition = ()=>{
    if (props.order.status!=5) return false;
    editPositionForm.quantity = 0;
    storeEditPosition();
};

const storeEditPosition = ()=>{
    if (props.order.status!=5) return false;
    editPositionForm.post(route('admin.order.editPosition', [props.order.uuid]), {
        preserveScroll: true,
        onSuccess: (e) => {
            orderBody.value = e.props.order.body;
            closeModal();
        },
        onError: (e) => console.log(e)
    });
};

const waitingPayment = ()=>{
    setStatusForm.post(route('admin.order.waitingPayment'), {
        preserveScroll: true,
        onSuccess: () => setStatusForm.reset(),
        onError: (e) => console.log(e)
    });
};

const orderToAssembly = ()=>{
    if (props.order.status!=5 && props.order.status!=7) return false;
    setStatusForm.toAssembly=orderBody.value;
    setStatusForm.post(route('admin.order.toAssembly'), {
        preserveScroll: true,
        onSuccess: () => setStatusForm.reset(),
        onError: (e) => console.log(e)
    });
};

const setWh = (whID)=>{
    orderWh.value = whID;
};

const writeOffForm = ()=>{
    if (props.order.status==11) return false;

    showWriteOffEditForm.value=true;

    writeOffResult.value=null;
};
const storeWriteOff = ()=>{
    if(!canWriteOffForm.value) return false;
    
    useForm({
        order_id:props.order.id,
        warehouse_id:orderWh.value
    }).post(route('admin.order.setWarehouse', [props.order.uuid]), {
        preserveScroll: true,
        onSuccess: () => showWriteOffEditForm.value=false,
        onError: (e) => console.log(e)
    });
};
</script>

<template>
    <MarketLayout :navigation="navigation">
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
                        <div>
                            <div v-if="order.status==5 && order.warehouse_id!=null" class="font-semibold italic text-gray-700 text-xs">
                                <div class="my-1">Установить в статус в</div>
                                <div class="grid grid-cols-1 mb-2 w-fit mx-auto gap-1 justify-items-center">
                                    <PrimaryButton v-show="order.status==5" class="w-full" @click="waitingPayment()">Ожидание оплаты</PrimaryButton>
                                    <PrimaryButton v-show="[5,7].includes(order.status)" class="w-full" @click="orderToAssembly()">В сборке</PrimaryButton>
                                </div>
                                <InputError :message="setStatusForm.errors.order_id" class="mt-2" />
                            </div>
                            <div v-else class="font-semibold italic text-gray-700 text-xs">
                                <div class="my-1">Действия</div>
                                <div class="grid grid-cols-1 mb-2 w-fit mx-auto gap-1 justify-items-center">
                                    <PrimaryButton v-show="order.status==5" class="w-full" @click="writeOffForm()">Выбрать склад</PrimaryButton>
                                </div>
                            </div>
                            <div v-show="canCancel" class="text-xs font-semibold italic text-gray-700 mx-4 border-t-[1px] border-gray-400 pt-1">
                                или
                            </div>
                            <DangerButton v-show="canCancel" @click="showCancelForm=true" class="my-2">Отменить заказ</DangerButton>
                        </div>
                    </div>
                </div>
            </div>
            <div class="rounded-md border border-gray-100 bg-gray-100 mt-2 pl-2">
                <div>
                    Состав заказа:
                </div>
                <div>
                    <div v-for="(position, index) in orderBody" :key="index" class="md:grid md:grid-cols-11 md:gap-2 mb-2 border rounded-md border-gray-100 hover:border-gray-200">
                        <div>{{ index+1 }}</div>
                        <div class="md:col-span-5">{{ position.product_title }}, {{ position.offer_title }}</div>
                        <div>{{ (position.price/100).toFixed(2) }} ₽</div>
                        <div>
                            <div class="grid grid-cols-2 gap-0 w-fit">
                                <div>{{ position.quantity }}</div>
                                <div class="text-left">{{ position.measure }}</div>
                            </div>
                        </div>
                        <div>{{ (position.total/100).toFixed(2) }} ₽</div>
                        <div class="px-2 md:col-span-2 relative flex whitespace-nowrap justify-end">
                            <SecondaryButton :disabled="order.status!=5" @click="editPosition(index)"><i class="ri-edit-2-line"></i></SecondaryButton>
                        </div>
                    </div>
                    <div class="md:grid md:grid-cols-11 md:gap-2 mb-2 border rounded-md border-gray-100">
                        <div class="md:col-span-6"></div>
                        <div>Всего:</div>
                        <div>{{ orderBody.map(arr=>{return arr.quantity;}).reduce((partialSum, a) => partialSum + a, 0) }}</div>
                        <div>{{ totalAmount }} ₽</div>
                        <div class="px-2 md:col-span-2 relative flex justify-end">
                            
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

        <Modal :show="showWriteOffEditForm">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Списание со склада
                </h2>
                <div class="mt-2">
                    <div v-for="(wh, index) in warehouses" v-key="index" class="flex w-full justify-beetween mt-2">
                        <div class="w-full">{{ wh.title }} ({{ wh.address }})</div>
                        <div>
                            <PrimaryButton @click="setWh(wh.id)" :disabled="wh.id==orderWh">{{wh.id==orderWh?'Выбран':'Выбрать'}}</PrimaryButton>
                        </div>
                    </div>
                </div>

                <div class="flex w-full justify-end mt-2">
                    <SecondaryButton @click="showWriteOffEditForm=false" class="mr-2">Закрыть</SecondaryButton>
                    <PrimaryButton @click="storeWriteOff()" :disabled="!canWriteOffForm">Сохранить</PrimaryButton>
                </div>
            </div>
        </Modal>

        <Modal :show="showCancelForm" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Вы действительно хотите отменить этот заказ?
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Укажите причину отмены заказа.
                    Для подтверждения отмены заказа введите свой пароль
                </p>

                <div class="mt-6">
                    <InputLabel for="comment" value="Комментарий" class="sr-only" />

                    <textarea id="comment" 
                              v-model="cancelForm.comment" 
                              class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 
                                    dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    </textarea>

                    <InputError :message="cancelForm.errors.comment" class="mt-2" />
                </div>

                <div class='mt-6'>
                    <label class="mx-2 text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300">
                        <Checkbox v-model="cancelForm.goods_returned" :checked="cancelForm.goods_returned"/> - товары из заказа возвращены на склад
                    </label>
                    <InputError :message="cancelForm.errors.goods_returned" class="mt-2" />
                </div>

                <div class="mt-6">
                    <InputLabel for="password" value="Пароль" class="sr-only" />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="cancelForm.password"
                        type="password"
                        class="mt-1 block w-3/4"
                        placeholder="Пароль"
                    />

                    <InputError :message="cancelForm.errors.password" class="mt-2" />
                    <InputError :message="cancelForm.errors.order_id" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Отмена </SecondaryButton>

                    <DangerButton
                        class="ms-3"
                        :class="{ 'opacity-25': cancelForm.processing }"
                        :disabled="cancelForm.processing"
                        @click="cancelOrder"
                    >
                        Отменить заказ
                    </DangerButton>
                </div>
            </div>
        </Modal>

        <Modal :show="showEditPositionForm" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{editPositionForm.name}}
                </h2>

                <div>Цена: {{ (editPositionForm.price/100).toFixed(2) }} ₽</div>

                <div>
                    <div class="flex w-full">
                        Количество:
                        <TextInput
                            id="quantity"
                            v-model="editPositionForm.quantity"
                            type="number"
                            class="mt-1 block w-3/4"
                            placeholder="quantity"
                        />
                    </div>
                    <div>
                        <InputError :message="editPositionForm.errors.quantity" class="mt-2" />
                    </div>
                </div>

                <div>
                    Стоимость: {{ (editPositionForm.price*editPositionForm.quantity/100).toFixed(2) }} ₽
                </div>

                <div class="my-2 flex justify-between">
                    <DangerButton @click="deletePosition" 
                                   title="Удалить позицию из заказа"
                                   class="ms-3"
                                  :class="{ 'opacity-25': editPositionForm.processing }"
                                  :disabled="editPositionForm.processing"
                    >
                        Удалить
                    </DangerButton>
                    <div class="flex justify-end">
                        <SecondaryButton @click="closeModal" class="mr-2" title="Отменить изменения и закрыть форму">Отменить</SecondaryButton>
                        <PrimaryButton @click="storeEditPosition" 
                                        title="Сохранить изменения и закрыть форму"
                                       :class="{ 'opacity-25': editPositionForm.processing }"
                                       :disabled="editPositionForm.processing"
                        >
                            Сохранить
                        </PrimaryButton>
                    </div>
                </div>
            </div>
        </Modal>
    </MarketLayout>
</template>