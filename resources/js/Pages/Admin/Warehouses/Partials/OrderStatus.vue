<script setup>
import { computed } from 'vue';

const props = defineProps({
    status:{type:Object, default:{}}
});

const classes = computed(()=>{
    let forall = 'font-semibold rounded-md p-1 text-white px-2';
    if ([5,7,9,10,12].includes(props.status.id)) return forall+' bg-green-500';
    else if ([6,8].includes(props.status.id)) return forall+' bg-orange-500';
    else if (props.status.id==11) return forall+' bg-red-500';
    else return forall;
});
</script> 

<template>
    <span :class="classes">
        {{ status.value }}
    </span>
</template>