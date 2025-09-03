<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import MarketLayout from '@/Layouts/MarketLayout.vue';
import InputLabel from '@/Components/UI/InputLabel.vue';
import TextInput from '@/Components/UI/TextInput.vue';
import InputError from '@/Components/UI/InputError.vue';
import SecondaryButton from '@/Components/UI/SecondaryButton.vue';
import Tiptap from '@/Components/Tiptap/Tiptap.vue';
import PrimaryButton from '@/Components/UI/PrimaryButton.vue';
import Modal from '@/Components/Modals/MainModal.vue';
import Checkbox from '@/Components/UI/Checkbox.vue';
import { ref } from 'vue';

const props = defineProps({
    navigation:{type:Array, default:[]},
    warehouse: {type:Object, default:{}}
});

const warehouseForm = useForm({
    id:props.warehouse.id??null,
    title:props.warehouse.title??null,
    code:props.warehouse.code??null,
    address:props.warehouse.address??'',
    phone:props.warehouse.phone??'',
    caschier:props.warehouse.caschier??null,                   
    description:props.warehouse.description??'',
});

const storeWarehouse = ()=>{
    warehouseForm.post(route('admin.warehouses.store'), {
        preserveScroll:true,
        onSuccess:(e)=>console.log(warehouseForm.errors), 
        onError:(e)=>console.log(e)
    });
};

const updateContent = (content) => {
    warehouseForm.description=content;
};

const addCashRegisterForm = useForm({
    cash_registers:[],
    warehouse_id: props.warehouse.id
});
const cashRegistersModal = ref(false);
const addCashRegister = () => {
    axios.get(route('admin.warehouse.cashRegisters', [props.warehouse.code])).then(res=>{
        if (res.data) {
            addCashRegisterForm.cash_registers = res.data.map(arr=>{
                return {
                    id:arr.id,
                    name:arr.name,
                    address:arr.address,
                    phone:arr.phone,
                    addForWarehouse:false
                }
            });
            cashRegistersModal.value = true;
        }
    });
}
const storeCashRegisters = () => {
    addCashRegisterForm.put(route('admin.warehouse.cashRegisters', props.warehouse.code), {
        preserveScroll:true,
        onSuccess:()=>modalClose(), 
        onError:(e)=>console.log(e)
    });
};
const modalClose = ()=>{
    cashRegistersModal.value=false;
    addCashRegisterForm.reset();
}

const cashRegisterRemoveForm = useForm({
    warehouse_id:props.warehouse.id,
    guid: null,
});

const removeCashRegister = (guid)=>{
    cashRegisterRemoveForm.guid = guid;
    cashRegisterRemoveForm.delete(route('admin.warehouse.cashRegisters', props.warehouse.code), {
        preserveScroll:true,
        onSuccess:() => cashRegisterRemoveForm.reset(), 
        onError:(e) => console.log(e)
    });
}
</script>

<template>
    <Head title="Warehouse"/>
    <MarketLayout :navigation="navigation">
        <div>
            <div class="md:grid md:grid-cols-2 md:gap-2">
                <div class="mt-2">
                    <InputLabel for="title" value="Название" />
                    <TextInput
                        type="text"
                        id="title"
                        name="title"
                        class="w-full mt-1 block "
                        required
                        v-model="warehouseForm.title"
                        autocomplete="off"
                    />
                    <InputError class="ml-2" :message="warehouseForm.errors.title" />
                </div>
                <div class="mt-2">
                    <InputLabel for="code" value="Код" />
                    <TextInput
                        type="text"
                        id="code"
                        name="code"
                        class="w-full mt-1 block"
                        required
                        v-model="warehouseForm.code"
                        autocomplete="off"
                    />
                    <InputError class="ml-2" :message="warehouseForm.errors.code" />
                </div>
            </div>
            <div class="md:grid md:grid-cols-2 md:gap-2">
                <div class="mt-2">
                    <InputLabel for="address" value="Адрес" />
                    <TextInput
                        type="text"
                        id="address"
                        name="address"
                        class="w-full mt-1 block"
                        required
                        v-model="warehouseForm.address"
                        autocomplete="off"
                    />
                    <InputError class="ml-2" :message="warehouseForm.errors.address" />
                </div>
                <div class="mt-2">
                    <InputLabel for="phone" value="Телефон" />
                    <TextInput
                        type="text"
                        id="phone"
                        name="phone"
                        class="w-full mt-1 block"
                        required
                        v-model="warehouseForm.phone"
                        autocomplete="off"
                    />
                    <InputError class="ml-2" :message="warehouseForm.errors.phone" />
                </div>
            </div>
            <div class="mt-2">
                <span>Описание</span>
                <Tiptap :content="warehouseForm.description" @updateContent="updateContent"/>
                <InputError class="ml-2" :message="warehouseForm.errors.description" />
            </div>
            
            <div class="text-right mt-2">
                <Link :href="route('admin.warehouses.manage')" class="mr-2">
                    <SecondaryButton>Отменить</SecondaryButton>
                </Link>
                <PrimaryButton @click="storeWarehouse">Сохранить</PrimaryButton>
            </div>

            <div class="mt-2">
                <h2>МодульКасса</h2>
                <div>
                    <div v-for="(kassa, index) in warehouse.cash_registers">
                        {{ kassa.details.name }}
                        <span title="Отвязать кассу" class="text-red-500 pl-2 cursor-pointer" @click="removeCashRegister(kassa.cr_id)"><i class="ri-close-circle-line"></i></span>
                    </div>
                    <SecondaryButton @click="addCashRegister">+ Добавить</SecondaryButton>
                </div>
            </div>
            <Modal :show="cashRegistersModal" @close="closeModal()">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Выберите кассу
                    </h2>

                    <div class="mt-6 md:grid md:grid-cols-3 md:gap-2">
                        <label v-for="(point, index) in addCashRegisterForm.cash_registers" :key="index">
                            <div>
                                <Checkbox @change="addCashRegisterForm.cash_registers[index].addForWarehouse=!addCashRegisterForm.cash_registers[index].addForWarehouse"/>
                                {{ point.name }}
                            </div>
                            <div>{{ point.address }}</div>
                        </label>
                    </div>
                    
                    <div class="mt-2 md:flex gap-2 justify-end">
                        <SecondaryButton @click="modalClose">Закрыть</SecondaryButton>
                        <PrimaryButton @click="storeCashRegisters">Сохранить</PrimaryButton>
                    </div>
                </div>
            </Modal>
        </div>
    </MarketLayout>
</template>
