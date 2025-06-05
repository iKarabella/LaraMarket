<script setup>
import { Link } from '@inertiajs/vue3';
import CatalogMenuItem from './CatalogMenuItem.vue';

defineProps({
    elements:{type:Array, default:[]},
    parent:{type:Boolean, default:true}
});


</script>
<template>
    <div>
        <CatalogMenuItem v-for="(element, index) in elements"
              :key="index"
              :element="element"
              :parent="parent"
        />
    </div>
</template>