<script setup>
import AuthenticatedLayout from "@starter/Layouts/AuthenticatedLayout.vue";
import StoreSwitch from "@starter/Components/Misc/StoreSwitch.vue";

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
        top-menu-current="Statements"
        :side-menu-current="sideMenuCurrent"
        :side-menu-main-pages="mainPages"
        :side-menu-sections="sections"
    >
        <template v-slot:sidebar-top>
            <StoreSwitch />
        </template>

        <slot/>

    </AuthenticatedLayout>
</template>
