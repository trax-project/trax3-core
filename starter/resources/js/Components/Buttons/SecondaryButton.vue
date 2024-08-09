<script setup>
defineEmits(["click"]);
</script>

<template>
    <button
        type="button"
        @click="$emit('click')"
        class="rounded px-3.5 py-2 text-sm font-semibold shadow-sm ring-1 ring-inset ring-gray-300 text-gray-900 bg-white hover:bg-gray-50 dark:text-gray-300 dark:font-normal dark:bg-transparent dark:hover:bg-white/5"
    >
        <slot />
    </button>
</template>
