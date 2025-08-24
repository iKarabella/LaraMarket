<script setup>
import MarketLayout from '@/Layouts/MarketLayout.vue';
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination';
import SecondaryButton from '@/Components/UI/SecondaryButton.vue';
import { Link } from '@inertiajs/vue3';
import '@vuepic/vue-datepicker/dist/main.css';

const props = defineProps({
    orders: {type:Array, default:[]},
    navigation:{type:Array, default:[]},
});

let showOrderBodyId = ref(null);
</script>

<template>
    <Head title="Orders Manage">
    </Head>
    <MarketLayout :navigation="navigation">
        <div>
            <div>
                <div v-for="order in orders.data" :key="order.id" class="hover:bg-gray-200 rounded-md mb-2">
                    <div class="grid grid-cols-6 gap-2">
                        <div>{{ order.id }}</div>
                        <div class="col-span-2">{{ order.address }}</div>
                        <div class="cursor-pointer" title="Просмотреть состав заказа" @click="showOrderBodyId=order.id">asdf</div>
                        <div>
                            zxcv
                        </div>
                        <div>
                            <Link><SecondaryButton>Открыть</SecondaryButton></Link>
                        </div>
                    </div>
                    <div v-show="showOrderBodyId==order.id">
                        //
                    </div>
                </div>
            </div>
            <Pagination :paginator="orders.meta"/>
        </div>
    </MarketLayout>
</template>