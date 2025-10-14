<script setup>
import Positions from '@/Components/Elements/CatalogPositions/Positions.vue';
import Pagination from '@/Components/Pagination';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import FullLayout from '@/Layouts/FullLayout.vue';

const props = defineProps({
    products:{type:Array, default:[]},
    filters:{type:Object, default:{
        category:null,
        onlyInStock:false,
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
                    <div class="md:flex md:justify-between">
                        <Breadcrumb :breadcrumb="breadcrumb"/>
                        <!--div class="flex whitespace-nowrap text-right p-4">
                            <Checkbox :v-model="filters.onlyInStock" 
                                      :checked="filters.onlyInStock" 
                                      id="onlyInStock" 
                                      name="onlyInStock"/>
                            <InputLabel for="onlyInStock"> - в наличии</InputLabel>
                        </div-->
                    </div>
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