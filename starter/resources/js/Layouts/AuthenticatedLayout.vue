<script setup>
import TopBar from "@starter/Layouts/Parts/TopBar.vue";
const props = defineProps({
    topMenuCurrent: {
        type: String,
        required: false,
    },
    sideMenuMainPages: {
        type: Array,
        required: true,
    },
    sideMenuSections: {
        type: Array,
        required: true,
    },
    sideMenuCurrent: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <div class="min-h-full">
        <TopBar :current="topMenuCurrent" />
        <div class="mx-auto max-w-7xl flex px-4 gap-x-4 lg:px-8 lg:gap-x-8">
            <main class="flex-auto px-0 py-8">
                <slot />
            </main>
        </div>
    </div>
</template>
