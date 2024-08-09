<script setup>
import { computed } from "vue";

const props = defineProps({
    id: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
    modelValue: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:modelValue"]);

const proxyChecked = computed({
    get() {
        return props.modelValue;
    },

    set(val) {
        emit("update:modelValue", val);
    },
});
</script>

<template>
    <div class="relative flex items-start">
        <div class="flex h-6 items-center">
            <input
                :name="id"
                :id="id"
                type="checkbox"
                class="cursor-pointer h-4 w-4 rounded border-gray-400 text-indigo-600 accent-pink-500 focus:ring-2 focus:ring-indigo-600 dark:border-gray-400 dark:text-indigo-400 dark:bg-gray-800 dark:focus:ring-indigo-400 dark:ring-offset-gray-700"
                v-model="proxyChecked"
            />
        </div>
        <div class="ml-3 text-sm leading-6">
            <label
                :for="id"
                class="cursor-pointer font-medium text-gray-900 dark:text-gray-200"
            >
                {{ label }}
            </label>
        </div>
    </div>
</template>
