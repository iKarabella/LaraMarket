<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import PrimaryButton from '@/Components/UI/PrimaryButton.vue';
import FullLayout from '@/Layouts/FullLayout.vue';
import NotifyAboutAdmission from '@/Components/Elements/NotifyAboutAdmission.vue';
import { usercart, addToCart, removeFromCart } from '@/Mixins/UserCart.js';
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    product: {type:Object, default:{}}, 
    breadcrumb:{type:Array, default:[]},
    canManage:{type:Boolean, default:false}
});

const currentImage = ref(0);
const currentOffer = ref(0);

const inCart = computed(() => {
    if (!usercart || !usercart.value) return {};
    
    let find = usercart.value.find(a=>a.position == props.product.id && a.offer == props.product.offers[currentOffer.value].id);
    return find?find:{};
});

const noMore = computed(() => {
    return inCart.value.quantity>=props.product.offers[currentOffer.value].available;
});

const cardImages = computed(()=>{
    return props.product.media; //[...this.product.offers[this.selectedOfferIndex]?this.product.offers[this.selectedOfferIndex].media:[], ...this.product.media];
});

const changeCart = (remove=false) => {
    if(!remove && noMore.value) return false;
    if(remove) removeFromCart({position:props.product.id, offer:props.product.offers[currentOffer.value].id});
    else addToCart({position:props.product.id, offer:props.product.offers[currentOffer.value].id});
}

const showNotifyAboutAdmission = ref(null);

const notifyAboutAdmission = () => {
    showNotifyAboutAdmission.value = {
        offer_id: props.product.offers[currentOffer.value].id,
        product_id: props.product.id
    };
};

const closeNotify = ()=>{
    showNotifyAboutAdmission.value = null;
};

const selectOffer = (index)=>{
    currentOffer.value = index;
};

</script>
<template>
    <Head title="Marketzone" />

    <FullLayout>
        <div class="py-4">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2">

                    <!--Breadcrumb-->
                    <Breadcrumb v-if="breadcrumb" :breadcrumb="breadcrumb"/>

                    <!--Product main info-->
                    <div class="md:flex w-full relative">
                        <div class="absolute top-0 z-50 p-2" v-if="canManage">
                            <Link :href="route('admin.products.edit', [product.code])">
                                <i class="ri-settings-4-line"></i>
                            </Link>
                        </div>
                        <!--Images-->
                        <div class="min-w-[30%] relative mx-4 mt-4 overflow-hidden text-gray-700 bg-clip-border rounded-xl h-96">
                            <div class="relative">
                                <!-- Carousel wrapper -->
                                <div class="overflow-hidden relative rounded-lg">
                                    <!-- Items -->
                                    <div v-for="(media, index) in cardImages" :key="media.id" class="duration-700 ease-in-out" :class="{'hidden':currentImage!=index}">
                                        <img :src="media.preview" class="block w-full" alt="...">
                                    </div>
                                </div>
                                <!-- Slider indicators -->
                                <div v-if="cardImages.length>1" class="flex absolute bottom-5 left-1/2 z-30 space-x-3 -translate-x-1/2">
                                    <button v-for="(media, index) in cardImages" :key="index" 
                                            type="button" 
                                            class="w-3 h-3 rounded-full bg-gray-500"
                                            :class="{'opacity-20':currentImage==index, 'opacity-60':currentImage!=index}"
                                            @click="currentImage=index"></button>
                                </div>
                                <!-- Slider controls -->
                                <button v-if="cardImages.length>1" type="button" class="flex absolute top-0 left-0 z-30 justify-center items-center h-full cursor-pointer group focus:outline-none" @click="moveImg(false)">
                                    <span class="inline-flex justify-center items-center w-8 h-8 rounded-full sm:w-10 sm:h-10 bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                                        <svg class="w-5 h-5 text-white sm:w-6 sm:h-6 dark:text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                        <span class="hidden"><</span>
                                    </span>
                                </button>
                                <button v-if="cardImages.length>1" type="button" class="flex absolute top-0 right-0 z-30 justify-center items-center h-full cursor-pointer group focus:outline-none" @click="moveImg()">
                                    <span class="inline-flex justify-center items-center w-8 h-8 rounded-full sm:w-10 sm:h-10 bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                                        <svg class="w-5 h-5 text-white sm:w-6 sm:h-6 dark:text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        <span class="hidden">></span>
                                    </span>
                                </button>
                            </div>
                        </div>

                        <!--Product info block-->
                        <div class="m-4">
                            <h1>{{ product.title }}</h1>
                            <div v-if="product.offers.length>1">
                                <div v-if="product.offersign">{{ product.offersign }}:</div>
                                <div>
                                    <div v-for="(offer, index) in product.offers" 
                                         class="border border-opacity-0 border-gray-400 cursor-pointer"
                                        :key="index" 
                                        :class="{'border-opacity-90':index==currentOffer}"
                                        @click="selectOffer(index)"
                                    >
                                        {{ offer.title }}
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div>{{ product.offers[currentOffer].price }}₽</div>
                                <div v-show="product.offers[currentOffer].available" class="m-4 bg-white">
                                    <PrimaryButton v-show="!inCart.quantity" title="Добавить в корзину" @click="changeCart()" class="w-full">
                                        <i class="ri-shopping-basket-2-line mr-2"></i> Добавить
                                    </PrimaryButton>
                                    <div v-show="inCart.quantity" class="flex justify-between items-center relative whitespace-nowrap font-semibold tracking-widest 
                                                                         transition ease-in-out duration-150 min-w-32">
                                        <div class="rounded-md cursor-pointer w-9 h-9 flex justify-center items-center bg-indigo-200 text-indigo-700 hover:bg-indigo-100" @click="changeCart(true)" title="-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"><path fill="currentColor" d="M5 11a1 1 0 1 0 0 2h14a1 1 0 1 0 0-2z"></path></svg>
                                        </div>
                                        <span>{{ inCart.quantity }}</span>
                                        <div class="rounded-md cursor-pointer w-9 h-9 flex justify-center items-center bg-indigo-200 text-indigo-700 hover:bg-indigo-100" @click="changeCart()" title="+1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"><path fill="currentColor" d="M12 4a1 1 0 0 0-1 1v6H5a1 1 0 1 0 0 2h6v6a1 1 0 1 0 2 0v-6h6a1 1 0 1 0 0-2h-6V5a1 1 0 0 0-1-1"></path></svg>
                                        </div>
                                    </div>
                                </div>
                                <div v-show="!product.offers[currentOffer].available" class="m-4 bg-white">
                                    <PrimaryButton v-if="product.offers[currentOffer].notify" title="Сообщим как только товар появится в продаже" class="w-full" :disabled="true">
                                        <i class="ri-check-line mr-2"></i> Сообщим
                                    </PrimaryButton>
                                    <PrimaryButton v-else title="Сообщить, как только товар появится в продаже" class="w-full" @click="notifyAboutAdmission">
                                        <i class="ri-notification-2-line mr-2"></i> Сообщить
                                    </PrimaryButton>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--Product description-->
                    <div v-html="product.description"></div>
                </div>
            </div>
        </div>
        <NotifyAboutAdmission :show="showNotifyAboutAdmission" @closeNotify="closeNotify" :auth="$page.props.auth"/>
    </FullLayout>
</template>