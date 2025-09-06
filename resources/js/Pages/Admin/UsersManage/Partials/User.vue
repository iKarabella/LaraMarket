<script setup>
import SecondaryButton from '@/Components/UI/SecondaryButton.vue';
import TextInput from '@/Components/UI/TextInput.vue';
import Checkbox from '@/Components/UI/Checkbox.vue';
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
        <td>{{ user.nickname }}</td>
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
                    name="phone"
                    autocomplete="false"
                    placeholder="Телефон"/>
            </td>
            <td class="text-center">
                <TextInput v-model="params.name" 
                    v-on:change="updFilts"
                    name="name"
                    autocomplete="false"
                    placeholder="Имя"/>
            </td>
            <td class="text-center">
                <TextInput v-model="params.surname" 
                    v-on:change="updFilts"
                    name="surname"
                    placeholder="Фамилия"/>
            </td>
            <td class="text-center">
                <TextInput v-model="params.nickname" 
                    :keyup="params.nickname=TextTranslit(params.nickname, true)"
                    v-on:change="updFilts"
                    name="nickname"
                    placeholder="Псевдоним"/>
            </td>
            <td></td>
            <td class="text-right">
                <label class="flex font-medium text-sm text-gray-700 dark:text-gray-300">
                    <Checkbox class="mr-4"
                              name="courier"
                              v-model="params.couriers"
                              :checked="params.couriers"
                              v-on:change="updFilts"/> 
                    Курьер
                </label>
            </td>
        </tr>
    </template>
</template>
