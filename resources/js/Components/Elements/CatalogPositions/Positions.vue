<script setup>
import Position from './Position.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Modal from '@/Components/Modals/MainModal.vue';
import InputLabel from '@/Components/UI/InputLabel.vue';
import TextInput from '@/Components/UI/TextInput.vue';
import InputError from '@/Components/UI/InputError.vue';
import SecondaryButton from '@/Components/UI/SecondaryButton.vue';
import PrimaryButton from '@/Components/UI/PrimaryButton.vue';
import DangerButton from '@/Components/UI/DangerButton.vue';
import { usercart, addToCart, removeFromCart } from '@/Mixins/UserCart.js';

const props = defineProps({
    positions:{type:Array, default:[]},
    auth:{type:Object, default:null}
});

const showNotifyAboutAdmission = ref(false);
const showNotifyAboutAdmissionResult = ref(false);

const notifyAboutAdmissionForm = useForm({
    product_id:null,
    offer_id:null,
    name:null,
    email:null,
});

const closeModal = () => {
    notifyAboutAdmissionForm.reset();
    showNotifyAboutAdmission.value=false;
    showNotifyAboutAdmissionResult.value=false;
};

const notifyAboutAdmission = (i) => {
    if (!i.offer || !i.position) return false;

    notifyAboutAdmissionForm.offer_id = i.offer;
    notifyAboutAdmissionForm.product_id = i.position;

    if ((props.auth==null || !props.auth.user)) showNotifyAboutAdmission.value = true;
    else notifyFormSend();
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
    <div class="p-2 lg:grid lg:grid-cols-2 xl:grid-cols-3 lg:gap-2">
        <Position v-for="position in positions" 
                 :key="position.id" 
                 :position="position" 
                 :usercart="usercart" 
                 @addToCart="addToCart" 
                 @removeFromCart="removeFromCart"
                 @notifyAboutAdmission="notifyAboutAdmission"
        />
    </div>
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