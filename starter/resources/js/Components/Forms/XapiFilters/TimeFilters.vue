<script setup>
import InputLabel from "@starter/Components/Forms/Filters/InputLabel.vue";
import TimestampFilter from "@starter/Components/Forms/XapiFilters/TimestampFilter.vue";

const props = defineProps({
    since: {
        type: String,
        required: true,
    },
    until: {
        type: String,
        required: true,
    },
    errors: {
        type: Object,
        required: true,
    },
});

defineEmits(["update:since", "update:until", "apply"]);
</script>

<template>
    <div class="relative flex flex-row">
        <InputLabel for="since" value="Since (>=)" class="sm:basis-32" />
        <TimestampFilter
            id="since"
            :modelValue="since"
            v-model:error="errors.since"
            @update:modelValue="$emit('update:since', $event)"
            @apply="$emit('apply')"
        />
    </div>

    <div class="relative flex flex-row">
        <InputLabel for="until" value="Until (<)" />
        <TimestampFilter
            id="until"
            :modelValue="until"
            v-model:error="errors.until"
            @update:modelValue="$emit('update:until', $event)"
            @apply="$emit('apply')"
        />
    </div>
</template>
