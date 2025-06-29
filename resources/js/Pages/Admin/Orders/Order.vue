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

const props = defineProps({
    order: {type:Object, default:{}},
    warehouses: {type:Array, default:[]},
    navigation:{type:Array, default:[]},
});

const writeOffEdit = ref({});
const showWriteOffStocksList = ref(false);
const showWriteOffEditForm = ref(null);
const showEditPositionForm = ref(null);
const showCancelForm = ref(null);
const writeOffResult = ref(null);
const orderBody = ref(props.order.body);

const cancelForm = useForm({
    order_id: props.order.id,
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

const totalAmount = computed(()=>{
    return (props.order.amount/100).toFixed(2);
});

const writeOffStocks = computed(()=>{
    if(!writeOffEdit.value.stocks) return [];
    return writeOffEdit.value.stocks.filter(arr=>writeOffEdit.value.writeOffWh.findIndex(a=>a.id==arr.warehouse_id)<0).map(arr=>{
        arr.warehouse = props.warehouses.find(w=>w.id==arr.warehouse_id);
        return arr;
    });
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

const writeOffForm = (index=null)=>{
    console.log('writeOffForm', index);
    if (props.order.status==11 || index===null) return false;

    writeOffEdit.value = JSON.parse(JSON.stringify(orderBody.value[index]));
    showWriteOffEditForm.value=true;

    writeOffResult.value=null;
};
const storeWriteOff = ()=>{
    if(props.order.status!=5 && props.order.status!=7) return false;
    
    let ret = checkWriteOff();
    if(ret===true){
        let find = orderBody.value.find(f=>f.offer_id==writeOffEdit.value.offer_id);
        if(find){
            find.writeOffWh=writeOffEdit.value.writeOffWh.map(arr=>{
            arr.quantity = parseInt(arr.quantity);
            return arr;
        }).filter(a=>{return a.quantity>0;});
            showWriteOffEditForm.value=false;
            writeOffEdit.value={};
        }
    }
    else {
        writeOffResult.value=ret;
        setTimeout(() => writeOffResult.value=null, 7000);
    }
};
const checkWriteOff = ()=>{
    let count = 0, ret = true;
    writeOffEdit.value.writeOffWh.forEach(wo=>{
        let find = writeOffEdit.value.stocks.find(wh=>wh.warehouse_id==wo.id);
        let quantity = 0;
        if (find)
            {
                quantity = parseInt(wo.quantity);
            
                if (quantity>find.quantity) ret='Превышено наличие на складе.';

                count+=quantity;
            }
            else ret=`Склад (${wo.id}) не найден`;
    });

    if(count!=writeOffEdit.value.quantity) ret=`Количество в списании (${count}) не соответствует заказу. (${writeOffEdit.value.quantity})`;

    return ret;
};
const addWhToWriteOff = (whId)=>{
    let find = writeOffEdit.value.stocks.find(arr=>arr.warehouse_id==whId);
    
    if(find){
        writeOffEdit.value.writeOffWh.push({
            id:whId,
            code:find.warehouse.code,
            title:find.warehouse.title,
            address: find.warehouse.address,
            quantity:0
        });
    }
                
    showWriteOffStocksList.value=false;
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
                <div class="rounded-md border border-gray-100 bg-gray-100">
                    <div>Доставка:</div>
                    <div>Регион: {{ order.delivery.region }}</div>
                    <div>Город:{{ order.delivery.city }}</div>
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
                                <PrimaryButton v-show="order.status==5" class="w-full" @click="waitingPayment()">Ожидание оплаты</PrimaryButton>
                                <PrimaryButton v-show="[5,7].includes(order.status)" class="w-full" @click="orderToAssembly()">В сборке</PrimaryButton>
                            </div>
                            <InputError :message="setStatusForm.errors.order_id" class="mt-2" />
                        </div>
                        <div v-show="canCancel" class="text-xs font-semibold italic text-gray-700 mx-4 border-t-[1px] border-gray-400 pt-1">
                            или
                        </div>
                        <DangerButton v-show="canCancel" @click="showCancelForm=true" class="my-2">Отменить заказ</DangerButton>
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
                            <SecondaryButton title="Склады для списания" 
                                             class="w-full mr-2 text-left" 
                                             v-if="order.status!=12"
                                            @click="writeOffForm(index)"
                            >
                                <i class="ri-list-check-3"></i>
                                <span v-if="position.writeOffWh.length>0"> {{ position.writeOffWh[0].code }} {{ position.writeOffWh.length>1?`(+${position.writeOffWh.length-1})`:'' }}</span>
                            </SecondaryButton>
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
                
                <div class="mt-2">{{ writeOffEdit.name }}</div>

                <div v-for="wh in writeOffEdit.writeOffWh" class="md:grid md:grid-cols-3 md:gap-2 mt-2 hover:bg-gray-100">
                    <div class="md:col-span-2">
                        <div><b>[{{ wh.code}}] </b> {{ wh.title }} (в наличии: {{ writeOffEdit.stocks.find(a=>a.warehouse_id==wh.id).quantity }})</div>
                        <div>{{ wh.address }}</div>
                    </div>
                    <div>
                        <TextInput
                            :id="`writeoff_quantity_wh_${wh.id}`"
                            v-model="wh.quantity"
                            type="number"
                            :max="writeOffEdit.stocks.find(a=>a.warehouse_id==wh.id).quantity"
                            min="0"
                            :disabled="order.status!=5 && order.status!=7"
                            class="mt-1 block w-3/4"
                            placeholder="Количество"
                        />
                    </div>
                </div>

                <div class="font-semibold italic text-red-700">{{ writeOffResult }}</div>

                <div class="flex w-full justify-between mt-2">
                    <SecondaryButton @click="showWriteOffStocksList=true" :disabled="writeOffStocks.length<1 || (order.status!=5 && order.status!=7)">
                        <i class="ri-folder-add-line"></i> 
                        Добавить склад
                    </SecondaryButton>
                    <div>
                        <SecondaryButton @click="showWriteOffEditForm=false" class="mr-2">Закрыть</SecondaryButton>
                        <PrimaryButton @click="storeWriteOff()" :disabled="order.status!=5 && order.status!=7">Сохранить</PrimaryButton>
                    </div>
                </div>

                <div v-show="showWriteOffStocksList==true">
                    <div v-for="stock in writeOffStocks" class="md:grid md:grid-cols-3 md:gap-2 mt-2">
                        <div class="md:col-span-2">
                            <div><b>[{{ stock.warehouse.code}}] </b> {{ stock.warehouse.title }}. В наличии: {{ stock.quantity }}</div>
                            <div>{{ stock.warehouse.address }}</div>
                        </div>
                        <div class="text-right">
                            <SecondaryButton @click="addWhToWriteOff(stock.warehouse_id)">Добавить</SecondaryButton>
                        </div>
                    </div>
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