<script setup>
    import SecondaryButton from '@/Components/UI/SecondaryButton.vue';
    import InputError from '@/Components/UI/InputError.vue';
    import draggable from 'vuedraggable';
    import { useForm } from '@inertiajs/vue3';
    import { ref } from 'vue';
</script>
<script>
export default {
        props:{
            media:{type:Array, default:[]},
            product:{type:Number, default:null},
            offer:{type:Number, default:null}
        },
        emits:{select:null},
        data(){
            return {
                mediaForm:useForm({
                    product_id:this.product,
                    offer_id:this.offer,
                    files:[]
                }),
                mediaSort: useForm({
                    files:this.media
                }),
                mediaRemove: useForm({
                    id:null
                })
            }
        },
        computed:{
            //
        },
        methods: {
            handleFileUpload: function(){
                if(this.$refs.file.files.length<1) return false;

                this.mediaForm.files=this.$refs.file.files;
                this.mediaForm.post(route('admin.products.media'), {
                    preserveScroll: true,
                    onSuccess: (e) => {
                        this.mediaForm.reset();
                        this.mediaSort.files = this.media;
                    },
                    onError: () => {
                        console.log('error response', this.mediaForm.errors);
                    },
                });
            },
            openFileUpload(){
                this.$refs.file.click();
            },
            dragEnd:function(){
                this.mediaSort.files.map((arr,index)=>{
                    return arr.sort=index+1;
                });
                
                this.mediaSort.post(route('admin.products.media.sorting'), {preserveScroll: true});
            },
            removeFile: function(id){
                this.mediaRemove.id = id;
                this.mediaRemove.post(route('admin.products.media.remove'), {
                    preserveScroll: true,
                    onSuccess: () => {
                        let find = this.mediaSort.files.findIndex(arr=>arr.id==id);
                        this.mediaSort.files.splice(find, 1);
                    }
                });
            }
        }
    }
</script>

<template>
    <div class="p-2 rounded-md border border-gray-200">
        <div class="flex ml-3">
            <SecondaryButton @click="openFileUpload()"><i class="ri-image-add-line"></i> Добавить</SecondaryButton>
            <InputError class="mt-2" :message="mediaForm.errors.avatar" />
        </div>
        <div>
            <draggable v-model="mediaSort.files" tag="div" class="md:grid md:grid-cols-5 md:gap-2 p-2" @end="dragEnd">
                <template #item="{ element: file }">
                    <div class="rounded-md border border-gray-200 relative m-1 p-1">
                        <img :src="file.preview">
                        <SecondaryButton class="opacity-50 text-red-700 hover:opacity-100 absolute top-1 right-1" title="Удалить" @click="removeFile(file.id)">
                            <i class="ri-delete-bin-7-line"></i>
                        </SecondaryButton>
                    </div>
                </template>
            </draggable>
        </div>
        <input type="file" class="hidden" ref="file" v-on:change="handleFileUpload()" multiple/>
    </div>
</template>
