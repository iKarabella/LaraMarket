<script setup>
import { Link } from '@inertiajs/vue3';
import { usercart } from '@/Mixins/UserCart.js';
import { useStorage, onClickOutside } from '@vueuse/core'
import { computed, ref, useTemplateRef } from 'vue';
import { router } from '@inertiajs/vue3';

const searchInputFocus = ref(false);
const defaultResults = {
    found:[],
    categories:[],
    autocomplete:[],
    recommended:[],
    frequently:[],
};
const searchResults = ref(defaultResults);
const searchStringRef = ref();
const searchString = ref('');

const search = (relocation=false)=>{
    if (searchString.value.length<2) return;

    if (relocation) {
        if (prevSearched.value.length>2) prevSearched.value.pop();
        prevSearched.value.unshift(searchString.value);
        router.get(route('searchProducts'), {search:searchString.value});
    }
    else axios.post(route('searchProducts'), {search:searchString.value}).then(res=>{
        if (res.data) {
            searchResults.value = res.data;
        }
    });
};

const showSearchBlock = computed(()=>{
    return (
        searchResults.value.found.length>0 ||
        searchResults.value.categories.length>0 ||
        searchResults.value.autocomplete.length>0 ||
        searchResults.value.recommended.length>0 ||
        searchResults.value.frequently.length>0
    ) || (searchInputFocus.value && prevSearched.value.length);
});

const resetSearch = ()=>{
    searchString.value='';
    searchStringRef.value.focus();
};

const prevSearched = useStorage('previously_searched', [])

const searchResultBlock = useTemplateRef('searchResultBlock');

const searchInputInFocus = ()=>{
    if (searchString.value.length>1) search();
    searchInputFocus.value=true;
};

onClickOutside(searchResultBlock, event => {
    searchResults.value = defaultResults;
});
</script>
<template>
    <div class="max-w-7xl mx-auto py-4">
        <div class="flex justify-between">
            <div class='mr-5'>
                <Link :href="route('catalog')" class="whitespace-nowrap">
                    <i class="ri-menu-line"></i>
                    Каталог
                </Link>
            </div>
            <div class="w-full relative">
                <div class="flex w-full rounded bg-white">
                    <input class="w-full border-none bg-transparent px-4 py-1 text-gray-400 outline-none focus:outline-none"
                           autocomplete="off" 
                           ref="searchStringRef"
                           type="search" 
                           name="search" 
                           placeholder="Поиск..." 
                           @focus="searchInputInFocus"
                           @blur="searchInputFocus=false"
                           v-on:keyup="search()"
                           v-model="searchString"
                    />
                    <button type="submit" @click="search(true)" class="m-1 rounded bg-orange-600 hover:bg-orange-500 px-4 py-2 text-white cursor-pointer">
                        <i class="ri-search-line"></i>
                    </button>
                </div>
                <Transition enter-active-class="ease-out duration-300"
                            enter-from-class="opacity-0"
                            enter-to-class="opacity-100"
                            leave-active-class="ease-in duration-200"
                            leave-from-class="opacity-100"
                            leave-to-class="opacity-0"
                >
                    <div v-show="showSearchBlock" ref="searchResultBlock" class="absolute w-full bg-white rounded-b-sm p-2 z-50">
                        <div>
                            <div class="font-semibold italic text-sm">История</div>
                            <ul>
                                <li v-for="(str, index) in prevSearched" :key="index">
                                    <Link :href="route('searchProducts')" :data="{search:str}">
                                        <i class="ri-history-line"></i> 
                                        {{ str }}
                                    </Link>
                                </li>
                            </ul>
                        </div>
                        <div v-show="searchResults.categories.length>0">
                            <div class="font-semibold italic text-sm">Искать в категории</div>
                            <div class="md:grid md:grid-cols-5 md:gap-2">                                
                                <Link v-for="(cat, index) in searchResults.categories" 
                                      :key="index" 
                                      :href="route('searchProducts')" 
                                      :data="{search:searchString, filters:[{category:cat.id}]}"
                                      class="border border-orange-300 bg-orange-200 hover:bg-orange-300 rounded"
                                >
                                    <i class="ri-menu-search-line"></i> 
                                    {{ cat.title }}
                                </Link>
                            </div>
                        </div>
                        <div v-show="searchResults.found.length>0">
                            <div class="font-semibold italic text-sm">Товары</div>
                            <div class="p-2 lg:grid lg:grid-cols-2 xl:grid-cols-3 lg:gap-2">
                                <div v-for="(position, index) in searchResults.found" :key="index" :position="position">
                                    <div>{{ position.title }}</div>
                                    <div class="text-right">от {{ position.price }} ₽</div>
                                </div>
                            </div>
                            <div class="text-center w-full">
                                <button @click="search(true)" class="m-1 rounded bg-orange-600 hover:bg-orange-500 px-4 py-2 text-white cursor-pointer">
                                    Все результаты
                                </button>
                            </div>
                        </div>
                        <div v-show="searchResults.found.length<1 && searchString.length>2" class="text-center w-full">
                            По поиску «{{searchString}}» результаты не найдены. <br/>
                            Попробуйте изменить запрос.<br/>
                            <button @click="resetSearch" class="m-1 rounded bg-orange-600 hover:bg-orange-500 px-4 py-2 text-white cursor-pointer">
                                Изменить запрос
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
            <div class="ml-5">
                <Link :href="route('user.cart')" class="rounded-md hover:bg-indigo-50 text-indigo-700 p-2 relative" title="Открыть корзину">
                    <i class="ri-shopping-basket-2-line text-xl m-2"></i>
                    <div v-show="usercart.length>0" class="absolute top-0 right-0">
                        {{ usercart.length }}
                    </div>
                </Link>
            </div>
        </div>
    </div>
</template>