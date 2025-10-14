<script setup>
import FullLayout from '@/Layouts/FullLayout.vue';
import Positions from '@/Components/Elements/CatalogPositions/Positions.vue';
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
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-2 pb-2">
                    <Positions :positions="results.found" 
                               :userCart="usercart"
                               :auth="$page.props.auth"
                    />
                </div>
            </div>
        </div>
    </FullLayout>
</template>