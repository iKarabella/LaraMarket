<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import WarehouseLayout from './Partials/WarehouseLayout.vue';
import { computed, ref } from 'vue';
import Pagination from '@/Components/Pagination';
import InputLabel from '@/Components/UI/InputLabel.vue';
import TextInput from '@/Components/UI/TextInput.vue';
import SecondaryButton from '@/Components/UI/SecondaryButton.vue';

const props = defineProps({
    warehouses: {type: Array, defalt:[]},
    navigation:{type:Array, default:[]},
    selectedWh: {type:Number, default:null},
    stocks: {type:Object, default:{}},
    filters: {type:Object, default:{
        search:null,
        page:0
    }}
});

const currentWhInfo = computed(()=>{
    return props.warehouses.find(arr=>arr.id==props.selectedWh)??{};
});

const filterForm = useForm(props.filters);

const searchItem = ()=>{    
    filterForm.post(route('admin.warehouses.stock_in', [currentWhInfo.value.code]), {
        preserveScroll:true,
        onSuccess:(e)=>{
            if (e.props && e.props.filters) filterForm.page = e.props.filters.page;
        }, 
        onError:(e)=>console.log(e)
    });
};

const getPage = (page)=>{
    if (page==filterForm.page) return;
    filterForm.page = page;
    searchItem();
}

</script>

<template>
    <WarehouseLayout :warehouses="warehouses" :navigation="navigation" :selectedWh="selectedWh" :currentBlock="'stockIn'">
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
                    v-model="filterForm.search"
                    autocomplete="off"
                    placeholder="название/id/артикул/штрихкод"
                    v-on:keyup="searchItem"
                />
            </div>
        </div>
        <div class="mt-2">
            <div class="grid grid-cols-8 gap-2 m-1 hover:bg-gray-100 rounded">
                <div>#</div>
                <div class="col-span-4">Название</div>
                <!--div>Цена (зак.)</div>
                <div>Цена</div-->
                <div>В наличии</div>
                <div title="В заказах, не списано со склада">Резерв (сайт)</div>
                <div title="В незакрытых заказах, списано со склада">Резерв (заказы)</div>
            </div>
            <div v-for="(position, index) in stocks.data" v-key="index" class="grid grid-cols-8 gap-2 m-1 hover:bg-gray-100 rounded">
                <div>{{ position.offer_id }}</div>
                <div class="col-span-4">{{ position.product_title }}, {{ position.offer_title }}</div>
                <!--div>{{ position.baseprice.toFixed(2) }} ₽</div>
                <div>{{ position.price.toFixed(2) }} ₽</div-->
                <div>{{ position.stock_in }} {{ position.measure_val }}</div>
                <div>{{ position.stock_reserved }} {{ position.measure_val }}</div>
                <div>{{ position.in_orders }} {{ position.measure_val }}</div>
            </div>
            <Pagination :paginator="stocks.meta" class="mt-2" :href="false" @getPage="getPage"/>
        </div>
    </WarehouseLayout>
</template>
