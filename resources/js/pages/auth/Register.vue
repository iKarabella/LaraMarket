<script setup>
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { PhoneFormat } from '@/Mixins/PhoneFormat.js';

const form = useForm({
    phone: '',
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
    <AuthBase title="Регистрация" description="Заполните форму ниже для создания аккаунта">
        <Head title="Регистрация" />

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="phone">Телефон</Label>
                    <Input id="phone"
                           type="phone" 
                           required 
                           :tabindex="2" 
                           autocomplete="phone" 
                           v-model="form.phone" 
                           placeholder="+7 000 000 0000" 
                           v-on:keyup="form.phone=PhoneFormat(form.phone)"
                    />
                    <InputError :message="form.errors.phone" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Пароль</Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="3"
                        autocomplete="new-password"
                        v-model="form.password"
                        placeholder="Пароль"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Повторите пароль</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        v-model="form.password_confirmation"
                        placeholder="Confirm password"
                    />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <Button type="submit" class="mt-2 w-full" tabindex="5" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Создать аккаунт
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Уже есть аккаунт?
                <TextLink :href="route('login')" class="underline underline-offset-4" :tabindex="6">Войти</TextLink>
            </div>
        </form>
    </AuthBase>
</template>
