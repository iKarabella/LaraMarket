<script setup>
import MarketLayout from '@/Layouts/MarketLayout.vue';
import Categories from './Partials/Categories.vue';
import { ref } from 'vue';

const props = defineProps({
    categories: {type:Array, default:[]},
    navigation:{type:Array, default:[]},
});

const selectedCat = ref(null);

const selectCat = (e)=>{
    selectedCat.value=e;
};

</script>

<template>
    <Head title="Catalog Manage">
    </Head>
    <MarketLayout :navigation="navigation">
        <Categories :categories="categories" @select="selectCat"/>
    </MarketLayout>
</template>