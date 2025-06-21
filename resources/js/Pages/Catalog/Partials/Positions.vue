<script setup>
import axios from 'axios';
import Position from './Position.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Modal from '@/Components/Modals/MainModal.vue';
import InputLabel from '@/Components/UI/InputLabel.vue';
import TextInput from '@/Components/UI/TextInput.vue';
import InputError from '@/Components/UI/InputError.vue';
import SecondaryButton from '@/Components/UI/SecondaryButton.vue';
import PrimaryButton from '@/Components/UI/PrimaryButton.vue';
</script>
<script>
    export default {
        props:{
            positions:{type:Array, default:[]},
            userCart:{type:Array, default:[]}
        },
        emits:{changeCart:null},
        data(){
            return {
                products:{},
                filters:{
                    category:null,
                },
                usercart:[],
                showNotifyAboutAdmission: ref(false),
                notifyAboutAdmissionForm: useForm({
                    product_id:null,
                    offer_id:null,
                    name:'',
                    email:'',
                })
            }
        },
        mounted(){
            if (localStorage.getItem('user_cart')) {
                try {
                    this.usercart = JSON.parse(localStorage.getItem('user_cart'));
                } catch(e) {
                    localStorage.removeItem('user_cart');
                }
            }

            if (!this.usercart.length && Array.isArray(this.userCart) && this.userCart.length) this.usercart = this.userCart;
        },
        methods: 
        {
            addToCart(i)
            {
                let find = this.usercart.findIndex(a=>a.position == i.position && a.offer == i.offer);

                if (find>-1) this.usercart[find].quantity++;
                else this.usercart.push({position:i.position, offer:i.offer, quantity:1});

                this.saveCart();
            },
            removeFromCart(i)
            {
                let find = this.usercart.findIndex(a=>a.position == i.position && a.offer == i.offer);

                if (find>-1) {
                    if (this.usercart[find].quantity>1) this.usercart[find].quantity--;
                    else this.usercart.splice(find, 1);
                }

                this.saveCart();
            },
            saveCart()
            {
                const parsed = JSON.stringify(this.usercart);
                localStorage.setItem('user_cart', parsed);
                //axios.post(route('catalog.saveCart'), {cart:this.usercart});
                this.$emit('changeCart');
            },
            closeModal()
            {
                this.notifyAboutAdmissionForm.reset();
                this.showNotifyAboutAdmission=false;
            },
            notifyAboutAdmission(i)
            {
                if (!i.offer || !i.position) return false;

                this.notifyAboutAdmissionForm.offer_id = i.offer;
                this.notifyAboutAdmissionForm.product_id = i.position;

                if (this.$page.props.auth && this.$page.props.auth.user.id) this.notifyFormSend();
                else this.showNotifyAboutAdmission = true;
            },
            notifyFormSend(){
                this.notifyAboutAdmissionForm.post(route('catalog.notifyAboutAdmission'), {
                    preserveScroll:true,
                    onSuccess:(e)=>{
                        this.notifyAboutAdmissionForm.reset();
                        this.closeModal();
                    }, 
                    onError:(e)=>console.log(e)
                });
            },
        }
    }
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