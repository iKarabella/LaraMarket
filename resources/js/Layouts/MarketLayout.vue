<script setup>
import FullLayout from '@/Layouts/FullLayout.vue';
import NavigationBar from './Partials/NavigationBar/NavigationBar.vue';
defineProps({navigation:{type:Array, default:[]}});
</script>

<template>
    <FullLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <NavigationBar :sections="navigation"/>
            </div>
            <div class="mt-2 max-w-7xl mx-auto sm:px-6 lg:px-8 rounded border py-2 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <slot />
            </div>
        </div>
    </FullLayout>
</template>
