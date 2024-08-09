<script setup>
import AgentField from "@starter/Components/Forms/XapiFilters/AgentField.vue";
import InputLabel from "@starter/Components/Forms/Filters/InputLabel.vue";
import InputError from "@starter/Components/Forms/Filters/InputError.vue";
import FieldOptions from "@starter/Components/Forms/Filters/FieldOptions.vue";
import { onMounted } from "vue";

const props = defineProps({
    modelValue: {
        type: Object,
        required: false,
    },
    location: {
        type: String,
        default: "everywhere",
    },
    error: {
        type: String,
        required: false,
    },
});

const emit = defineEmits([
    "update:modelValue",
    "update:location",
    "update:error",
    "apply",
]);

const whereOptions = [
    { id: "everywhere", name: "Everywhere" },
    { id: "actor", name: "As the actor" },
    { id: "object", name: "As the object" },
];

onMounted(() => {
    // Required to pass back the default value of the type and  location.
    emit("update:location", props.location);
});
</script>

<template>
    <div class="relative md:col-span-2 flex flex-row">
        <InputLabel for="agent" value="Agent" />
        <div class="w-full">
            <div
                :class="[
                    'flex rounded-md w-full',
                    error ? 'ring-2 ring-red-500 dark:ring-red-400' : '',
                ]"
            >
                <AgentField
                    :rounded="false"
                    :modelValue="modelValue"
                    :error="error"
                    :showError="false"
                    @update:modelValue="$emit('update:modelValue', $event)"
                    @update:error="$emit('update:error', $event)"
                    @apply="$emit('apply')"
                />

                <FieldOptions
                    id="agent-where"
                    :right="true"
                    :modelValue="location"
                    @update:modelValue="$emit('update:location', $event)"
                    :options="whereOptions"
                />
            </div>
            <InputError :error="error" />
        </div>
    </div>
</template>
