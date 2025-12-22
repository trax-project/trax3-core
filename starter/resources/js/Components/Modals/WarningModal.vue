<script setup>
import SecondaryButton from "@starter/Components/Buttons/SecondaryButton.vue";
import {
    Dialog,
    DialogPanel,
    DialogTitle,
    TransitionChild,
    TransitionRoot,
} from "@headlessui/vue";
import { ExclamationTriangleIcon } from "@heroicons/vue/24/outline";

defineProps({
    title: {
        type: String,
        required: true,
    },
    modelValue: {
        type: Boolean,
        required: true,
    },
});

defineEmits(["update:modelValue"]);
</script>

<template>
    <TransitionRoot as="template" :show="modelValue">
        <Dialog
            as="div"
            class="relative z-10"
            @close="$emit('update:modelValue', false)"
        >
            <TransitionChild
                as="template"
                enter="ease-out duration-300"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="ease-in duration-200"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div
                    class="fixed inset-0 bg-gray-500/75 transition-opacity"
                />
            </TransitionChild>

            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div
                    class="flex min-h-full justify-center text-center items-center p-0"
                >
                    <TransitionChild
                        as="template"
                        enter="ease-out duration-300"
                        enter-from="opacity-0 translate-y-0 scale-95"
                        enter-to="opacity-100 translate-y-0 scale-100"
                        leave="ease-in duration-200"
                        leave-from="opacity-100 translate-y-0 scale-100"
                        leave-to="opacity-0 translate-y-0 scale-95"
                    >
                        <DialogPanel
                            class="relative transform rounded-lg text-left shadow-xl transition-all my-8 w-full max-w-lg bg-white dark:ring-0 dark:bg-gray-800"
                        >
                            <div class="p-6 pb-0">
                                <div class="flex items-start">
                                    <div
                                        class="flex flex-shrink-0 items-center justify-center rounded-full mx-0 h-10 w-10 bg-orange-100 dark:bg-orange-500"
                                    >
                                        <ExclamationTriangleIcon
                                            class="h-6 w-6 text-orange-600 dark:text-white"
                                            aria-hidden="true"
                                        />
                                    </div>
                                    <div class="ml-4 mt-0 text-left">
                                        <DialogTitle
                                            as="h3"
                                            class="text-base uppercase font-semibold leading-6 text-gray-900 dark:text-gray-100"
                                        >
                                            {{ title }}</DialogTitle
                                        >
                                        <div class="mt-2">
                                            <p
                                                class="text-sm text-gray-500 dark:text-gray-400"
                                            >
                                                <slot />
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="px-6 py-4 flex flex-row-reverse space-x-5"
                            >
                                <SecondaryButton
                                    class="inline-flex justify-center"
                                    @click="$emit('update:modelValue', false)"
                                >
                                    OK
                                </SecondaryButton>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>
