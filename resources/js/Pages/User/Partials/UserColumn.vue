<script setup>
    import SecondaryButton from '@/Components/UI/SecondaryButton.vue';
    import { Link } from '@inertiajs/vue3';
    defineProps({
        userInfo:{type:Object, default:{}},
        edit:{type:Boolean, default:false}
    });
</script>
<template>
    <div>
        <Avatar :avatar="userInfo.avatar"/>
        <h1 class="mt-4">
            <span class="block font-semibold text-2xl" itemprop="name">{{ userInfo.name }} {{ userInfo.surname }}</span>
            <span class="block text-gray-500" itemprop="additionalName">{{ userInfo.nickname }}</span>
        </h1>
        <Link v-if="edit" :href="route('profile.edit')">
            <SecondaryButton class="w-full mt-4">Редактировать</SecondaryButton>
        </Link>
        <div class="mt-4">
            <div v-if="userInfo.phone">
                <i class="ri-phone-line mr-2"></i>
                <a :href="'tel:+'+userInfo.phone">+{{ userInfo.phone }}</a>
            </div>
            <div v-if="userInfo.email">
                <i class="ri-mail-line mr-2"></i>{{ userInfo.email }}
            </div>
        </div>
    </div>
</template>
