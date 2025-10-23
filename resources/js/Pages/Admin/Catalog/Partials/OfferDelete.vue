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
    offer_id: {type: Number, default:null},
    product_code: {type:String, default:null},
});

const form = useForm({
    password: '',
    id: props.offer_id
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteOffer = () => {
    form.delete(route('admin.products.offers.delete', [props.product_code, props.offer_id]), {
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
        <DangerButton @click="confirmUserDeletion">Удалить</DangerButton>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Вы действительно хотите удалить торговое предложение?
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    После удаления торгового предложения все его данные будут удалены без возможности восстановления. <br/>
                    Пожалуйста, введите свой пароль, чтобы подтвердить, что вы хотите торговое предложение.
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
                        @keyup.enter="deleteOffer"
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
                        @click="deleteOffer"
                    >
                        Удалить
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
