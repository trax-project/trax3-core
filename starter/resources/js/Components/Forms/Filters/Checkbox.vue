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
    disabled: {
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
            <div class="group grid size-4 grid-cols-1">

                <input
                    :name="id"
                    :id="id"
                    type="checkbox"
                    :disabled="disabled"
                    class="cursor-pointer col-start-1 row-start-1 appearance-none rounded-sm
                        border border-gray-300 checked:border-indigo-600 indeterminate:border-indigo-600 dark:border-white/10 dark:checked:border-indigo-500 dark:indeterminate:border-indigo-500
                        bg-white checked:bg-indigo-600 indeterminate:bg-indigo-600 dark:bg-white/5 dark:checked:bg-indigo-500 dark:indeterminate:bg-indigo-500
                        focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:focus-visible:outline-indigo-500
                        disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 dark:disabled:border-white/5 dark:disabled:bg-white/10 dark:disabled:checked:bg-white/10
                        forced-colors:appearance-auto
                    "
                    v-model="proxyChecked"
                />
                
                <svg class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-disabled:stroke-white/25" viewBox="0 0 14 14" fill="none">
                    <path class="opacity-0 group-has-checked:opacity-100" d="M3 8L6 11L11 3.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path class="opacity-0 group-has-indeterminate:opacity-100" d="M3 7H11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>

            </div>
        </div>
        <div class="ml-3 text-sm leading-6">
            <label
                :for="id"
                class="font-medium"
                :class="disabled
                    ? 'text-gray-500 dark:text-gray-500'
                    : 'text-gray-900 dark:text-gray-200 cursor-pointer'
                "
            >
                {{ label }}
            </label>
        </div>
    </div>
</template>
