<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import MarketLayout from '@/Layouts/MarketLayout.vue';
import InputLabel from '@/Components/UI/InputLabel.vue';
import TextInput from '@/Components/UI/TextInput.vue';
import InputError from '@/Components/UI/InputError.vue';
import SecondaryButton from '@/Components/UI/SecondaryButton.vue';
import Tiptap from '@/Components/Tiptap/Tiptap.vue';
import PrimaryButton from '@/Components/UI/PrimaryButton.vue';
import Checkbox from '@/Components/UI/Checkbox.vue';
import { ref } from 'vue';
//import MediaManage from './Partials/MediaManage.vue';

const props = defineProps({
    navigation: {type:Array, default:[]},
    categories: {type:Array, default:[]},
    product: {type:Object, default:{}},
    measures: {type:Array, default:[]}
});

const productForm = useForm({
    id:props.product.id??null,
    title:props.product.title??null,
    link:props.product.link??'',
    description:props.product.description??null,
    visibility:props.product.visibility??false,
    offersign:props.product.offersign??'',
    categories:props.product.categories??[],
    measure:props.product.measure??null,
    short_description:props.product.short_description??''
});

const selectedCatForAdd = ref(null);

const reCat = (parent=null, level=0)=>{
    let ret = [];
    props.categories.filter(arr=>arr.parent==parent).sort(function(a,b){return a.sort-b.sort;}).forEach(cat=>{
        ret.push({id:cat.id, title:cat.title, level:level});
        let child = reCat(cat.id, level+1);
        if (child.length>0) ret.push(...child);
    });
    return ret;
};

const addCat = ()=>{
    if (selectedCatForAdd.value == null || productForm.categories.findIndex(a=>a.id==selectedCatForAdd.value)>-1) return false;
    let find = props.categories.find(arr=>arr.id==selectedCatForAdd.value);

    productForm.categories.push({id:find.id, title:find.title});
};
            
const delCat = (e)=>{
    let index = productForm.categories.findIndex(a=>a.id==e);
    if (index>-1) productForm.categories.splice(index,1);
};

const updateContent = (content)=>{
    productForm.description=content;
};
            
const storeProduct = ()=>{
    productForm.post(route('admin.products.store'), {
        preserveScroll:true,
        onSuccess:(e)=>{
            productForm.reset();
        }, 
        onError:(e)=>console.log(e)
    });
};
</script>

