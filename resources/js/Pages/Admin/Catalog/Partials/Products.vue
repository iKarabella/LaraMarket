<script setup>
import SecondaryButton from '@/Components/UI/SecondaryButton.vue';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    products: {type:Object, default:{}},
    loading: {type:Boolean, default:false}
});

const emits = defineEmits({getPage:null});

const showBlock = computed(()=>{
    if (props.loading) return 'loading';
    else if (props.products && props.products.data && props.products.data.length>0) return 'products';
    else return 'noproducts';
});

const offerStocks = (index)=>{
    if(!props.products.data[index].hasOwnProperty('offers')) return 0;

    let stocks = 0;

    props.products.data[index].offers.map(a=>a.stocks.map(i=>i.quantity)).forEach((arr)=>stocks = stocks+arr.reduce((a,b)=>a+b, 0));

    return stocks;
}
</script>

<template>
    <div>
        <div v-if="showBlock=='loading'">...</div>
        <div v-if="showBlock=='noproducts'">Продукты не найдены.</div>
        <div v-if="showBlock=='products'">
            <div class="md:grid md:grid-cols-6 md:gap-2 mt-1 p-2">
                <div class="col-span-3">Название</div>
                <div>ТП</div>
                <div>Остаток</div>
                <div class="text-right pr-2"></div>
            </div>
            <div v-for="(product,index) in products.data" :key="product.id" class="md:grid md:grid-cols-6 md:gap-2 mt-1 p-2 rounded hover:bg-gray-200">
                <div class="col-span-3">{{ product.title }}</div>
                <div>ТП: {{ product.offers.length }}</div>
                <div>{{ offerStocks(index) }}</div>
                <div class="text-right pr-2">
                    <i :class="{'ri-eye-line':product.visibility, 'ri-eye-off-line':!product.visibility}"></i>
                    <Link :href="route('admin.products.edit', [product.link])" class="ml-2" title="Редактировать">
                        <SecondaryButton><i class="ri-edit-2-fill"></i></SecondaryButton>
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
