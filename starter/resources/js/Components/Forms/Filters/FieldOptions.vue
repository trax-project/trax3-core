<script setup>
import { computed } from "vue";
import {
    Listbox,
    ListboxButton,
    ListboxLabel,
    ListboxOption,
    ListboxOptions,
} from "@headlessui/vue";
import { CheckIcon, ChevronUpDownIcon } from "@heroicons/vue/20/solid";

const props = defineProps({
    id: {
        type: String,
        required: true,
    },
    modelValue: {
        type: String,
        required: true,
    },
    options: {
        type: Array,
        required: true,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    right: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:modelValue"]);

const selected = computed({
    get() {
        return props.options.find((option) => option.id === props.modelValue);
    },
    set(option) {
        emit("update:modelValue", option.id);
    },
});
</script>

<template>
    <Listbox as="div" v-model="selected">
        <div class="relative">
            <ListboxButton
                :class="[
                    disabled
                        ? 'cursor-default'
                        : 'cursor-pointer focus-within:z-10 focus:ring-2 focus:ring-indigo-600 dark:focus:ring-indigo-400',
                    right ? 'rounded-r-md' : 'rounded-l-md',
                    'relative w-full py-1.5 pl-3 pr-10 focus:outline-none text-sm leading-6 text-left ring-1 ring-inset bg-white text-gray-900 ring-gray-300 dark:bg-gray-800 dark:text-gray-200 dark:ring-gray-600',
                ]"
            >
                <span class="block truncate">{{ selected.name }}</span>
                <span
                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2"
                >
                    <ChevronUpDownIcon
                        class="h-5 w-5 text-gray-400"
                        aria-hidden="true"
                        v-if="!disabled"
                    />
                </span>
            </ListboxButton>

            <transition
                leave-active-class="transition ease-in duration-100"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
                v-if="!disabled"
            >
                <ListboxOptions
                    class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 shadow-lg ring-1 ring-gray-300 dark:ring-white/10 focus:outline-none text-sm"
                >
                    <ListboxOption
                        as="template"
                        v-for="option in options"
                        :key="option.id"
                        :value="option"
                        v-slot="{ active, selected }"
                    >
                        <li
                            :class="[
                                active
                                    ? 'bg-gray-200 dark:bg-gray-300 dark:text-gray-900'
                                    : 'text-gray-900',
                                'relative cursor-default select-none py-2 px-3',
                            ]"
                        >
                            <span
                                :class="[
                                    selected ? 'font-semibold' : 'font-normal',
                                    'block truncate',
                                ]"
                                >{{ option.name }}</span
                            >
                        </li>
                    </ListboxOption>
                </ListboxOptions>
            </transition>
        </div>
    </Listbox>
</template>
