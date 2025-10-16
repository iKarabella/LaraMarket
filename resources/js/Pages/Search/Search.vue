<script setup>
import FullLayout from '@/Layouts/FullLayout.vue';
import Positions from '@/Components/Elements/CatalogPositions/Positions.vue';
import Pagination from '@/Components/Pagination';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    results:{type:Object, default: {
        found:[],
        categories:[],
        recommended:[],
        frequently:[],
        autocomplete:[],
        meta:{
            current_page:1,
            total:0,
            total_pages:1,
            per_page:0,
        }
    }},
    search:'',
});

</script>
<template>
    <Head title="Результаты поиска" />

    <FullLayout>
        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-2 pb-2" v-if="results.found && results.found.length>0">
                    <div>
                        Найдено по запросу <b>«{{ search }}»</b>:
                    </div>
                    <Positions :positions="results.found" 
                               :userCart="usercart"
                               :auth="$page.props.auth"
                    />
                    <Pagination lineClass="w-full flex justify-center mb-2 mt-2" 
                                v-show="results.meta" 
                                :paginator="results.meta" 
                                :href="true" 
                    />
                </div>
                <div v-else class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-2 pb-2">
                    <div>
                        <div class="text-center w-full" style="display: none;"> 
                            По поиску «{{ search }}» результаты не найдены.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </FullLayout>
</template>