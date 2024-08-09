<script setup>
import NeutralBadge from "@starter/Components/Badges/NeutralBadge.vue";
import InfoBadge from "@starter/Components/Badges/InfoBadge.vue";
import WarningBadge from "@starter/Components/Badges/WarningBadge.vue";
import AlertBadge from "@starter/Components/Badges/AlertBadge.vue";

defineProps({
    style: {
        type: String,
        default: "neutral",
    },
});
</script>

<template>
    <NeutralBadge v-if="style == 'neutral'">
        <slot></slot>
    </NeutralBadge>

    <InfoBadge v-if="style == 'info'">
        <slot></slot>
    </InfoBadge>

    <WarningBadge v-if="style == 'warning'">
        <slot></slot>
    </WarningBadge>

    <AlertBadge v-if="style == 'alert'">
        <slot></slot>
    </AlertBadge>
</template>
