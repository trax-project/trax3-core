<script setup>
import ActivityTypeField from "@starter/Components/Forms/XapiFilters/ActivityTypeField.vue";
import InputLabel from "@starter/Components/Forms/Filters/InputLabel.vue";
import InputError from "@starter/Components/Forms/Filters/InputError.vue";
import FieldOptions from "@starter/Components/Forms/Filters/FieldOptions.vue";

defineProps({
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

const whereOptions = [{ id: "object", name: "In the object" }];
</script>

<template>
    <div class="relative md:col-span-2 flex flex-row">
        <InputLabel for="activity-type" value="Act. Type" />
        <div class="w-full">
            <div
                :class="[
                    'flex rounded-md w-full',
                    error ? 'ring-2 ring-red-500 dark:ring-red-400' : '',
                ]"
            >
                <div
                    class="relative flex flex-grow items-stretch focus-within:z-10"
                >
                    <ActivityTypeField
                        class="rounded-none rounded-l"
                        :modelValue="modelValue"
                        :error="error"
                        :showError="false"
                        @update:modelValue="$emit('update:modelValue', $event)"
                        @update:error="$emit('update:error', $event)"
                        @apply="$emit('apply')"
                    />
                </div>

                <FieldOptions
                    id="activity-type-where"
                    :right="true"
                    :disabled="true"
                    modelValue="object"
                    :options="whereOptions"
                />
            </div>
            <InputError :error="error" />
        </div>
    </div>
</template>
