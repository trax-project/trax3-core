<script setup>
import DarkModeButton from "@starter/Layouts/Parts/DarkModeButton.vue";
</script>

<template>
    <header class="w-full text-right p-6 absolute top-0">
        <DarkModeButton></DarkModeButton>
    </header>

    <div
        class="flex min-h-full flex-col justify-center px-6 pt-6 pb-24 bg-gray-200 dark:bg-gray-900"
    >
        <slot />
    </div>
</template>
