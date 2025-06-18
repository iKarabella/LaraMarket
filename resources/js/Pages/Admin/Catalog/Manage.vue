<script setup>
import MarketLayout from '@/Layouts/MarketLayout.vue';
import Categories from './Partials/Categories.vue';
import Products from './Partials/Products.vue';
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    categories: {type:Array, default:[]},
    navigation:{type:Array, default:[]},
    products:{type:Array, default:[]},
    filters:{type:Object, default:{
        category:null,
    }}
});

const filters = useForm({
    category:props.filters.category
});

const selectCat = (e)=>{
    filters.category=e;
    filters.post(route('admin.catalog.manage'), {
        preserveScroll:true,
        onSuccess:(e)=>{
            //filters.reset();
        }, 
        onError:(e)=>console.log(e)
    });
};

</script>

<template>
    <Head title="Catalog Manage">
    </Head>
    <MarketLayout :navigation="navigation">
        <Categories :categories="categories" @select="selectCat"/>
        <div>
            <div class="text-gray-800">{{filters.category==null?'Все товары':'Товары в этой категории'}}</div>
            <Products class="mt-2" :products="products"/>
        </div>
    </MarketLayout>
</template>