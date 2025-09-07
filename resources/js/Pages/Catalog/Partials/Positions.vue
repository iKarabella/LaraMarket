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
import { usercart, addToCart, removeFromCart } from '@/Mixins/UserCart.js';

const props = defineProps({
    positions:{type:Array, default:[]},
    auth:{type:Object, default:null}
});

const showNotifyAboutAdmission = ref(false);

const notifyAboutAdmissionForm = useForm({
    product_id:null,
    offer_id:null,
    name:'',
    email:'',
});

const closeModal = () => {
    notifyAboutAdmissionForm.reset();
    showNotifyAboutAdmission.value=false;
};

const notifyAboutAdmission = (i) => {
    if (!i.offer || !i.position) return false;

    notifyAboutAdmissionForm.offer_id = i.offer;
    notifyAboutAdmissionForm.product_id = i.position;

    if (props.auth && props.auth.user.id) notifyFormSend();
    else showNotifyAboutAdmission.value = true;
};
            
const notifyFormSend = () => {
    notifyAboutAdmissionForm.post(route('catalog.notifyAboutAdmission'), {
        preserveScroll:true,
        onSuccess:(e)=>{
            notifyAboutAdmissionForm.reset();
            //TODO показать уведомление, что мы вам сообщим о поступлении
            closeModal();
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
                Notify About Admission
            </div>
                
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
        </div>
    </Modal>
</template>