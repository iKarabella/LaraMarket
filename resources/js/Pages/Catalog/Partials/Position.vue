<script setup>
import PrimaryButton from '@/Components/UI/PrimaryButton.vue';
import { Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    position: {type:Object, default:{}}, 
    usercart:{type:Array, default:[]}
});

const emit = defineEmits(['addToCart', 'removeFromCart', 'notifyAboutAdmission']);

const currentImage = ref(0);
const currentOffer = ref(0);

const inCart = computed(() => {
    if(!props.usercart) return {};
    let find = props.usercart.find(a=>a.position == props.position.id && a.offer == props.position.offers[currentOffer.value].id);
    return find?find:{};
});

const noMore = computed(() => {
    return inCart.value.quantity>=props.position.offers[currentOffer.value].available;
});

const moveImg = (next) => {
    if(next){
        if (currentImage.value == props.position.media.length-1) currentImage.value = 0;
        else currentImage.value++;
    }
    else {
        if (currentImage.value==0) currentImage.value = props.position.media.length-1;
        else currentImage.value--;
    }
};

const changeCart = (remove=false) => {
    if(!remove && noMore.value) return false;
    emit(remove?'removeFromCart':'addToCart', {position:props.position.id, offer:props.position.offers[currentOffer.value].id});
}

const notifyAboutAdmission = () => {
    emit('notifyAboutAdmission', {position:props.position.id, offer:props.position.offers[currentOffer.value].id});
};
</script>

<template>
    <div class="relative flex flex-col justify-between text-gray-700 bg-gray-50 shadow-md bg-clip-border rounded-xl w-96 mx-auto my-2">
        <div class="relative mx-4 mt-4 overflow-hidden text-gray-700 bg-clip-border rounded-xl h-96">
            <div class="relative">
                <!-- Carousel wrapper -->
                <div class="overflow-hidden relative rounded-lg">
                    <!-- Item 1 -->
                    <div v-for="(media, index) in position.media" :key="media.id" class="duration-700 ease-in-out" :class="{'hidden':currentImage!=index}">
                        <img :src="media.preview" class="block w-full" alt="...">
                    </div>
                </div>
                <!-- Slider indicators -->
                <div v-if="position.media.length>1" class="flex absolute bottom-5 left-1/2 z-30 space-x-3 -translate-x-1/2">
                    <button v-for="(media, index) in position.media" :key="index" 
                            type="button" 
                            class="w-3 h-3 rounded-full bg-gray-500"
                            :class="{'opacity-20':currentImage==index, 'opacity-60':currentImage!=index}"
                            @click="currentImage=index"></button>
                </div>
                <!-- Slider controls -->
                <button v-if="position.media.length>1" type="button" class="flex absolute top-0 left-0 z-30 justify-center items-center h-full cursor-pointer group focus:outline-none" @click="moveImg(false)">
                    <span class="inline-flex justify-center items-center w-8 h-8 rounded-full sm:w-10 sm:h-10 bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                        <svg class="w-5 h-5 text-white sm:w-6 sm:h-6 dark:text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        <span class="hidden"><</span>
                    </span>
                </button>
                <button v-if="position.media.length>1" type="button" class="flex absolute top-0 right-0 z-30 justify-center items-center h-full cursor-pointer group focus:outline-none" @click="moveImg()">
                    <span class="inline-flex justify-center items-center w-8 h-8 rounded-full sm:w-10 sm:h-10 bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
                        <svg class="w-5 h-5 text-white sm:w-6 sm:h-6 dark:text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        <span class="hidden">></span>
                    </span>
                </button>
            </div>
        </div>
        <div class="mx-4 mt-4">
            <Link :href="route('catalog.product', [position.code])">
                <p class="block font-sans text-base antialiased font-medium leading-relaxed text-blue-gray-900">
                    {{ position.title }}
                </p>
            </Link>
            <p class="block font-sans text-sm antialiased font-normal leading-normal text-gray-700 opacity-75">
                {{ position.short_description }}
            </p>
        </div>
        <div class="mx-4 mt-4">
            <div v-for="(offer, index) in position.offers" 
                :key="offer.id" 
                :class="{'border-gray-200':currentOffer==index, 'border-gray-200/10':currentOffer!=index}"
                 class="flex items-center justify-between p-1 mb-2 hover:bg-gray-200 rounded border cursor-pointer"
                 title="Выбрать"
                @click="currentOffer=index" 
            >
                <div>{{ offer.title }}</div>
                <div class="whitespace-nowrap">{{ offer.price}} ₽</div>
            </div>
        </div>
        <div v-show="position.offers[currentOffer].available" class="m-4 bg-white">
            <PrimaryButton v-show="!inCart.quantity" title="Добавить в корзину" @click="changeCart()" class="w-full">
                <i class="ri-shopping-basket-2-line mr-2"></i> Добавить
            </PrimaryButton>
            <div v-show="inCart.quantity" class="flex justify-between items-center relative whitespace-nowrap font-semibold tracking-widest transition ease-in-out duration-150">
                <div class="rounded-md cursor-pointer w-9 h-9 flex justify-center items-center bg-indigo-200 text-indigo-700 hover:bg-indigo-100" 
                    @click="changeCart(true)" title="-1"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"><path fill="currentColor" d="M5 11a1 1 0 1 0 0 2h14a1 1 0 1 0 0-2z"></path></svg>
                </div>
                <span>{{ inCart.quantity }}</span>
                <div class="rounded-md cursor-pointer w-9 h-9 flex justify-center items-center"
                     title="+1"
                    :class="{'bg-gray-100 text-gray-500':noMore, 'bg-indigo-200 hover:bg-indigo-100 text-indigo-700':!noMore}"
                    @click="changeCart()"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6"><path fill="currentColor" d="M12 4a1 1 0 0 0-1 1v6H5a1 1 0 1 0 0 2h6v6a1 1 0 1 0 2 0v-6h6a1 1 0 1 0 0-2h-6V5a1 1 0 0 0-1-1"></path></svg>
                </div>
            </div>
        </div>
        <div v-show="!position.offers[currentOffer].available" class="m-4 bg-white">
            <PrimaryButton title="Сообщить, как только товар появится в продаже" class="w-full" @click="notifyAboutAdmission">
                <i class="ri-notification-2-line mr-2"></i> Сообщить
            </PrimaryButton>
        </div>
        <!--Link v-if="position.canEdit" class="absolute top-0 right-0 mr-1 opacity-25 hover:opacity-90 cursor-pointer" 
              title="Редактировать"
              :href="route('market.manage.product.link', [position.code])" 
        >
            <i class="ri-edit-2-fill"></i>
        </Link-->
    </div>
</template>