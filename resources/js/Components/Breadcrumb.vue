<script setup>
import { Link } from '@inertiajs/vue3';
import Dropdown from './UI/Dropdown.vue';
defineProps({
    breadcrumb:{type:Array, default:[]},
    home: {type:Boolean, default:true},
});
</script>
<template>
    <div class="w-full p-4">
        <nav aria-label="breadcrumb"> 
            <ol class="flex space-x-2 list-none">
                <li v-if="home"><Link :href="route('home')" class="after:content-['/'] after:ml-2 text-gray-600 hover:text-purple-700"><i class="ri-home-heart-line"></i></Link></li>
                <li v-for="(link, index) in breadcrumb">
                    <Dropdown v-if="Array.isArray(link.link)">
                        <template #trigger>
                            <span class="inline-flex rounded-md text-gray-600 after:ml-2" :class="{'after:content-[\'/\']':(index < breadcrumb.length-1)}">
                                <button
                                    type="button"
                                    class=" hover:text-purple-700 inline-flex items-center border border-transparent leading-4 rounded-md focus:outline-none transition ease-in-out duration-150"
                                >
                                    {{ link.title }}
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
                            <Link  v-for="(lk,index) in link.link" 
                                  :key="index" 
                                  :href="lk.link"                                    
                            >
                                <div class="rounded-md hover:bg-indigo-100">{{ lk.title }}</div>
                            </Link>
                        </template>
                    </Dropdown>
                    <template v-else>
                        <Link v-if="index < breadcrumb.length-1" :href="link.link" class="after:content-['/'] after:ml-2 text-gray-600 hover:text-purple-700">
                            {{ link.title }}
                        </Link>
                        <span v-else class="text-purple-700" aria-current="page">{{ link.title }}</span>
                    </template>
                </li>
            </ol>
        </nav>
    </div>
</template>
