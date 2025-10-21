<script setup>
import FullLayout from '@/Layouts/FullLayout.vue';

const props = defineProps({
    order: {type: Object},
});

</script>

<template>
    <Head title="Заказ" />

    <FullLayout>
        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div>
                        Заказ #{{ order.id }}
                        <span>[{{ order.status.value }}]</span>
                    </div>
                    <div v-for="(position, index) in order.body" :key="index" class="md:grid md:grid-cols-6 gap-2">
                        <div class="md:col-span-3">{{ position.product_title }} {{ position.offer_title }}</div>
                        <div>{{ position.quantity }} {{ position.measure }}</div>
                        <div>{{ (position.price/100).toFixed(2) }} ₽</div>
                        <div>{{ (position.total/100).toFixed(2) }} ₽</div>
                    </div>
                </div>
            </div>
        </div>
    </FullLayout>
</template>
