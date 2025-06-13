<script setup>
    const props = defineProps({
        content:{type:String, default:''}
    });

    const view = props.content;
</script>
<template>
    <div v-html="view"></div>
</template>