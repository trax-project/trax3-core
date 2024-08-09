<script setup>
import PrimaryButton from "@starter/Components/Buttons/PrimaryButton.vue";
import { useXapiProps } from "@starter/Composables/XapiProps";

const emit = defineEmits(["loadMore", "show"]);

const props = defineProps({
    loadedItems: {
        type: Array,
        required: true,
    },
    hasMoreItems: {
        type: Boolean,
        default: false,
    },
    loadingItems: {
        type: Boolean,
        default: false,
    },
});

const {
    actorType,
    actorName,
    verb,
    objectType,
    objectName,
    storedDate,
    storedTime,
} = useXapiProps();

</script>

<template>
    <div
        class="relative overflow-hidden px-0 rounded-lg bg-white shadow-md dark:ring-0 dark:bg-gray-800"
    >
        <div class="flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div
                    class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8"
                >
                    <table
                        class="min-w-full divide-y divide-gray-300 dark:divide-gray-500"
                    >
                        <thead>
                            <tr>
                                <th
                                    scope="col"
                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-500 w-0"
                                ></th>
                                <th
                                    scope="col"
                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-500"
                                >
                                    Actor
                                </th>
                                <th
                                    scope="col"
                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-500"
                                >
                                    Verb
                                </th>
                                <th
                                    scope="col"
                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-500"
                                >
                                    Object
                                </th>
                                <th
                                    scope="col"
                                    class="w-32 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-500"
                                >
                                    Stored
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-gray-200 dark:divide-gray-700"
                        >
                            <tr
                                v-for="(item, index) in loadedItems"
                                :key="index"
                                class="group hover:cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700/50"
                                @click="
                                    $emit('show', {
                                        index,
                                        content: item.raw,
                                        content_type: 'application/json',
                                    })
                                "
                            >
                                <td
                                    class="pl-6 pr-3 py-4 text-sm text-center font-bold text-fuchsia-500 dark:text-orange-500 border-l-2 border-white group-hover:border-fuchsia-500 dark:group-hover:border-orange-600 dark:border-gray-800"
                                >
                                    {{ index + 1 }}
                                </td>
                                <td class="px-3 py-4">
                                    <div
                                        class="text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        {{ actorType(item.raw.actor) }}
                                    </div>
                                    <div
                                        class="text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        {{ actorName(item.raw.actor) }}
                                    </div>
                                </td>
                                <td
                                    class="px-3 py-4 text-sm text-gray-500 dark:text-gray-300"
                                >
                                    {{ verb(item.raw.verb) }}
                                </td>
                                <td
                                    class="px-3 py-4 text-sm text-gray-500 dark:text-gray-300"
                                >
                                    <div
                                        class="text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        {{ objectType(item.raw.object) }}
                                    </div>
                                    <div
                                        class="text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        {{ objectName(item.raw.object) }}
                                    </div>
                                </td>
                                <td
                                    class="pl-3 pr-6 py-4 text-sm text-gray-500 dark:text-gray-300"
                                >
                                    <div
                                        class="text-xs text-gray-500 dark:text-gray-400"
                                    >
                                        {{ storedDate(item.raw.stored) }}
                                    </div>
                                    <div
                                        class="text-sm font-medium text-gray-900 dark:text-white"
                                    >
                                        {{ storedTime(item.raw.stored) }}
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-center mt-4 mb-6" v-if="hasMoreItems">
            <PrimaryButton :processing="loadingItems" @click="emit('loadMore')">
                Load More
            </PrimaryButton>
        </div>
    </div>
</template>
