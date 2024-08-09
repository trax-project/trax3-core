<script setup>
import AuthenticatedLayout from "@starter/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
    sideMenuCurrent: {
        type: String,
        required: true,
    },
});

const mainPages = [];
const sections = [];

</script>

<template>
    <AuthenticatedLayout
        top-menu-current="Access"
        :side-menu-current="sideMenuCurrent"
        :side-menu-main-pages="mainPages"
        :side-menu-sections="sections"
    >
        <slot />
    </AuthenticatedLayout>
</template>
