<script setup>
import FullLayout from '@/Layouts/FullLayout.vue';
import SecondaryButton from '@/Components/UI/SecondaryButton.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    order: {type: Object},
});

const order_status_info = computed(()=>{
    return props.order.status_info?props.order.status_info.value:'Создан';
});

</script>

<template>
    <Head title="Заказ" />

    <FullLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div>
                        Заказ #{{ order.uuid }}
                        <span>[{{ order_status_info }}]</span>
                    </div>
                    {{ order }}
                </div>
            </div>
        </div>
    </FullLayout>
</template>
