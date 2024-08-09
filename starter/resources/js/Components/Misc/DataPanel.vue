<script setup>
import { ref } from "vue";
import {
    Dialog,
    DialogPanel,
    DialogTitle,
    TransitionChild,
    TransitionRoot,
} from "@headlessui/vue";
import { XMarkIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    titleStyle: {
        type: String,
        required: false,
    },
    size: {
        type: String,
        default: "medium",
    },
});

const emit = defineEmits(["update:show"]);

const close = () => {
    emit("update:show", false);
};
</script>

<template>
    <TransitionRoot as="template" :show="show">
        <Dialog as="div" class="relative z-10" @close="close">
            <div class="fixed inset-0" />

            <div class="fixed inset-0 overflow-hidden">
                <div class="absolute inset-0 overflow-hidden bg-black/20">
                    <div
                        class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-16"
                    >
                        <TransitionChild
                            as="template"
                            enter="transform transition ease-in-out duration-300"
                            enter-from="translate-x-full"
                            enter-to="translate-x-0"
                            leave="transform transition ease-in-out duration-300"
                            leave-from="translate-x-0"
                            leave-to="translate-x-full"
                        >
                            <DialogPanel
                                :class="[
                                    size == 'xl' ? 'max-w-7xl' : 'max-w-5xl',
                                    'pointer-events-auto w-screen',
                                ]"
                            >
                                <div
                                    class="flex h-full flex-col overflow-y-scroll shadow-xl bg-white dark:bg-gray-700"
                                >
                                    <div class="p-0">
                                        <div
                                            class="flex items-center justify-between"
                                        >
                                            <DialogTitle
                                                v-if="!titleStyle"
                                                class="text-center px-5 py-3 text-md font-semibold leading-6 text-white bg-fuchsia-500 dark:bg-orange-500"
                                            >
                                                <slot name="title"></slot>
                                            </DialogTitle>
                                            <DialogTitle
                                                v-if="titleStyle == 'neutral'"
                                                class="text-center px-5 py-3 text-md font-semibold leading-6 text-white bg-gray-500"
                                            >
                                                <slot name="title"></slot>
                                            </DialogTitle>
                                            <DialogTitle
                                                v-if="titleStyle == 'info'"
                                                class="text-center px-5 py-3 text-md font-semibold leading-6 text-white bg-blue-500"
                                            >
                                                <slot name="title"></slot>
                                            </DialogTitle>
                                            <DialogTitle
                                                v-if="titleStyle == 'warning'"
                                                class="text-center px-5 py-3 text-md font-semibold leading-6 text-white bg-orange-500"
                                            >
                                                <slot name="title"></slot>
                                            </DialogTitle>
                                            <DialogTitle
                                                v-if="titleStyle == 'alert'"
                                                class="text-center px-5 py-3 text-md font-semibold leading-6 text-white bg-red-500"
                                            >
                                                <slot name="title"></slot>
                                            </DialogTitle>
                                            <div class="p-3 flex items-center">
                                                <button
                                                    type="button"
                                                    class="rounded-md bg-transparent text-gray-500 hover:text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:text-white/60 dark:hover:text-white dark:focus:ring-white"
                                                    @click="close"
                                                >
                                                    <span class="sr-only"
                                                        >Close panel</span
                                                    >
                                                    <XMarkIcon
                                                        class="h-6 w-6"
                                                        aria-hidden="true"
                                                    />
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="relative flex-1 py-6 px-6 pt-3 dark:text-white"
                                    >
                                        <slot name="content"></slot>
                                    </div>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>
