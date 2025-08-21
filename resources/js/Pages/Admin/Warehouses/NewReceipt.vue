<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import SecondaryButton from '@/Components/UI/SecondaryButton.vue';
import PrimaryButton from '@/Components/UI/PrimaryButton.vue';
import DangerButton from '@/Components/UI/DangerButton.vue';
import InputLabel from '@/Components/UI/InputLabel.vue';
import TextInput from '@/Components/UI/TextInput.vue';
import InputError from '@/Components/UI/InputError.vue';
import WarehouseLayout from './Partials/WarehouseLayout.vue';
import Modal from '@/Components/Modals/MainModal.vue';
import { onClickOutside } from '@vueuse/core'
import { useTemplateRef, ref, nextTick } from 'vue'

const props = defineProps({
    warehouses: {type: Array, defalt:[]},
    navigation:{type:Array, default:[]},
    selectedWh: {type:Number, default:null},
});

const searchItemStr = ref('');
const listItems = ref([]);
const addItemFormModal = ref(null);
const priceInput = ref(null);
const searchInput = ref(null);
const removeItemIndex = ref(null);
const searchItemsList = useTemplateRef('searchItemsList');

onClickOutside(searchItemsList, event => {searchItemStr.value='', listItems.value=[];});

const addItemForm = ref({
    title:'',
    offer_id:null,
    price:null,
    coeff:0,
    oldPrice:null,
    quantity:'',
    measure_val:'',
});

const receiptForm = useForm({
    warehouse:props.selectedWh,
    items:[]
});

const addItem = (id, title, measure_val, coeff, price) => {
    addItemForm.value.offer_id = id;
    addItemForm.value.title = title;
    addItemForm.value.measure_val = measure_val;
    addItemForm.value.coeff = coeff;
    addItemForm.value.oldPrice = (price/100).toFixed(2);
    searchItemStr.value='';
    listItems.value=[];
    addItemFormModal.value = true;
    nextTick(() => {
        priceInput.value.focus();
    });
};

const addItemToReceipt = () => {
    let item = Object.assign({}, addItemForm.value);
    let price = parseFloat(item.price);

    if (parseInt(item.coeff)>0) item.newPrice = (price+(price/parseInt(item.coeff))).toFixed(2);
    else item.newPrice = price;
    
    receiptForm.items.push(item);

    modalClose();

    nextTick(() => {
        searchInput.value.focus();
    });
};

const saveReceipt = ()=>{
    if (receiptForm.items.length<1 || props.selectedWh==null) return false;
    receiptForm.warehouse = props.selectedWh;
    receiptForm.put(route('admin.warehouse.storeReceipt'),{
        preserveScroll:true,
        onSuccess:(e)=>{
            receiptForm.reset();
        }, 
        onError:(e)=>console.log(e)
    });
};

const removeItem = (index=null)=>{
    if(index===null) {
        receiptForm.items.splice(removeItemIndex.value, 1);
        removeItemIndex.value=null;
    }
    else {
        removeItemIndex.value=index;
    }
};

const searchItem = ()=>{
    if (searchItemStr.value.length < 1) return false;
        axios.post(route('admin.warehouses.searchProduct'), {
        search: searchItemStr.value,
    }).then(res=>{
        if (res.data) {
            listItems.value=res.data;
        }
    });
};

const modalClose = ()=>{
    addItemFormModal.value=false;
    addItemForm.value.title='';
    addItemForm.value.offer_id=null;
    addItemForm.value.quantity=null;
    addItemForm.value.measure_val = null;
    addItemForm.value.price = null;
    addItemForm.value.coeff = 0;
};

const toFloat = (val)=>{
    return parseFloat(val).toFixed(2);
};
</script>

