<script setup>
import InputError from "@starter/Components/Forms/Filters/InputError.vue";
import { watch } from "vue";

const props = defineProps({
    id: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        default: "text",
    },
    placeholder: {
        type: String,
        required: false,
    },
    modelValue: {
        type: [String, Number],
        required: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    error: {
        type: String,
        required: false,
    },
    showError: {
        type: Boolean,
        default: true,
    },
    class: {
        type: String,
        required: false,
    },
    prefix: {
        type: String,
        required: false,
    },
});

const emit = defineEmits(["update:modelValue", "update:error", "apply"]);

const customClass = () => props.class;

watch(
    () => props.modelValue,
    () => {
        emit("update:error", "");
    }
);
</script>

<template>
    <div class="w-full" v-if="prefix">
        <div
            :class="[
                customClass(),
                'flex w-full rounded-md focus-within:ring-2 focus-within:ring-inset ring-inset bg-white focus-within:ring-indigo-600 dark:bg-gray-800 dark:focus-within:ring-indigo-400',
                error && showError
                    ? 'ring-2 ring-red-500 dark:ring-red-400'
                    : 'ring-1 ring-gray-300 dark:ring-gray-600',
            ]"
        >
            <span
                class="flex select-none items-center pl-3 text-sm text-gray-500 dark:text-gray-200"
            >
                {{ prefix }}</span
            >
            <input
                :type="type"
                :id="id"
                :name="id"
                :placeholder="placeholder"
                class="block flex-1 border-0 bg-transparent py-1.5 pl-1 pr-3 focus:outline-none text-sm leading-6 focus:ring-0 placeholder:text-gray-400 dark:placeholder:text-gray-500"
                :class="disabled
                    ? 'text-gray-500 dark:text-gray-500'
                    : 'text-gray-900 dark:text-gray-200'
                "
                :value="modelValue"
                :disabled="disabled"
                @input="$emit('update:modelValue', $event.target.value)"
                @focus="$emit('update:error', '')"
                @keyup.enter="$emit('apply')"
            />
        </div>
        <InputError :error="error" v-if="showError" />
    </div>
    <div class="w-full p-0 bg-transparent border-0" v-else>
        <input
            :type="type"
            :id="id"
            :name="id"
            :placeholder="placeholder"
            :class="[
                customClass(),
                'focus-within:z-10 block w-full rounded-md border-0 py-1.5 px-3 text-sm leading-6 ring-inset placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:bg-gray-800 dark:placeholder:text-gray-500 dark:focus:ring-indigo-400',
                error && showError
                    ? 'ring-2 ring-red-500 dark:ring-red-400'
                    : 'ring-1 ring-gray-300 dark:ring-gray-600',
                disabled
                    ? 'text-gray-500 dark:text-gray-500'
                    : 'text-gray-900 dark:text-gray-200',
            ]"
            :value="modelValue"
            :disabled="disabled"
            @input="$emit('update:modelValue', $event.target.value)"
            @focus="$emit('update:error', '')"
            @keyup.enter="$emit('apply')"
        />
        <InputError :error="error" v-if="showError" />
    </div>
</template>
