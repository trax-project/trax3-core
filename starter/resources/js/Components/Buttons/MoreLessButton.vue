<script setup>
import { ChevronUpIcon } from "@heroicons/vue/20/solid";
import { ChevronDownIcon } from "@heroicons/vue/20/solid";

const props = defineProps({
    moreLabel: {
        type: String,
        default: "More",
    },
    lessLabel: {
        type: String,
        default: "Less",
    },
    modelValue: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:modelValue"]);
</script>

<template>
    <button
        type="button"
        @click="$emit('update:modelValue', !modelValue)"
        class="cursor-pointer inline-flex items-center gap-x-1 rounded px-3.5 py-2 text-sm font-semibold text-indigo-600 bg-transparent focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 hover:text-indigo-500 dark:hover:bg-white/10 dark:text-fuchsia-400 dark:hover:text-fuchsia-400 dark:focus-visible:outline-fuchsia-400"
    >
        {{ modelValue ? lessLabel : moreLabel }}
        <ChevronUpIcon
            class="-mr-0.5 h-5 w-5"
            aria-hidden="true"
            v-if="modelValue"
        />
        <ChevronDownIcon class="-mr-0.5 h-5 w-5" aria-hidden="true" v-else />
    </button>
</template>
