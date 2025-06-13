<script setup>
    import { Link } from '@inertiajs/vue3';
    defineProps({
        element:{type:Object, default:{}}, 
    });
</script>
<template>
    <Link :href="element.link" 
          :class="{'border-gray-300/100':element.active, 'border-gray-300/0':!element.active}"
          class="rounded border py-1 px-2 "
    >
        {{element.name}}
    </Link>
</template>