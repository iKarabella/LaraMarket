<script setup>
import InputError from '@/Components/UI/InputError.vue';
import PrimaryButton from '@/Components/UI/PrimaryButton.vue';
import SecondaryButton from '@/Components/UI/SecondaryButton.vue';
import Tiptap from '@/Components/Tiptap/Tiptap.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    comments:{type:Array, default:[]},
    order:{type:Object},
    active:{type:Boolean, default:false}
});

const showAddCommentForm = ref(null);

const addCommentForm = useForm({
    order_id:props.order.id,
    comment:''
});

const storeComment = () => {
    if (props.active!==true) return false;
    addCommentForm.post(route('admin.order.addComment', [props.order.uuid]), {
        preserveScroll: true,
        onSuccess: () => {
            showAddCommentForm.value=false;
            addCommentForm.reset();
        },
        onError: (e) => console.log(e)
    });
};

const updateContent = (content)=>{
    addCommentForm.comment = content;
};

</script>
<template>
    <div>
        <div class="flex justify-between w-full p-2">
            <div>Служебные комментарии:</div>
            <SecondaryButton @click="showAddCommentForm=true" v-if="active">
                <i class="ri-edit-2-line"></i>
                Добавить комментарий
            </SecondaryButton>
        </div>
        <div v-if="active" v-show="showAddCommentForm">
            <Tiptap :content="addCommentForm.comment" @updateContent="updateContent"/>
            <InputError :message="addCommentForm.errors.comment" class="mt-2" />
            <div class="flex mt-2 justify-center">
                <SecondaryButton @click="showAddCommentForm=false" class="mr-2">Отменить</SecondaryButton>
                <PrimaryButton @click="storeComment()">Сохранить</PrimaryButton>
            </div>
        </div>
        <div>
            <div v-for="comment in comments" :key="comment.id" class="flex w-full border-t border-gray-300 mt-2">
                <div class="border-r border-gray-300 pr-2">
                    <div class="whitespace-nowrap">{{ comment.created_at }}</div>
                    <div v-show="comment.updated_at">
                        <div class="font-semibold italic text-xs">Обновлен:</div>
                        <div class="whitespace-nowrap">{{comment.updated_at}}</div>
                    </div>
                </div>
                <div class="w-full px-2">
                    <div v-show="comment.title" v-html="comment.title"/>
                    <div v-html="comment.comment"/>
                </div>
            </div>
        </div>
    </div>
</template>