<template>
    <WarehouseLayout :warehouses="warehouses" :navigation="navigation" :selectedWh="selectedWh" :currentBlock="'receipt'">
        <div class="md:grid md:grid-cols-2 md:gap-2">
            <div>
                <InputLabel for="search" value="Поиск"/>
                <TextInput
                    type="text"
                    ref="searchInput"
                    id="search"
                    name="search"
                    class="w-full mt-1 block"
                    required
                    v-model="searchItemStr"
                    autocomplete="off"
                    placeholder="название/id/артикул/штрихкод"
                    v-on:keyup="searchItem"
                />
            </div>
            <div class="relative">
                <Transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0 scale-95"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-75"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-95"
                >
                    <div class="absolute bg-white border rounded border-gray-200 searchBox" 
                        v-show="listItems.length"
                        ref="searchItemsList">
                        <ul>
                            <li class="cursor-pointer px-4 py-2 m-1 hover:bg-gray-200"
                                v-for="item in listItems" 
                                title="'Добавить"
                                @click="addItem(item.id, `${item.product_title}, ${item.title}`, item.measure_val, item.coeff, item.price)">
                                {{ item.product_title }}, {{ item.title }}
                            </li>
                        </ul>
                    </div>
                </Transition>
            </div>
        </div>
        <div class="mt-2">
            <div class="grid grid-cols-10 gap-2 m-1 hover:bg-gray-100 rounded">
                <div>#</div>
                <div class="col-span-4">Название</div>
                <div>Цена (зак.)</div>
                <div>Количество</div>
                <div>Стоимость</div>
                <div title="Рекомендуемая розничная цена (текущая розничная цена): Цена (зак) * Коэфф.">Цена (рек.)</div>
                <div></div>
            </div>
            <div v-for="(item, index) in receiptForm.items" :key="item.id" class="grid grid-cols-10 gap-2 m-1 hover:bg-gray-100 rounded">
                <div>{{ index+1 }}</div>
                <div class="col-span-4">{{ item.title }}</div>
                <div>{{ item.price }}₽</div>
                <div>{{ item.quantity }} {{ item.measure_val }}</div>
                <div>{{ (item.price*item.quantity).toFixed(2) }}₽</div>
                <div title="Рекомендуемая розничная цена (текущая розничная цена): Цена (зак) * Коэфф.">
                    {{item.newPrice}}
                    ({{ item.oldPrice }})
                </div>
                <div>
                    <div class="w-fit rounded-full hover:bg-red-700 hover:text-white text-red-800 cursor-pointer py-1" 
                        title="Убрать" 
                        @click="removeItem(index)"
                    >
                        <i class="ri-delete-bin-6-line"></i>
                    </div>
                </div>
                <!--InputError :message="receiptForm.errors[`items.${index}.price`]" class="mt-2" /-->
            </div>
            <div class="mt-2">
                <SecondaryButton :disable="receiptForm.items.length<1" @click="saveReceipt">Сохранить</SecondaryButton>
            </div>
        </div>
        <Modal :show="addItemFormModal" ref="modalWindow" @close="modalClose">
            <div class="p-6">
                <div>{{ addItemForm.title }}</div>
                <div class="md:grid md:grid-cols-2 md:gap-2">
                    <div>
                        <InputLabel for="price" value="Цена (закуп.)" />
                        <TextInput
                            type="text"
                            id="price"
                            ref="priceInput"
                            name="price"
                            class="w-full mt-1 block"
                            required
                            v-model="addItemForm.price"
                            @change="addItemForm.price=toFloat(addItemForm.price)"
                            autocomplete="off"
                        />
                    </div>
                    <div>
                        <InputLabel for="quantity" :value="`Количество (${addItemForm.measure_val})`" />
                        <TextInput
                            type="text"
                            id="quantity"
                            name="quantity"
                            class="w-full mt-1 block"
                            required
                            v-model="addItemForm.quantity"
                            autocomplete="off"
                        />
                    </div>
                </div>
                <div class="text-center mt-2">
                    <SecondaryButton @click="modalClose" class="mr-2">Отменить</SecondaryButton>
                    <PrimaryButton @click="addItemToReceipt">Добавить</PrimaryButton>
                </div>
            </div>
        </Modal>
        <Modal :show="removeItemIndex!==null" @close="removeItemIndex=null">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Вы действительно хотите удалить эту позицию?
                </h2>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="removeItemIndex=null"> Отмена </SecondaryButton>

                    <DangerButton
                        class="ms-3"
                        @click="removeItem()"
                    >
                        Удалить
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </WarehouseLayout>
    
</template>
