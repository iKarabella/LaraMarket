<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    catalog: {
        type:Array,
        default:[]
    }
});

console.log(props.catalog);
</script>
<template>
    <div class="w-full bg-black text-gray-200 bg-[url(/resources/images/keller.jpg)] bg-no-repeat text-shadow-xs text-shadow-gray-800">
        <div class="ml-auto mr-auto md:max-w-5xl md:grid md:grid-cols-4 md:gap-2">
            <div>
                <div class="text-lg py-2">Контакты</div>
                <div class="grid grid-cols-1 gap-2">
                    <div>
                        <a href="tel:+7 (812) 123-12-12">
                            <i class="ri-phone-fill"></i>
                            +7 (812) 123-12-12
                        </a>
                    </div>
                    <div>
                        <i class="ri-map-pin-2-line"></i>
                        ул. Евдокии Дашиной д.1
                    </div>
                </div>
            </div>
            <div>
                <div class="text-lg py-2">Информация</div>
                <div class="grid grid-cols-1 gap-2">
                    <Link :href="route('home')">Доставка</Link>
                    <Link :href="route('home')">Контакты</Link>
                </div>
            </div>
            <div>
                <div class="text-lg py-2">
                    <Link :href="route('catalog')">Каталог</Link>
                </div>
                <div v-if="catalog" class="grid grid-cols-1 gap-2">
                    <Link v-for="(element,index) in catalog" :key="index" :href="element.link">{{ element.title }}</Link>
                </div>
            </div>
            <div>
                <div class="text-lg py-2">Документы</div>
                <div class="grid grid-cols-1 gap-2">
                    <Link :href="route('home')">Согласие на обработу персональных данных</Link>
                    <Link :href="route('home')">Политика конфиденциальности</Link>
                    <Link :href="route('home')">Публичная оферта</Link>
                </div>
            </div>
        </div>
    </div>
</template>