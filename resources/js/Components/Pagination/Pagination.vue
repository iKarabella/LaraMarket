<script setup>
import SecondaryButton from '@/Components/UI/SecondaryButton.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    paginator:{ 
        type:Object, 
        default:{
            current_page:1,
            links:[], //TODO может быть, что ссылок будет дохрена?
            first_page_url: '',
            from: 1,
            last_page: 1,
            last_page_url: '',
            next_page_url: null,
            path: '',
            per_page: 1,
            prev_page_url: null,
            to: 1,
            total: 1
        },
    },
    lineClass: {
        type:String,
        default: "w-full flex justify-end mb-2"
    },
    itemClass:{
        type:String,
        default:"mx-2"
    },
    href:{type:Boolean, default:true}
});

const emits = defineEmits({getPage:null});

const sendEmit = (link) => {
    if (link!=null) emits('getPage', link);
};
</script>
<template>
    <div :class="props.lineClass" v-if="props.paginator.links.length>3">
        <template v-if="props.href">
            <div v-for="link in props.paginator.links" :class="props.itemClass">
                <Link v-if="link.url" :href="link.url">
                    <SecondaryButton :selected="link.active" :disable="link.url==null" v-html="link.label"></SecondaryButton>
                </Link>
                <SecondaryButton v-else :selected="link.active" :disable="link.url==null" v-html="link.label"></SecondaryButton>
            </div>
        </template>
        <template v-else>
            <div v-for="link in props.paginator.links" :class="props.itemClass">
                <SecondaryButton :selected="link.active" :disable="link.url==null" v-html="link.label" @click="sendEmit(link.label)"></SecondaryButton>
            </div>
        </template>
    </div>
</template>
