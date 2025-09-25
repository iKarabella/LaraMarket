<script setup>
import InputError from '@/Components/UI/InputError.vue';
import InputLabel from '@/Components/UI/InputLabel.vue';
import PrimaryButton from '@/Components/UI/PrimaryButton.vue';
import TextInput from '@/Components/UI/TextInput.vue';
import {PhoneFormat} from '@/Mixins/PhoneFormat.js';
import {TextTranslit} from '@/Mixins/TextFormat.js';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    user: {
        type: Object,
    },
});

const form = useForm({
    phone: props.user.phone?'+'+props.user.phone:props.user.phone,
    name: props.user.name,
    patronymic: props.user.patronymic,
    surname: props.user.surname,
    nickname: props.user.nickname,
    email: props.user.email,
});
</script>
<template>
    <div>        
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Общая информация</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Здесь вы можете обновить информацию о профиле
            </p>
        </header>
        <div class="md:flex w-full mt-6 space-y-6">
            <div class="md:hidden"><Avatar :src="user.avatar"/></div>
            <div class="md:w-9/12">
                <form @submit.prevent="form.patch(route('profile.update'))">
                    <div>
                        <InputLabel for="name" value="Имя" />
        
                        <TextInput
                            id="name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                        />
        
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>
                    <div>
                        <InputLabel for="patronymic" value="Отчество" />
        
                        <TextInput
                            id="patronymic"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.patronymic"
                            autocomplete="patronymic"
                        />
        
                        <InputError class="mt-2" :message="form.errors.patronymic" />
                    </div>
                    <div>
                        <InputLabel for="surname" value="Фамилия" />
        
                        <TextInput
                            id="surname"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.surname"
                            autocomplete="surname"
                        />
        
                        <InputError class="mt-2" :message="form.errors.surname" />
                    </div>
                    <div>
                        <InputLabel for="nickname" value="Ник" />
        
                        <TextInput
                            id="nickname"
                            type="text"
                            class="mt-1 block w-full"
                            v-on:keyup="form.nickname=TextTranslit(form.nickname, true)"
                            v-model="form.nickname"
                            autocomplete="nickname"
                        />
        
                        <InputError class="mt-2" :message="form.errors.nickname" />
                    </div>
        
                    <div>
                        <InputLabel for="phone" value="Телефон" />
        
                        <TextInput
                            id="phone"
                            v-on:keyup="form.phone=PhoneFormat(form.phone)"
                            type="tel"
                            class="mt-1 block w-full"
                            v-model="form.phone"
                            required
                            autocomplete="phone"
                        />
        
                        <InputError class="mt-2" :message="form.errors.phone" />
                    </div>
        
                    <div>
                        <InputLabel for="email">
                            <span>Email</span>
                            <Link :href="route('verification.notice')" 
                                  title="Подтвердить" 
                                  v-if="!user.email_verified_at && user.email" 
                                  class="text-red-700 ml-2" 
                                  style="font-size: 90%;">
                                Не подтвержден
                            </Link>
                        </InputLabel>
        
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            v-model="form.email"
                            autocomplete="email"
                        />
        
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="flex items-center gap-4 mt-4">
                        <PrimaryButton :disabled="form.processing">Сохранить</PrimaryButton>
        
                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-if="form.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">Сохранено</p>
                        </Transition>
                    </div>
                </form>
            </div>
            <div class="hidden md:block"><Avatar :src="user.avatar"/></div>
        </div>
    </div>
</template>
