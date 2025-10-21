<script setup>
    import Modal from '@/Components/Modals/MainModal.vue';
    import InputLabel from '@/Components/UI/InputLabel.vue';
    import TextInput from '@/Components/UI/TextInput.vue';
    import InputError from '@/Components/UI/InputError.vue';
    import SecondaryButton from '@/Components/UI/SecondaryButton.vue';
    import PrimaryButton from '@/Components/UI/PrimaryButton.vue';
    import DangerButton from '@/Components/UI/DangerButton.vue';
    import { watch, ref } from 'vue';
    import { useForm } from '@inertiajs/vue3';

    const props = defineProps({
        show:{type:Object, default:null},
        auth:{type:Object, default:null}
    });

    const emit = defineEmits(['closeNotify']);
    
    const showNotifyAboutAdmissionResult = ref(false);
    const showNotifyAboutAdmission = ref(false);

    const notifyAboutAdmissionForm = useForm({
        product_id:null,
        offer_id:null,
        name:null,
        email:null,
    });

    watch(
        () => props.show,
        (newValue, oldValue) => {
            if (!newValue || !newValue.offer_id || !newValue.product_id) return;

            notifyAboutAdmissionForm.offer_id = newValue.offer_id;
            notifyAboutAdmissionForm.product_id = newValue.product_id;

            if ((props.auth==null || !props.auth.user)) showNotifyAboutAdmission.value = true;
            else notifyFormSend();
        }
    );

    const closeModal = () => {
        notifyAboutAdmissionForm.reset();
        showNotifyAboutAdmission.value = false;
        emit('closeNotify');
    };
                
    const notifyFormSend = () => {
        notifyAboutAdmissionForm.post(route('catalog.notifyAboutAdmission'), {
            preserveScroll:true,
            onSuccess:(e)=>{
                showNotifyAboutAdmissionResult.value=true;
            }, 
            onError:(e)=>console.log(e)
        });
    };
</script>
<template>
    <Modal :show="showNotifyAboutAdmission" @close="closeModal">
        <div class="p-6">
            <div>
                Уведомление о наличии
            </div>
            <template v-if="showNotifyAboutAdmissionResult">
                <div class="mt-2">
                    Как только этот товар будет в наличии — мы сразу вам сообщим!
                </div>
                <div class="mt-2 text-center">
                    <DangerButton class="mr-2" @click="closeModal">Отлично!</DangerButton>
                </div>
            </template>
            <template v-else>
                <div class="mt-2">
                    <InputLabel for="name" value="Имя" />
                    <TextInput
                        type="text"
                        id="name"
                        name="name"
                        class="w-full mt-1 block"
                        required
                        v-model="notifyAboutAdmissionForm.name"
                        autocomplete="off"
                    />
                    <InputError class="ml-2" :message="notifyAboutAdmissionForm.errors.name" />
                </div>
                <div class="mt-2">
                    <InputLabel for="email" value="E-mail" />
                    <TextInput
                        type="email"
                        id="email"
                        name="email"
                        class="w-full mt-1 block"
                        required
                        v-model="notifyAboutAdmissionForm.email"
                        autocomplete="off"
                    />
                    <InputError class="ml-2" :message="notifyAboutAdmissionForm.errors.email" />
                </div>
                    
                <div class="mt-2 text-right">
                    <SecondaryButton class="mr-2" @click="closeModal">Отменить</SecondaryButton>
                    <PrimaryButton class="mr-2" @click="notifyFormSend">Отправить</PrimaryButton>
                </div>
            </template>            
        </div>
    </Modal>
</template>