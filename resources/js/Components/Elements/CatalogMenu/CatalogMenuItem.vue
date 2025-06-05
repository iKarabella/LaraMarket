<script setup>
import { Link } from '@inertiajs/vue3';
import CatalogMenuBlock from './CatalogMenuBlock.vue';
import { ref } from 'vue';

defineProps({
    element:{type:Array, default:[]},
    parent:{type:Boolean, default:true}
});

const isOpen = ref(false);

</script>
<template>
    <div @mouseover="isOpen=true" @mouseleave="isOpen=false" class="relative px-3 py-2">
        <Link :href="element.link" class="px-3 py-2 hover:bg-gray-200">
            {{ element.title }}
        </Link>
        <CatalogMenuBlock v-show="isOpen" 
                          :elements="element.children" 
                          :parent="false"
                          class="absolute" 
                          :class="{'left-0':parent==true, 'left-full top-0':parent==false}"/>
    </div>
</template>