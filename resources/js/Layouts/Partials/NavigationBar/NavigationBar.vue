<script setup>
    import NavigationBarLink from './NavigationBarLink.vue';
    defineProps({
        sections:{type:Array, default:[]}, 
        current: {type:String, default:null}
    });
</script>
<template>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 rounded border py-2 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
        <div class="flex justify-between">
            <NavigationBarLink v-for="(element, index) in sections"
                  :key="index"
                  :element="element"
            />
        </div>
    </div>
</template>