<template>
    <MarketLayout :navigation="navigation">
        <Head title="Products" />
        <div>
            <div class="md:grid md:grid-cols-2 md:gap-2">
                <div class="mt-2">
                    <InputLabel for="title" value="Наименование" />
                    <TextInput
                        type="text"
                        id="title"
                        name="title"
                        class="w-full mt-1 block "
                        required
                        v-model="productForm.title"
                        autocomplete="off"
                    />
                    <InputError class="ml-2" :message="productForm.errors.title" />
                </div>
                <div class="mt-2">
                    <InputLabel for="link" value="Символьная ссылка" />
                    <TextInput
                        type="text"
                        id="link"
                        name="link"
                        class="w-full mt-1 block"
                        required
                        v-model="productForm.link"
                        autocomplete="off"
                    />
                    <InputError class="ml-2" :message="productForm.errors.link" />
                </div>
            </div>
            <div class="mt-2">
                <div class="w-full flex">
                    <div>
                        <InputLabel for="categories" value="Категории" />
                        <div>
                            <select class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    id="categories"
                                    v-model="selectedCatForAdd"
                            >
                                <option v-for="cat in reCat()" :key="cat.id" :value="cat.id">
                                    <span v-for="n in cat.level">-</span>
                                    {{cat.title}}
                                </option>
                            </select>
                            <SecondaryButton title="Добавить категорию" class="mx-2" @click="addCat()"><i class="ri-add-line"></i></SecondaryButton>
                        </div>
                    </div>
                    <div class="p-4 md:grid md:grid-cols-3 md:gap-2">
                        <div v-for="cat in productForm.categories">
                            {{ cat.title }} 
                            <span @click="delCat(cat.id)"><i class="ri-close-line"></i></span>
                        </div>
                    </div>
                </div>
                <InputError class="ml-2" :message="productForm.errors.categories" />
            </div>
            <div class="md:grid md:grid-cols-4 md:gap-2">
                <div class="mt-2 col-span-2">
                    <InputLabel for="offersign" value="Признак торгового предложения" />
                    <TextInput
                        type="text"
                        id="offersign"
                        name="offersign"
                        class="w-full mt-1 block"
                        required
                        v-model="productForm.offersign"
                        autocomplete="off"
                    />
                    <InputError class="ml-2" :message="productForm.errors.offersign" />
                </div>
                <div class="flex whitespace-nowrap mt-2 pt-6">
                    <Checkbox name="visibility" id="visibility" v-model="productForm.visibility" :checked="productForm.visibility" title="Будет доступен в каталоге"/>
                    <InputLabel class="ml-2" for="visibility" value=" - Доступен в каталоге"/>
                    <InputError class="ml-2" :message="productForm.errors.visibility" />
                </div>
                <div class="mt-2">
                    <InputLabel for="measure" value="Единица измерения" />
                    <select class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            v-model="productForm.measure"
                            id="measure" name="measure"
                    >
                        <option v-for="entity in measures" :key="entity.id" :value="entity.id" :title="entity.description">{{ entity.value }}</option>
                    </select>
                    <InputError class="ml-2" :message="productForm.errors.measure" />
                </div>
            </div>
            <div class="mt-2">
                <InputLabel for="short_description" value="Краткое описание" />
                <TextInput
                    type="text"
                    id="short_description"
                    name="short_description"
                    class="w-full mt-1 block"
                    required
                    v-model="productForm.short_description"
                    autocomplete="off"
                />
                <InputError class="ml-2" :message="productForm.errors.short_description" />
            </div>
            <div class="mt-2">
                <span>Описание</span>
                <Tiptap :content="productForm.description" @updateContent="updateContent"/>
                <InputError class="ml-2" :message="productForm.errors.description" />
            </div>
            <div class="mt-2">
                <MediaManage :media="productForm.media" :product="product.id"/>
            </div>
            <div v-if="product.id && product.link" class="mt-2">
                <div>
                    Торговые предложения:
                    <Link :href="route('admin.products.newOffer', [product.link])">
                        <SecondaryButton title="Добавить предложение"><i class="ri-add-line"></i></SecondaryButton>
                    </Link>
                </div>
                <div>
                    <div class="grid grid-cols-5 gap-2 text-gray-800">
                        <div>
                            <span class="mr-2">#</span>
                            название
                        </div>
                        <div>цена, руб</div>
                        <div>стоимость, руб</div>
                        <div>артикул</div>
                        <div>видимость</div>
                    </div>
                    <div v-for="(offer,index) in product.offers" class="grid grid-cols-5 gap-2 hover:bg-gray-200 rounded mt-1 py-2">
                        <div>
                            <span class="mr-2">{{index+1}}</span>
                            {{ offer.title }}
                        </div>
                        <div>{{ offer.price }}₽</div>
                        <div>{{ offer.baseprice }}₽</div>
                        <div>{{ offer.art }}</div>
                        <div class="text-center">
                            <i :class="{'ri-eye-line':offer.visibility, 'ri-eye-off-line':!offer.visibility}"></i>
                            <Link :href="route('admin.products.editOffer', [product.link, offer.id])" class="ml-2" title="Редактировать">
                                <SecondaryButton><i class="ri-edit-2-fill"></i></SecondaryButton>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-right mt-2">
                <Link :href="route('admin.catalog.manage')" class="mr-2">
                    <SecondaryButton>В каталог</SecondaryButton>
                </Link>
                <PrimaryButton @click="storeProduct">Сохранить</PrimaryButton>
            </div>
        </div>
    </MarketLayout>
</template>
