<script setup>
import Category from './Category.vue';
import SecondaryButton from '@/Components/UI/SecondaryButton.vue';
import DangerButton from '@/Components/UI/DangerButton.vue';
import Checkbox from '@/Components/UI/Checkbox.vue';
import Modal from '@/Components/Modals/MainModal.vue';
import PrimaryButton from '@/Components/UI/PrimaryButton.vue';
import InputError from '@/Components/UI/InputError.vue';
import Tiptap from '@/Components/Tiptap/Tiptap.vue';
import InputLabel from '@/Components/UI/InputLabel.vue';
import TextInput from '@/Components/UI/TextInput.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    categories: {type:Array, default:[]},
});

const emit = defineEmits(['select']);

const newCatForm = useForm({id:null, title:'', code:'', visibility:false, description:'', parent:null, image:null, parentcatname:''});
const editCatForm = useForm({id:null, title:'', code:'', visibility:false, description:'', parent:null, image:null, parentcatname:''});
const deleteCatForm = useForm({id:null});

const modalNewCat = ref(false);
const modalEditCat = ref(false);
const modalDeleteCat = ref(false);


const selectedCat = ref(null);
const selectedCatInfo = ref ({});

const catSelect = (e)=>{
    emit('select', e);
    if(e==selectedCat.value){
        selectedCat.value = null;
        selectedCatInfo.value = {};
        newCatForm.reset();
        editCatForm.reset();
        deleteCatForm.reset();
    }
    else{
        selectedCat.value = e;
        let find = props.categories.find(arr=>arr.id==e);
        if (find) {
            selectedCatInfo.value = Object.assign(find);
            newCatForm.parent = e;
            newCatForm.parentcatname = find.title;
            editCatForm.id = find.id;
            editCatForm.title = find.title;
            editCatForm.code = find.code;
            editCatForm.visibility = find.visibility;
            editCatForm.description = find.description;
            editCatForm.parent = find.parent;
            editCatForm.image = find.image;
            editCatForm.parentcatname = find.title;
            deleteCatForm.id = e;
        }
        else {
            selectedCatInfo.value = {};
            newCatForm.reset();
            editCatForm.reset();
            deleteCatForm.reset();
        }
    }
};

const closeModal = () => {
    modalDeleteCat.value = false;
    modalEditCat.value = false;
    modalNewCat.value = false;
};

const deleteCat = ()=>{
    deleteCatForm.delete(route('admin.catalog.catsManage'), {
        preserveScroll: true,
        onSuccess:()=>{
            closeModal();
            selectedCat.value = null,
            selectedCatInfo.value = {};
            newCatForm.reset();
            editCatForm.reset();
            deleteCatForm.reset();
        }
    });
};

const editCat = ()=>{
    editCatForm.post(route('admin.catalog.catsManage'), {
        preserveScroll: true,
        onSuccess:()=>closeModal()
    });
};

const newCat = ()=>{
    newCatForm.post(route('admin.catalog.catsManage'), {
        preserveScroll: true,
        onSuccess:()=>{
            closeModal();
            newCatForm.reset();
            newCatForm.parent=selectedCat.value;
            newCatForm.parentcatname=selectedCatInfo.value.title;
        }
    });
};

const updateContent = (content)=>{
    if(modalEditCat.value) editCatForm.description=content;
    else if (modalNewCat.value) newCatForm.description=content;
};

const changeSort = (up=true)=>{
    if (selectedCat.value==null) return false;
                
    let find = props.categories.find(arr=>arr.id==selectedCat.value);

    if (!find) return false;

    useForm({id:selectedCat.value, sort:(up==true?find.sort-1:find.sort+1)}).post(route('admin.catalog.catsSortManage'), {
        preserveScroll:true,
        onSuccess:()=>selectedCatInfo.value.sort=(up==true?find.sort-1:find.sort+1)
    });
};

