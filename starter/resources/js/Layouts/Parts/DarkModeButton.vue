<script setup>
import { ref, watch } from "vue";
import { Switch } from "@headlessui/vue";
import { MoonIcon } from "@heroicons/vue/24/outline";
import { SunIcon } from "@heroicons/vue/24/solid";

const props = defineProps({
    dark: {
        type: Boolean,
        default: false,
    },
});

const darkByDefault =
    localStorage.theme === "dark" ||
    (!("theme" in localStorage) &&
        window.matchMedia("(prefers-color-scheme: dark)").matches);

const active = ref(darkByDefault);

watch(
    active,
    (active) => {
        if (active) {
            localStorage.theme = "dark";
            document.documentElement.classList.add("dark");
        } else {
            localStorage.theme = "light";
            document.documentElement.classList.remove("dark");
        }
    },
    { immediate: true }
);
</script>

<template>
    <Switch
        v-model="active"
        :class="[
            active ? 'bg-indigo-500' : dark ? 'bg-white/50' : 'bg-gray-400',
            'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2',
        ]"
    >
        <span class="sr-only">Use setting</span>
        <span
            :class="[
                active ? 'translate-x-5' : 'translate-x-0',
                'pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
            ]"
        >
            <span
                :class="[
                    active
                        ? 'opacity-0 duration-100 ease-out'
                        : 'opacity-100 duration-200 ease-in',
                    'absolute inset-0 flex h-full w-full items-center justify-center transition-opacity',
                ]"
                aria-hidden="true"
            >
                <SunIcon class="h-3 w-3 text-gray-400" />
            </span>
            <span
                :class="[
                    active
                        ? 'opacity-100 duration-200 ease-in'
                        : 'opacity-0 duration-100 ease-out',
                    'absolute inset-0 flex h-full w-full items-center justify-center transition-opacity',
                ]"
                aria-hidden="true"
            >
                <MoonIcon class="h-3 w-3 text-indigo-600" />
            </span>
        </span>
    </Switch>
</template>
