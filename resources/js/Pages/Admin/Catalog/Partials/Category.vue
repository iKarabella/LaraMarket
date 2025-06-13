<script setup>
import { computed } from 'vue';
import Category from './Category.vue';

const props = defineProps({
    cat: {type: Object, default:{}},
    level:{type:Number, default:0},
    categories:{type:Array, default:[]},
    selected:{type:Number, default:null}
});
const emits = defineEmits({select:null});

const catSelect = (e) => emits('select', e);

const children = computed(function(){
    return props.categories.filter(a=>a.parent==props.cat.id).sort(function(a,b){return a.sort-b.sort;});
});
</script>

<template>
    <div :style="`padding-left:${level*10}px;`">
        <div class="hover:bg-gray-200 rounded my-1 p-1 flex" 
            :class="{'bg-gray-300':selected==cat.id}" 
            @click="emits('select', cat.id)"
        >
            {{cat.title}}
        </div>
        <Category v-for="c in children" :categories="categories" :cat="c" :level="level+1" @select="catSelect" :selected="selected"/>
    </div>
</template>
