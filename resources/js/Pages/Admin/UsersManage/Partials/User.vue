<script setup>
import SecondaryButton from '@/Components/UI/SecondaryButton.vue';
import TextInput from '@/Components/UI/TextInput.vue';
import {PhoneFormat} from '@/Mixins/PhoneFormat.js';
import {TextTranslit} from '@/Mixins/TextFormat.js';
import { ref } from 'vue';

defineProps({user: Object, params: Object});

const emit = defineEmits(['userEdit', 'updateParams']);
const showFilter = ref(false);
const userEdit = (e) => {emit('userEdit', e);};
const updFilts = () => {emit('updateParams');};
</script>
<template>
    <tr v-if="user" class="hover:bg-gray-200">
        <td class="py-2">+{{ user.phone }}</td>
        <td>{{ user.name }} {{ user.patronymic }}</td>
        <td>{{ user.surname }}</td>
        <td>{{ user.login }}</td>
        <td class="text-right">
            <SecondaryButton @click="userEdit(user.id)">{{user.isAdmin?'Смотреть':'Редактировать'}}</SecondaryButton>
        </td>
    </tr>
    <template v-else>
        <tr>
            <th class="w-2/12">Телефон</th>
            <th class="w-4/12">Имя</th>
            <th class="w-3/12">Фамилия</th>
            <th class="w-2/12">Псевдоним</th>
            <th class="w-1/12 text-right">
                <SecondaryButton @click="showFilter=!showFilter">Фильтры</SecondaryButton>
            </th>
        </tr>
        <tr v-show="showFilter">
            <td class="text-center">
                <TextInput v-model="params.phone" 
                    :keyup="params.phone=PhoneFormat(params.phone)"
                    v-on:change="updFilts"
                    placeholder="Телефона"/>
            </td>
            <td class="text-center">
                <TextInput v-model="params.name" 
                    v-on:change="updFilts"
                    placeholder="Имя"/>
            </td>
            <td class="text-center">
                <TextInput v-model="params.surname" 
                    v-on:change="updFilts"
                    placeholder="Фамилия"/>
            </td>
            <td class="text-center">
                <TextInput v-model="params.login" 
                    :keyup="params.login=TextTranslit(params.login, true)"
                    v-on:change="updFilts"
                    placeholder="Псевдоним"/>
            </td>
            <td></td>
            <td class="text-right"></td>
        </tr>
    </template>
</template>
