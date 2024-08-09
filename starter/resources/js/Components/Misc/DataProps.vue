<script setup>
defineProps({
    props: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="border-b border-b-gray-300 dark:border-b-gray-500 pb-8 mb-8">
        <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-500">
            <tbody class="">
                <tr v-for="(propVal, propName) in props" :key="propName">
                    <td class="p-2 w-52" v-if="propVal != null">
                        <div
                            class="text-sm font-medium text-black dark:text-white"
                        >
                            {{ propName }}:
                        </div>
                    </td>
                    <td class="p-1" v-if="propVal != null">
                        <code class="text-sm text-gray-400 dark:text-gray-300">
                            {{ propVal }}
                        </code>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
