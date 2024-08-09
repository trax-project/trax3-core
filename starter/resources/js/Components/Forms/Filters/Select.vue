<script setup>
import { computed } from "vue";
import {
    Listbox,
    ListboxButton,
    ListboxOption,
    ListboxOptions,
} from "@headlessui/vue";
import { CheckIcon, ChevronUpDownIcon } from "@heroicons/vue/20/solid";
import Badge from "@starter/Components/Badges/NeutralBadge.vue";

const props = defineProps({
    id: {
        type: String,
        required: true,
    },
    modelValue: {
        type: [String, Number, Boolean, Array],
        required: false,
    },
    options: {
        type: Array,
        required: true,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    multiple: {
        type: Boolean,
        default: false,
    },
    lines: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:modelValue"]);

const selected = computed({
    get() {
        if (props.multiple) {
            return props.options.filter((option) => {
                return props.modelValue.includes(option.id);
            });
        } else {
            let select = props.options.find(
                (option) => props.modelValue == option.id
            );
            // We always return something to avoid recursive error.
            // But we should always find an option matching with the current value.
            if (!select) {
                console.log('Select component issue: no option matching with the selected value!');
                return props.options[0];
            }
            return select;
        }
    },
    set(options) {
        if (props.multiple) {
            emit(
                "update:modelValue",
                options.map((option) => option.id)
            );
        } else {
            emit("update:modelValue", options.id);
        }
    },
});
</script>

<template>
    <Listbox as="div" v-model="selected" :multiple="multiple" class="w-full">
        <div class="relative">
            <ListboxButton
                :class="[
                    lines
                        ? (disabled ? '' : 'bg-opacity-0 text-white text-opacity-80 dark:bg-opacity-0 .ring-gray-300 focus:ring-white/80')
                        : (disabled ? '' : 'dark:ring-gray-600 focus:ring-indigo-600 dark:focus:ring-indigo-400'),
                    disabled
                        ? 'cursor-default'
                        : 'cursor-pointer focus-within:z-10 focus:outline-none focus:ring-2',
                    'rounded-md relative w-full py-1.5 pl-3 pr-10 text-sm leading-6 text-left ring-1 ring-inset bg-white text-gray-900 ring-gray-300 dark:bg-gray-800 dark:text-gray-200',
                ]"
            >
                <div v-if="multiple">
                    <span
                        class="block truncate text-gray-500"
                        v-if="selected.length == 0"
                    >
                        Select some options
                    </span>

                    <div class="pt-1" v-else>
                        <Badge
                            v-for="(option, index) in selected"
                            :key="index"
                            class="mr-1 mb-1"
                            >{{ option.name }}</Badge
                        >
                    </div>
                </div>

                <span class="block truncate" v-if="!multiple">
                    {{ selected.name }}
                </span>

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
                    class="absolute z-10 mt-1 max-h-80 w-full overflow-auto rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none text-sm"
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
                                    multiple ? 'pl-6' : '',
                                    'block truncate',
                                ]"
                                >{{ option.name }}</span
                            >

                            <span
                                v-if="selected && multiple"
                                :class="[
                                    'absolute inset-y-0 left-0 flex items-center pl-2 text-indigo-600',
                                ]"
                            >
                                <CheckIcon class="h-5 w-5" aria-hidden="true" />
                            </span>
                        </li>
                    </ListboxOption>
                </ListboxOptions>
            </transition>
        </div>
    </Listbox>
</template>
