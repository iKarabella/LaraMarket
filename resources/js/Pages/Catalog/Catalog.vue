<script setup>
import Positions from './Partials/Positions.vue';
import Pagination from '@/Components/Pagination';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import FullLayout from '@/Layouts/FullLayout.vue';

const props = defineProps({
    products:{type:Array, default:[]},
    filters:{type:Object, default:{
        category:null,
    }},
    breadcrumb:{type:Array, default:[]},
});
</script>

<template>
    <Head title="Catalog Manage">
    </Head>
    <FullLayout>
        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-2 pb-2">
                    <Breadcrumb :breadcrumb="breadcrumb"/>
                    <div v-if="products.data && products.data.length">
                        <Positions :positions="products.data" 
                                   :userCart="userCart"
                                   :auth="$page.props.auth"
                        />
                        <Pagination lineClass="w-full flex justify-center mb-2 mt-2" 
                                    v-show="products.meta" 
                                    :paginator="products.meta" 
                                    :href="true" 
                        />
                    </div>
                    <div v-else>
                        В этой категории пока нет товаров
                    </div>
                </div>
            </div>
        </div>
    </FullLayout>
</template>