<script setup>
import InputLabel from "@starter/Components/Forms/Filters/InputLabel.vue";
import TextInput from "@starter/Components/Forms/Filters/TextInput.vue";
import { useStoreSelector } from "@starter/Composables/StoreSelector";

const { currentStore } = useStoreSelector();

const props = defineProps({
    modelValue: {
        type: String,
        required: true,
    },
    error: {
        type: String,
        required: false,
    },
});

defineEmits(["update:modelValue", "update:error", "apply"]);

const completionEndpoint = "/trax/api/gateway/front/stores/" + currentStore() + "/verbs";
const completionProp = "iri";
const completionParams = (search) => {
    return {
        sort: ["iri"],
        limit: 10,
        filters: {
            in_iri: search,
        },
    };
};
</script>

<template>
    <div class="relative md:col-span-2 flex flex-row">
        <InputLabel for="verb" value="Verb" />
        <TextInput
            id="verb"
            placeholder="http://adlnet.gov/expapi/verbs/completed"
            :modelValue="modelValue"
            :error="error"
            @update:modelValue="$emit('update:modelValue', $event)"
            @update:error="$emit('update:error', $event)"
            @apply="$emit('apply')"
            :completionEndpoint="completionEndpoint"
            :completionProp="completionProp"
            :completionParams="completionParams"
        />
    </div>
</template>
