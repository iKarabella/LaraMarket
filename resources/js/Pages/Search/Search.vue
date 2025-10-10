<script setup>
import FullLayout from '@/Layouts/FullLayout.vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    search_result:{type:Object},
});

</script>
<template>
    <Head title="Результаты поиска" />

    <FullLayout>
        <div class="py-4">
            <div class="sm:px-6 lg:px-8">
                {{ search_result }}
            </div>
        </div>
    </FullLayout>
</template>