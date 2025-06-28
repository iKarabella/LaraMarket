<script setup>
import FullLayout from '@/Layouts/FullLayout.vue';

const props = defineProps({
    order: {type: Object},
});

</script>

<template>
    <Head title="Заказ" />

    <FullLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div>
                        Заказ #{{ order.id }}
                        <span>[{{ order.status.value }}]</span>
                    </div>
                    {{ order }}
                </div>
            </div>
        </div>
    </FullLayout>
</template>
