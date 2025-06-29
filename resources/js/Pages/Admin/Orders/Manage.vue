<script setup>
import MarketLayout from '@/Layouts/MarketLayout.vue';
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination';
import SecondaryButton from '@/Components/UI/SecondaryButton.vue';
import { Link } from '@inertiajs/vue3';
import Dropdown from '@/Components/UI/Dropdown.vue';
import Checkbox from '@/Components/UI/Checkbox.vue';
import Datepicker from '@vuepic/vue-datepicker';
import { DatepickerFormat } from '@/Mixins/DatepickerFormat';
import '@vuepic/vue-datepicker/dist/main.css';
import OrderStatus from './Partials/OrderStatus.vue';

const props = defineProps({
    orders: {type:Array, default:[]},
    navigation:{type:Array, default:[]},
    filters:{type:Object, default:{
        status:null,
        dateFrom:null,
        deteUntil:null,
    }}
});

let showOrderBodyId = ref(null);

const fChange = () => {
    console.log('fChange');
    useForm({filters:props.filters}).post(route('admin.orders.manage'), {
        preserveScroll:true,
    });
};

</script>

<template>
    <Head title="Orders Manage">
    </Head>
    <MarketLayout :navigation="navigation">
        <div>
            <div class="flex items-start w-full">
                <div class="relative">
                    <Dropdown align="right" width="48" :closeAfterClick="false">
                        <template #trigger>
                            <span class="inline-flex rounded-md">
                                <button
                                    type="button"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150"
                                >
                                    Статус заказа
                                    <svg
                                        class="ms-2 -me-0.5 h-4 w-4"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </button>
                            </span>
                        </template>
                        <template #content>
                            <div v-for="status in filters.statuses" :key="status.id" class="ml-2 mt-1 hover:bg-gray-100 rounded-md">
                                <label>
                                    <Checkbox v-model="status.on" :checked="status.on" @change="fChange"/> 
                                    {{status.name}}
                                </label>
                            </div>
                        </template>
                    </Dropdown>
                </div>
                <div>
                    <label class="mx-2 text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300">
                        <Checkbox v-model="filters.sortDesc" :checked="filters.sortDesc" @change="fChange"/> - сначала новые
                    </label>
                </div>
                <div>
                    <Datepicker v-model="filters.dates" range
                                @update:model-value="fChange"
                                :show-last-in-range="false"
                                :format="DatepickerFormat"
                                locale="ru"
                                position="left"
                                auto-position="bottom"
                                id="date"
                                placeholder="Период"
                                cancelText="Отменить"
                                selectText="Выбрать" 
                    />
                </div>
            </div>
            <div>
                <div v-for="order in orders.data" :key="order.id" class="hover:bg-gray-200 rounded-md mb-2">
                    <div class="grid grid-cols-6 gap-2">
                        <div>{{ order.id }}</div>
                        <div class="col-span-2">{{ order.delivery_string }}</div>
                        <div class="cursor-pointer" title="Просмотреть состав заказа" @click="showOrderBodyId=order.id">{{ (order.amount/100).toFixed(2) }} ₽</div>
                        <div>
                            <OrderStatus :status="order.status_info"/>
                        </div>
                        <div>
                            <Link :href="route('admin.order.manage', [order.uuid])"><SecondaryButton>Открыть</SecondaryButton></Link>
                        </div>
                    </div>
                    <div v-show="showOrderBodyId==order.id">
                        <div v-for="(position, index) in order.body" :key="index" class="grid grid-cols-9 gap-2">
                            <div>{{ index+1 }}</div>
                            <div class="col-span-5">{{ position.product_title }}, {{ position.offer_title }}</div>
                            <div>{{ (position.price/100).toFixed(2) }} ₽</div>
                            <div>{{ position.quantity }} {{ position.measure }}</div>
                            <div>{{ (position.total/100).toFixed(2) }} ₽</div>
                        </div>
                    </div>
                </div>
            </div>
            <Pagination :paginator="orders.meta"/>
        </div>
    </MarketLayout>
</template>