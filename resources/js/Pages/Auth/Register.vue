<script setup>
import SimpleLayout from '@/Layouts/SimpleLayout.vue';
import InputError from '@/Components/UI/InputError.vue';
import InputLabel from '@/Components/UI/InputLabel.vue';
//import Checkbox from '@/Components/UI/Checkbox.vue';
import PrimaryButton from '@/Components/UI/PrimaryButton.vue';
import TextInput from '@/Components/UI/TextInput.vue';
import {PhoneFormat} from '@/Mixins/PhoneFormat.js';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    phone: '',
    offer_accepted: true,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <SimpleLayout>
        <Head title="Register" />

        <form @submit.prevent="submit">
            <div class="mt-4">
                <InputLabel for="phone" value="Телефон" />

                <TextInput
                    id="phone"
                    type="tel"
                    class="mt-1 block w-full"
                    v-model="form.phone"
                    v-on:keyup="form.phone=PhoneFormat(form.phone)"
                    required
                    autocomplete="phone"
                />

                <InputError class="mt-2" :message="form.errors.phone" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Пароль" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Подтвердите пароль" />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <!--div class="mt-4">
                <div class="flex items-center justify-end">
                    <Checkbox
                        id="offer_accept"
                        class="m-2"
                        v-model="form.offer_accepted"
                    />
                    <div class="m-2 text-xs text-gray-500 text-justify">
                        Я согласен с условиями 
                        <a :href="route('public_offer')" class="underline hover:text-gray-800" target="_blank">публичной аферты</a> и 
                        <a :href="route('personal_data_agreement')" class="underline hover:text-gray-800" target="_blank">соглашением</a> 
                        об использовании персональных данных.
                    </div>
                </div>
                <InputError class="text-right" :message="form.errors.offer_accepted" />
            </div-->

            <div class="flex items-center justify-end mt-4">
                <Link
                    :href="route('login')"
                    class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                >
                    Уже зарегистрированы?
                </Link>

                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Регистрация
                </PrimaryButton>
            </div>
        </form>
    </SimpleLayout>
</template>
