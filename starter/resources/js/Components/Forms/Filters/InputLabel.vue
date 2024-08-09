<script setup>
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <label
        class="basis-28 pt-1 block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200"
    >
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
