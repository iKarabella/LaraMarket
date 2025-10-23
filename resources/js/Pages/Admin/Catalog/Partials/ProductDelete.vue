<script setup>
import DangerButton from '@/Components/UI/DangerButton.vue';
import InputError from '@/Components/UI/InputError.vue';
import InputLabel from '@/Components/UI/InputLabel.vue';
import Modal from '@/Components/Modals/MainModal.vue';
import SecondaryButton from '@/Components/UI/SecondaryButton.vue';
import TextInput from '@/Components/UI/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const props = defineProps({
    product_id: {type: Number, default:null},
});

const form = useForm({
    password: '',
    id: props.product_id
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteProduct = () => {
    form.delete(route('admin.products.store'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <DangerButton @click="confirmUserDeletion" title="Удалить товар из каталога">Удалить</DangerButton>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Вы действительно хотите удалить товар из каталога?
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    После удаления товара все его данные будут удалены без возможности восстановления. <br/>
                    Пожалуйста, введите свой пароль, чтобы подтвердить, что вы хотите товар.
                </p>

                <div class="mt-6">
                    <InputLabel for="password" value="Пароль" class="sr-only" />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-3/4"
                        placeholder="Password"
                        @keyup.enter="deleteProduct"
                    />

                    <InputError :message="form.errors.password" class="mt-2" />
                    <InputError :message="form.errors.id" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Отмена </SecondaryButton>

                    <DangerButton
                        class="ms-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteProduct"
                    >
                        Удалить
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
