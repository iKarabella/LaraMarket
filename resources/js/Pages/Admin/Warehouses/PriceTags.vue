<script setup>
import { useForm } from '@inertiajs/vue3';
import WarehouseLayout from './Partials/WarehouseLayout.vue';
import TextInput from '@/Components/UI/TextInput.vue';
import SecondaryButton from '@/Components/UI/SecondaryButton.vue';
import PrimaryButton from '@/Components/UI/PrimaryButton.vue';
import InputLabel from '@/Components/UI/InputLabel.vue';
import { computed, ref, useTemplateRef, nextTick } from 'vue';
import { onClickOutside } from '@vueuse/core'

const props = defineProps({
    warehouses: {type: Array, defalt:[]},
    navigation:{type:Array, default:[]},
    selectedWh: {type:Number, default:null},
    positions:{type:Array, default:[]},
});

const printPageRef = ref();

const currentWhInfo = computed(()=>{
    return props.warehouses.find(arr=>arr.id==props.selectedWh)??{};
});

const priceTagsForm = ref({
    positions: props.positions,
    warehouse_id: props.selectedWh,
    format:'A4'
});

const remove = (offerId) => {
    priceTagsForm.value.positions = priceTagsForm.value.positions.filter(arr=>{return arr.offer_id!=offerId;});
};

const print = (format='A4') => {
    if (priceTagsForm.value.positions.length<1) return;
    priceTagsForm.format = format;
    axios.post(route('admin.warehouses.printPriceTags'), priceTagsForm.value).then(res=>{
        if (res) {
            printPageRef.value.contentWindow.document.body.innerHTML = res.data;
            printPageRef.value.contentWindow.print();
        }
    });
};

const searchItemStr = ref('');
const listItems = ref([]);
const searchItemsList = useTemplateRef('searchItemsList');
const searchInput = ref(null);
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

const addItem = (offer_id, title, measure_val, price) => {
    priceTagsForm.value.positions.unshift({
        title:title,
        price:(price/100).toFixed(2),
        offer_id:offer_id,
        quantity:1,
        measure:measure_val
    });
    
    searchItemStr.value='';

    listItems.value=[];

    nextTick(() => {
        searchInput.value.focus();
    });
};

onClickOutside(searchItemsList, event => {searchItemStr.value='', listItems.value=[];});

</script>

<template>
    <WarehouseLayout :warehouses="warehouses" :navigation="navigation" :selectedWh="selectedWh" :currentBlock="'priceTags'">
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
                                @click="addItem(item.id, `${item.product_title}, ${item.title}`, item.measure_val, item.price)">
                                {{ item.product_title }}, {{ item.title }}
                            </li>
                        </ul>
                    </div>
                </Transition>
            </div>
        </div>
        <div class="mt-2">
            <div class="md:grid md:grid-cols-10 gap-2 py-2">
                <div>#</div>
                <div class="col-span-6">Позиция</div>
                <div>Цена</div>
                <div title="Количество ценников">Кол-во</div>
                <div></div>
            </div>
            <div v-for="(position, index) in priceTagsForm.positions" :key="index" class="md:grid md:grid-cols-10 gap-2 py-2 hover:bg-gray-100 rounded">
                <div>{{ index+1 }}</div>
                <div class="col-span-6">{{ position.title }}</div>
                <div>{{ position.price }}₽</div>
                <div>
                    <TextInput v-model="position.quantity" :name="`quantity_${index}`" class="pl-2 w-[4rem]"/>
                </div>
                <div>
                    <SecondaryButton @click="remove(position.offer_id)" class="text-red-900" title="Удалить ценник"><i class="ri-close-circle-line"></i></SecondaryButton>
                </div>
            </div>
        </div>
        <div class="mt-2">
            <PrimaryButton @click="print()" :disabled="priceTagsForm.positions.length<1">Печать</PrimaryButton>
        </div>
        <div class="relative">
            <iframe
                width="1"
                ref="printPageRef"
                height="1"
                title="Печать ценников"
                frameborder="0"
                style="z-index:-10"
            />
        </div>
    </WarehouseLayout>
</template>
