<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import SecondaryButton from '@/Components/UI/SecondaryButton.vue';
import PrimaryButton from '@/Components/UI/PrimaryButton.vue';
import DangerButton from '@/Components/UI/DangerButton.vue';
import InputLabel from '@/Components/UI/InputLabel.vue';
import TextInput from '@/Components/UI/TextInput.vue';
import InputError from '@/Components/UI/InputError.vue';
import WarehouseLayout from './Partials/WarehouseLayout.vue';
import Tiptap from '@/Components/Tiptap/Tiptap.vue';
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
const quantityInput = ref(null);
const searchInput = ref(null);
const removeItemIndex = ref(null);
const searchItemsList = useTemplateRef('searchItemsList');
const error_quantity = ref(null);

onClickOutside(searchItemsList, event => {searchItemStr.value='', listItems.value=[];});

const addItemForm = ref({
    title:'',
    offer_id:null,
    quantity:'',
    oldQuantity:null,
    measure_val:'',
});

const receiptForm = useForm({
    warehouse:props.selectedWh,
    write_off:true,
    reason:'',
    items:[]
});

const addItem = (id, title, measure_val, quantity) => {
    addItemForm.value.offer_id = id;
    addItemForm.value.title = title;
    addItemForm.value.measure_val = measure_val;
    addItemForm.value.oldQuantity = quantity;
    searchItemStr.value='';
    listItems.value=[];
    addItemFormModal.value = true;
    nextTick(() => {
        quantityInput.value.focus();
    });
};

const addItemToReceipt = () => 
{
    if(addItemForm.value.quantity>addItemForm.value.oldQuantity)
    {
        error_quantity.value = `Нельзя списать больше, чем есть в наличии (${addItemForm.value.oldQuantity}).`;
        setTimeout(()=>{error_quantity.value=null;}, 5000);
        return;
    }

    let item = Object.assign({}, addItemForm.value);
    
    receiptForm.items.push(item);

    modalClose();
    nextTick(() => {
        searchInput.value.focus();
    });
};

const saveReceipt = ()=>{
    if (receiptForm.items.length<1 || props.selectedWh==null) return false;
    receiptForm.warehouse = props.selectedWh;
    receiptForm.put(route('admin.warehouse.storeWriteOff'),{
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
        warehouse: props.selectedWh
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

const updateContent = (content)=>{
    receiptForm.reason = content;
};
</script>

<template>
    <WarehouseLayout :warehouses="warehouses" :navigation="navigation" :selectedWh="selectedWh" :currentBlock="'writeOff'">
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
                                @click="addItem(item.id, `${item.product_title}, ${item.title}`, item.measure_val, item.quantity)">
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
                <div>Количество</div>
                <div></div>
            </div>
            <div v-for="(item, index) in receiptForm.items" :key="item.id" class="grid grid-cols-10 gap-2 m-1 hover:bg-gray-100 rounded">
                <div>{{ index+1 }}</div>
                <div class="col-span-4">{{ item.title }}</div>
                <div>{{ item.quantity }} {{ item.measure_val }}</div>
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
                <span class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                    Причина списания:
                    <span v-if="required" class="text-red-700" title="Обязательное поле">*</span>
                </span>
                <Tiptap :content="receiptForm.reason" @updateContent="updateContent"/>
                <InputError :message="receiptForm.errors.reason" class="mt-2" />
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
                        <InputLabel for="quantity" :value="`Количество (${addItemForm.measure_val})`" />
                        <TextInput
                            type="text"
                            id="quantity"
                            ref="quantityInput"
                            name="quantity"
                            class="w-full mt-1 block"
                            required
                            v-model="addItemForm.quantity"
                            autocomplete="off"
                        />
                        <InputError :message="error_quantity" class="mt-2"/>
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
