<script setup>
defineProps({
    error: {
        type: String,
        required: false,
    },
});
</script>

<template>
    <div v-show="error">
        <p class="pl-1 pt-1 text-sm text-red-600 dark:text-red-400">
            {{ error }}
        </p>
    </div>
</template>
