<script setup>
const props = defineProps({
    compact: {
        type: Boolean,
        default: false,
    },
    dark: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <div class="flex flex-shrink-0 items-center justify-center">
        <!--
        <div
            v-if="compact"
            class="ml-1 text-2xl text-white bg-gradient-to-b from-pink-500 to-purple-500 text-center py-1 h-10 w-10 lg:hidden"
        >
            TX
        </div>
        -->
        <div
            v-if="compact"
            class="ml-1 h-10 lg:hidden font-extrabold text-transparent text-4xl bg-clip-text bg-gradient-to-r from-purple-500 to-pink-500"
        >
            TX
        </div>

        <h1
            :class="[
                compact ? 'text-xl hidden md:visible lg:block' : 'text-3xl',
                dark ? 'text-white' : 'text-gray-800 dark:text-white',
                'ml-2 font-semibold text-center',
            ]"
        >
            TRAX LRS
            <span
                :class="[
                    dark
                        ? 'text-fuchsia-400'
                        : 'text-fuchsia-500 dark:text-fuchsia-400',
                    'font-normal',
                ]"
                style="font-size: 0.7em"
                >3.0</span
            >
        </h1>
    </div>
</template>
