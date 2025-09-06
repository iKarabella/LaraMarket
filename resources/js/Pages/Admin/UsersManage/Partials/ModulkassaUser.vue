<script setup>
import SecondaryButton from '@/Components/UI/SecondaryButton.vue';
import TextInput from '@/Components/UI/TextInput.vue';
import Checkbox from '@/Components/UI/Checkbox.vue';
import {PhoneFormat} from '@/Mixins/PhoneFormat.js';
import {TextTranslit} from '@/Mixins/TextFormat.js';
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({user: {type:Number, defult:null}});

const cashRegisters = ref([]);
const openBlock = ref(false);
const addCashRegisterBlock = ref(false);
const availableCashRegisters = ref([]);

const getModulSettings = ()=>{
    if(openBlock.value){
        openBlock.value = false;
        cashRegisters.value=[];
    }
    else axios.get(route('admin.user.getCurrentCashRegisters', [props.user])).then(res=>{
        if (res.data) {
            cashRegisters.value=res.data;
            openBlock.value = true;
        }
    });
};

const addCashRegister = () => {
    axios.get(route('admin.user.getAvailableCashRegisters', [props.user])).then(res=>{
        if (res.data) {
            availableCashRegisters.value = res.data.map(arr=>{
                return {
                    id:arr.id,
                    name:arr.name,
                    address:arr.address,
                    phone:arr.phone,
                    users:[]
                }
            });
            addCashRegisterBlock.value = true;
        }
    });
}

const getModulkassaUsers = (point)=>{
    axios.get(route('admin.user.getModulkassaUsers', [props.user, point])).then(res=>{
        if (res.data) {
            let find = availableCashRegisters.value.find(arr=>arr.id==point);
            if (find) find.users = res.data;
        }
    });
};

const modulCashierForm = useForm({
    cashier:null,
    cashierRegister:null,
    point:null,
    user_id:props.user
});

const selectModulCashier = (cashier, point)=>{
    modulCashierForm.cashier = cashier;
    modulCashierForm.cashierRegister = point.id;
    modulCashierForm.point = point;
    modulCashierForm.post(route('admin.user.ModulkassaUser', [props.user]), {
        preserveScroll: true,
        onSuccess: () => {
            addCashRegisterBlock.value=false;
            availableCashRegisters.value=[];
            cashRegisters.value.push({
                details:point,
                cr_id:point.id
            });
            modulCashierForm.reset();
        },
        onError: () => {
            console.log('error response');
        }
    });
};

const removeCashRegister = (guid)=>{
    modulCashierForm.cashierRegister = guid;
    modulCashierForm.delete(route('admin.user.ModulkassaUser', props.user), {
        preserveScroll:true,
        onSuccess:() => {
            modulCashierForm.reset();
            cashRegisters.value = cashRegisters.value.filter(arr=>arr.cr_id!=guid);
            if (addCashRegisterBlock.value) addCashRegister();
        }, 
        onError:(e) => console.log(e)
    });
}

</script>
<template>
    <div v-show="user" class="p-2" :class="openBlock?'border rounded-md border-gray-200':''">
        <div class="grid grid-cols-2 gap-2">
            <div>{{ openBlock?'Модулькасса':'' }}</div>
            <div class="text-right">
                <SecondaryButton v-show="openBlock" @click="addCashRegister">+ Добавить</SecondaryButton>
                <SecondaryButton @click="getModulSettings" class="ml-2">{{ openBlock?'Скрыть':'Модулькасса' }}</SecondaryButton>
            </div>
        </div>
        <div v-show="openBlock">
            <div v-show="addCashRegisterBlock" class="mt-2">
                <div v-for="(point, index) in availableCashRegisters" :key="index" class="md:grid md:grid-cols-2 gap-2 hover:bg-gray-100">
                    <div class="cursor-pointer hover:bg-gray-200 rounded-md p-2" @click="getModulkassaUsers(point.id)">
                        <div>{{ point.name }}</div>
                        <div>{{ point.address }}</div>
                    </div>
                    <div>
                        <div v-for="user in point.users" class="p-2">
                            {{ user.name }}
                            <SecondaryButton @click="selectModulCashier(user.code, point)">Выбрать</SecondaryButton>
                        </div>
                    </div>
                </div>
            </div>
            
            <div v-for="(kassa, index) in cashRegisters" class="p-2">
                {{ kassa.details.name }}
                <span title="Отвязать кассу" class="text-red-500 pl-2 cursor-pointer" @click="removeCashRegister(kassa.cr_id)"><i class="ri-close-circle-line"></i></span>
            </div>
        </div>
    </div>
</template>
