<script setup>
import TextInput from "@starter/Components/Forms/Filters/TextInput.vue";

defineProps({
    id: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <TextInput :id="id" placeholder="2020-12-31 23:59:59" />
</template>