const openCatEdit = () =>{
    if (selectedCat.value==null) return false;
    modalEditCat.value=true;
};
const openCatDelete = () =>{
    if (selectedCat.value==null) return false;
    modalDeleteCat.value=true;
};
</script>
<template>
    <div>
        <div class="w-full flex">
                <div class="w-4/12 max-h-screen overflow-y-scroll overflow-x-auto py-4">
                    <Category v-for="(cat, index) in categories.filter(a=>a.parent==null).sort(function(a,b){return a.sort-b.sort;})"
                            :categories="categories" 
                            :cat="cat" v-key="index" 
                            :selected="selectedCat"
                            @select="catSelect"/>
                </div>
                <div class="w-8/12">
                    <div v-show="selectedCat!=null" class="p-2">
                        <div class="flex w-full">
                            <div class="w-full"><b>Категория:</b> {{selectedCatInfo.title}}</div>
                            <div class="whitespace-nowrap" title="Индекс сортировки">Сорт.:{{selectedCatInfo.sort}}</div>
                        </div>
                        <div>
                            <div>Описание:</div>
                            <div v-html="selectedCatInfo.description"></div>
                        </div>
                        <div>
                            <Checkbox :disabled="true" :checked="selectedCatInfo.visibility==true"/> - опубликована
                        </div>
                    </div>
                    <div class="text-right">
                        <Link :href="route('admin.products.manage')" :data="{'category':selectedCat}">
                            <PrimaryButton class="mr-2" title="Добавить товар">
                                <i class="ri-add-line mr-2"></i> Добавить товар
                            </PrimaryButton>
                        </Link>
                        <SecondaryButton :disabled="selectedCat==null" class="mr-2" title="Порядок выше" @click="changeSort()">
                            <i class="ri-arrow-up-long-line"></i>
                        </SecondaryButton>
                        <SecondaryButton :disabled="selectedCat==null" class="mr-2" title="Порядок ниже" @click="changeSort(false)">
                            <i class="ri-arrow-down-long-line"></i>
                        </SecondaryButton>
                        <SecondaryButton title="Создать новую категорию" class="mr-2" @click="modalNewCat=true">
                            <i class="ri-menu-add-fill"></i>
                        </SecondaryButton>
                        <SecondaryButton :disabled="selectedCat==null" title="Редактировать категорию" class="mr-2" @click="openCatEdit()">
                            <i class="ri-edit-2-line"></i>
                        </SecondaryButton>
                        <DangerButton :disabled="selectedCat==null" title="Удалить категорию" class="mr-2" @click="openCatDelete()">
                            <i class="ri-delete-bin-line"></i>
                        </DangerButton>
                    </div>
                </div>
            </div>
            <Modal :show="modalNewCat" @close="closeModal">
                <div class="p-6">
                    <div>
                        Новая категория
                    </div>
                    <div class="mt-2">
                        Родительская категория: {{ newCatForm.parentcatname==''?' - ':newCatForm.parentcatname }}
                    </div>
                    <div class="mt-2">
                        <InputLabel for="title" value="Название" />
                        <TextInput
                            type="text"
                            id="title"
                            name="title"
                            class="w-full mt-1 block"
                            required
                            v-model="newCatForm.title"
                            autocomplete="off"
                        />
                        <InputError class="ml-2" :message="newCatForm.errors.title" />
                    </div>
                    <div class="mt-2">
                        <InputLabel for="title" value="Код ссылки" />
                        <TextInput
                            type="text"
                            id="title"
                            name="title"
                            class="w-full mt-1 block"
                            required
                            v-model="newCatForm.code"
                            autocomplete="off"
                        />
                        <InputError class="ml-2" :message="newCatForm.errors.code" />
                    </div>
                    <div class="mt-2">
                        <Tiptap :content="newCatForm.description"
                                @updateContent='updateContent'
                        />
                        <InputError class="ml-2" :message="newCatForm.errors.description" />
                    </div>
                    <div class="flex whitespace-nowrap mt-2">
                        <Checkbox name="visibility" id="visibility" v-model="newCatForm.visibility" :checked="newCatForm.visibility" title="Будет доступна в каталоге"/>
                        <InputLabel class="ml-2" for="visibility" value=" - Опубликована"/>
                    </div>
                    <div class="mt-2 text-right">
                        <SecondaryButton class="mr-2" @click="closeModal">Отменить</SecondaryButton>
                        <PrimaryButton class="mr-2" @click="newCat">Сохранить</PrimaryButton>
                    </div>
                </div>
            </Modal>
            <Modal :show="modalEditCat" @close="closeModal">
                <div class="p-6">
                    <div class="mt-2">
                        <InputLabel for="title" value="Название" />
                        <TextInput
                            type="text"
                            id="title"
                            name="title"
                            class="w-full mt-1 block"
                            required
                            v-model="editCatForm.title"
                            autocomplete="off"
                        />
                        <InputError class="ml-2" :message="editCatForm.errors.title" />
                    </div>
                    <div class="mt-2">
                        <!--TODO изменение родительской категории-->
                        Родительская категория: {{ editCatForm.parentcatname==''?' - ':editCatForm.parentcatname }}
                    </div>
                    <div class="mt-2">
                        <InputLabel for="title" value="Код ссылки" />
                        <TextInput
                            type="text"
                            id="title"
                            name="title"
                            class="w-full mt-1 block"
                            required
                            v-model="editCatForm.code"
                            autocomplete="off"
                        />
                        <InputError class="ml-2" :message="editCatForm.errors.code" />
                    </div>
                    <div class="mt-2">
                        <Tiptap :content="editCatForm.description"
                                @updateContent='updateContent'
                        />
                        <InputError class="ml-2" :message="editCatForm.errors.description" />
                    </div>
                    <div class="flex whitespace-nowrap mt-2">
                        <Checkbox name="visibility" id="visibility" v-model="editCatForm.visibility" :checked="editCatForm.visibility" title="Будет доступна в каталоге"/>
                        <InputLabel class="ml-2" for="visibility" value=" - Опубликована"/>
                    </div>
                    <div class="mt-2 text-right">
                        <SecondaryButton class="mr-2" @click="closeModal">Отменить</SecondaryButton>
                        <PrimaryButton class="mr-2" @click="editCat">Сохранить</PrimaryButton>
                    </div>
                </div>
            </Modal>
            <Modal :show="modalDeleteCat" @close="closeModal">
                <div class="p-6">
                    <div class="font-semibold text-lg">Подтвердите удаление категории.</div>
                    <div>Дочерние категории будут так же удалены. Это действие необратимо.</div>
                    <div class="mt-2">
                        <InputError :message="deleteCatForm.errors.id" />
                    </div>
                    <div class="text-ring mt-2">
                        <DangerButton class="mr-2" @click="deleteCat">Удалить категорию</DangerButton>
                        <PrimaryButton class="mr-2" @click="closeModal">Отменить удаление</PrimaryButton>
                    </div>
                </div>
            </Modal>
    </div>
</template>