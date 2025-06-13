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
</script>
<script>
    export default {
        props:{
            rights: {type:Array, default:[]},
            product: {type:Object, default:{}},
            categories: {type:Array, default:[]},
            entities: {type:Array, default:[]}
        },
        data(){
            return {
                product:this.product,
                productForm: useForm({
                    id:this.product.id??null,
                    title:this.product.title??null,
                    link:this.product.link??'',
                    description:this.product.description??null,
                    visibility:this.product.visibility??false,
                    offersign:this.product.offersign??'',
                    categories:this.product.categories??[],
                    measure:this.product.measure??null,
                    media:this.product.media??[],
                    short_description:this.product.short_description??''
                }),
                selectedCatForAdd:null,
            }
        },
        computed:{
            cats_in_select:function(){
                return this.reCat();
            }
        },
        methods: {
            reCat: function(parent=null, level=0){
                let ret = [];
                this.categories.filter(arr=>arr.parent==parent).sort(function(a,b){return a.sort-b.sort;}).forEach(cat=>{
                    ret.push({id:cat.id, title:cat.title, level:level});
                    let child = this.reCat(cat.id, level+1);
                    if (child.length>0) ret.push(...child);
                });
                return ret;
            },
            addCat(){
                if (this.selectedCatForAdd == null || this.productForm.categories.findIndex(a=>a.id==this.selectedCatForAdd)>-1) return false;
                let find = this.categories.find(arr=>arr.id==this.selectedCatForAdd);

                this.productForm.categories.push({id:find.id, title:find.title});
            },
            delCat(e){
                let index = this.productForm.categories.findIndex(a=>a.id==e);
                if (index>-1) this.productForm.categories.splice(index,1);
            },
            updateContent: function(content){
                this.productForm.description=content;
            },
            storeProduct: function(){
                this.productForm.post(route('market.manage.storeProduct'), {
                    preserveScroll:true,
                    onSuccess:(e)=>{
                        this.productForm=useForm({
                            id:this.product.id??null,
                            title:this.product.title??null,
                            link:this.product.link??'',
                            description:this.product.description??null,
                            visibility:this.product.visibility??false,
                            offersign:this.product.offersign??'',
                            categories:this.product.categories??[],
                            measure:this.product.measure??null,
                            media:this.product.media??[]
                        });
                    }, 
                    onError:(e)=>console.log(e)
                });
            }
        }
    }
</script>

<template>
    <MarketLayout :rights="rights" :section="section" :header="product.id?'Редактирование продукта':'Создание продукта'">
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
                                <option v-for="cat in cats_in_select" :key="cat.id" :value="cat.id">
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
                        <option v-for="entity in entities" :key="entity.id" :value="entity.id" :title="entity.description">{{ entity.value }}</option>
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
                    <Link :href="route('market.manage.product.newOffer', [product.link])">
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
                            <Link :href="route('market.manage.product.offer', [product.link, offer.id])" class="ml-2" title="Редактировать">
                                <SecondaryButton><i class="ri-edit-2-fill"></i></SecondaryButton>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-right mt-2">
                <Link :href="route('market.manage.catalog')" class="mr-2">
                    <SecondaryButton>В каталог</SecondaryButton>
                </Link>
                <PrimaryButton @click="storeProduct">Сохранить</PrimaryButton>
            </div>
        </div>
    </MarketLayout>
</template>
