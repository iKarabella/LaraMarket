<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    act:{type:Object, default:{}}
});

const computedAct = computed(()=>{
    if(props.act.type=='write-off') return props.act.act;
    return props.act.act.map(function(pos){
        pos.amount = parseFloat(pos.price)*parseInt(pos.quantity);
        return pos;
    });
});

const actAmount = computed(()=>{
    if(props.act.type=='write-off') return null;
    let amount = 0;
    computedAct.value.forEach((position)=>{
        amount += position.amount;
    });
    return amount.toFixed(2)+' ₽';
});
const showAct = ref(false);

</script>
<template>
    <div>
        <div class="md:grid md:grid-cols-4 md:gap-2 cursor-pointer" @click="showAct=!showAct">
            <div>{{ act.created }}</div>
            <div>{{ act.user.name }}</div>
            <div v-if="act.type!='write-off'">{{ actAmount }}</div>
            <div>
                <span v-if="act.doc_id">{{ act.doc_id.type }}</span>
            </div>
        </div>
        <Transition>
            <div v-show="showAct">
                <div v-for="(position, index) in computedAct" class="md:grid md:grid-cols-10 md:gap-2">
                    <div>{{ index+1 }}</div>
                    <div class="col-span-5">{{ position.title }}</div>
                    <div class="text-right" v-if="act.type!='write-off'">{{ position.price }} ₽</div>
                    <div class="text-right">{{ position.quantity }}</div>
                    <div>{{ position.measure_val }}</div>
                    <div class="text-right" v-if="act.type!='write-off'">{{ position.amount.toFixed(2) }} ₽</div>
                </div>
                <div v-if="act.comment" v-html="act.comment"></div>
            </div>
        </Transition>
    </div>
</template>
<style scoped>
.v-enter-active,
.v-leave-active {
  transition: opacity 0.5s ease;
}

.v-enter-from,
.v-leave-to {
  opacity: 0;
}
</style>
