<script setup>
import SimpleLayout from '@/Layouts/SimpleLayout.vue';
import Checkbox from '@/Components/UI/Checkbox.vue';
import InputError from '@/Components/UI/InputError.vue';
import InputLabel from '@/Components/UI/InputLabel.vue';
import PrimaryButton from '@/Components/UI/PrimaryButton.vue';
import TextInput from '@/Components/UI/TextInput.vue';
import {PhoneFormat} from '@/Mixins/PhoneFormat.js';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    phone: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <SimpleLayout>
        <Head title="Авторизация" />
        <form @submit.prevent="submit">
            <div class="w-full flex justify-center text-[#00FF00] text-xl mb:2 md:mb-5">
                Вход
            </div>
            <div>
                <InputLabel for="phone" value="Телефон" class="text-gray-200"/>
                <TextInput
                    id="phone"
                    type="tel"
                    class="mt-1 block w-full text-black"
                    v-model="form.phone"
                    required
                    autofocus
                    v-on:keyup="form.phone=PhoneFormat(form.phone)"
                    autocomplete="tel"
                />
                <InputError class="mt-2" :message="form.errors.phone" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Пароль"/>
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full text-black"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Запомнить меня</span>
                </label>
            </div>

            <div class="flex items-center justify-between mt-4">
                <Link
                    :href="route('register')"
                    class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                >
                    Регистрация
                </Link>
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request.phone')"
                    class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                >
                    Забыли пароль?
                </Link>

                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Войти
                </PrimaryButton>
            </div>
        </form>
    </SimpleLayout>
</template>
