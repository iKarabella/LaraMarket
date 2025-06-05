<script setup>
import { computed } from 'vue';

const emit = defineEmits(['update:checked']);

const props = defineProps({
    checked: {
        type: [Array, Boolean],
        required: true,
    },
    value: {
        default: null,
    },
});

const proxyChecked = computed({
    get() {
        return props.checked;
    },

    set(val) {
        emit('update:checked', val);
    },
});
</script>

<template>
    <input
        type="checkbox"
        :value="value"
        v-model="proxyChecked"
        class="disabled:bg-gray-200 rounded dark:disabled:bg-gray-800 dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm enabled:focus:ring-indigo-500 dark:enabled:focus:ring-indigo-600 dark:enabled:focus:ring-offset-gray-800"
    />
</template>