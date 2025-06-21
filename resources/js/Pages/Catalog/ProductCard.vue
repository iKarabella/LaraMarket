<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import PrimaryButton from '@/Components/UI/PrimaryButton.vue';
import FullLayout from '@/Layouts/FullLayout.vue';
</script>
<script>
    export default {
        props:{
            product:{type:Object, default:{}},
            userCart:{type:Array, default:[]},
            breadcrumb:{type:Array, default:[]}
        },
        data(){
            return {
                selectedOfferIndex:0,
                currentImage:0,
                usercart:[],
            }
        },
        mounted(){
            if (localStorage.getItem('user_cart')) {
                try {
                    this.usercart = JSON.parse(localStorage.getItem('user_cart'));
                } catch(e) {
                    localStorage.removeItem('user_cart');
                }
            }

            if (!this.usercart.length && Array.isArray(this.userCart) && this.userCart.length) this.usercart = this.userCart;
        },
        computed:{
            cardImages:function(){
                return this.product.media;//[...this.product.offers[this.selectedOfferIndex]?this.product.offers[this.selectedOfferIndex].media:[], ...this.product.media];
            },
            selectedOffer: function(){
                return this.product.offers[this.selectedOfferIndex];
            },
            inCart: function(){
                let find = this.usercart.find(a=>a.position == this.product.id && a.offer == this.product.offers[this.selectedOfferIndex].id);
                return find?find:{};
            }
        },
        methods:{
            moveImg(next){
                if(next){
                    if (this.currentImage == this.cardImages.length-1) this.currentImage = 0;
                    else this.currentImage++;
                }
                else {
                    if (this.currentImage==0) this.currentImage = this.cardImages.length-1;
                    else this.currentImage--;
                }
            },
            selectOffer(index){
                this.currentImage=0;
                this.selectedOfferIndex=index;
            },
            addToCart()
            {
                let find = this.usercart.findIndex(a=>a.position == this.product.id && a.offer == this.product.offers[this.selectedOfferIndex].id);

                if (find>-1) this.usercart[find].quantity++;
                else this.usercart.push({position:this.product.id, offer:this.product.offers[this.selectedOfferIndex].id, quantity:1});

                this.saveCart();
            },
            removeFromCart()
            {
                let find = this.usercart.findIndex(a=>a.position == this.product.id && a.offer == this.product.offers[this.selectedOfferIndex].id);

                if (find>-1) {
                    if (this.usercart[find].quantity>1) this.usercart[find].quantity--;
                    else this.usercart.splice(find, 1);
                }

                this.saveCart();
            },
            saveCart()
            {
                const parsed = JSON.stringify(this.usercart);
                localStorage.setItem('user_cart', parsed);
                axios.post(route('market.saveCart'), {cart:this.usercart});
            },
        }
    }
</script>
<template>
    <Head title="Marketzone" />

    <FullLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-2">

                    <!--Breadcrumb-->
                    <Breadcrumb v-if="breadcrumb" :breadcrumb="breadcrumb"/>

                    <!--Product main info-->
                    <div class="md:flex w-full">
                        
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
                                        :class="{'border-opacity-90':index==selectedOfferIndex}"
                                        @click="selectOffer(index)"
                                    >
                                        {{ offer.title }}
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div>{{ selectedOffer.price }}₽</div>
                                <div class="m-4 bg-white">
                                    <PrimaryButton v-show="!inCart.quantity" title="Добавить в корзину" @click="addToCart()" class="w-full">
                                        <i class="ri-shopping-basket-2-line mr-2"></i> Добавить
                                    </PrimaryButton>
                                    <div v-show="inCart.quantity" class="flex justify-between items-center relative whitespace-nowrap font-semibold tracking-widest 
                                                                         transition ease-in-out duration-150 min-w-32">
                                        <div class="rounded-md cursor-pointer w-9 h-9 flex justify-center items-center bg-indigo-200 text-indigo-700 hover:bg-indigo-100" @click="removeFromCart()" title="-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"><path fill="currentColor" d="M5 11a1 1 0 1 0 0 2h14a1 1 0 1 0 0-2z"></path></svg>
                                        </div>
                                        <span>{{ inCart.quantity }}</span>
                                        <div class="rounded-md cursor-pointer w-9 h-9 flex justify-center items-center bg-indigo-200 text-indigo-700 hover:bg-indigo-100" @click="addToCart()" title="+1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"><path fill="currentColor" d="M12 4a1 1 0 0 0-1 1v6H5a1 1 0 1 0 0 2h6v6a1 1 0 1 0 2 0v-6h6a1 1 0 1 0 0-2h-6V5a1 1 0 0 0-1-1"></path></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--Product description-->
                    <div v-html="product.description"></div>
                </div>
            </div>
        </div>
    </FullLayout>
</template>