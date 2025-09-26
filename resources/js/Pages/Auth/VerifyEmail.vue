<script setup>
import { computed } from 'vue';
import SimpleLayout from '@/Layouts/SimpleLayout.vue';
import PrimaryButton from '@/Components/UI/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <SimpleLayout>
        <Head title="Подтверждение электронной почты" />

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            Чтобы подтвердить свой e-mail, пожалуйста, перейдите по ссылке в электронном письме, которое мы только что вам отправили.
            Если вы не получили письмо - мы с отправим вам новое.
        </div>

        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400" v-if="verificationLinkSent">
            Новая ссылка для подтверждения была отправлена на адрес электронной почты, который вы указали в профиле.
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Отправить письмо еще раз
                </PrimaryButton>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                    >Выйти из аккаунта</Link
                >
            </div>
        </form>
    </SimpleLayout>
</template>
