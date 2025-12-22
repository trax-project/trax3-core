<script setup>
import PrimaryButton from "@starter/Components/Buttons/PrimaryButton.vue";
import SecondaryButton from "@starter/Components/Buttons/SecondaryButton.vue";
import {
    Dialog,
    DialogPanel,
    DialogTitle,
    TransitionChild,
    TransitionRoot,
} from "@headlessui/vue";

defineProps({
    title: {
        type: String,
        required: true,
    },
    modelValue: {
        type: Boolean,
        required: true,
    },
    processing: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    size: {
        type: String,
        default: "lg",
    },
});

defineEmits(["update:modelValue", "confirm"]);
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
                            class="relative transform rounded-lg text-left shadow-xl transition-all my-8 w-full bg-white dark:ring-0 dark:bg-gray-800"
                            :class="'max-w-' + size"
                        >
                            <div>
                                <div>
                                    <DialogTitle
                                        as="h3"
                                        class="p-5 rounded-t-lg text-base font-semibold leading-6 text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 dark:bg-gray-800"
                                        >{{ title }}</DialogTitle
                                    >
                                    <div class="py-5 pb-8">
                                        <slot />
                                    </div>
                                </div>
                            </div>
                            <div
                                class="p-5 rounded-b-lg flex flex-row-reverse space-x-5 bg-gray-100 dark:bg-gray-800 dark:pt-3"
                            >
                                <PrimaryButton
                                    class="inline-flex justify-center ml-3"
                                    :processing="processing"
                                    @click="$emit('confirm')"
                                    v-if="!disabled"
                                >
                                    Save
                                </PrimaryButton>
                                <SecondaryButton
                                    class="inline-flex justify-center"
                                    @click="$emit('update:modelValue', false)"
                                >
                                    Cancel
                                </SecondaryButton>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>
