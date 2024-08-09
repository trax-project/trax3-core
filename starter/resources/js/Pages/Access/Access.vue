<script setup>
import AccessLayout from "@starter/Layouts/AccessLayout.vue";
import { Head } from "@inertiajs/vue3";

defineProps({
    settings: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head title="Default Endpoint" />

    <AccessLayout side-menu-current="Default Endpoint">
        <div
            class="rounded-lg shadow-md mb-10 pb-2 bg-white dark:ring-0 dark:bg-gray-800"
        >
            <div class="p-5 border-b-2 border-gray-200 dark:border-gray-700">
                <div
                    class="-ml-4 -mt-2 flex flex-wrap items-center justify-between sm:flex-nowrap"
                >
                    <div class="ml-4 mt-2 max-w-lg">
                        <h3
                            class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100"
                        >
                            Super-Admin
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            The super-admin has all the permissions.
                            <br />Its credentials can be set in the following environment
                            variables.
                        </p>
                    </div>
                </div>
            </div>
            <table
                class="table-auto min-w-full divide-y divide-gray-300 dark:divide-gray-500"
            >
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <tr>
                        <td
                            class="px-5 py-3 text-sm text-gray-900 dark:text-white w-1/2"
                        >
                            ADMIN_EMAIL
                        </td>
                        <td
                            class="px-5 py-3 text-sm text-gray-900 dark:text-white"
                        >
                            {{ settings.ADMIN_EMAIL }}
                        </td>
                    </tr>
                    <tr>
                        <td
                            class="px-5 py-3 text-sm text-gray-900 dark:text-white"
                        >
                            ADMIN_PASSWORD
                        </td>
                        <td
                            class="px-5 py-3 text-sm text-gray-900 dark:text-white"
                        >
                            ********
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            class="rounded-lg shadow-md mb-10 pb-2 bg-white dark:ring-0 dark:bg-gray-800"
        >
            <div class="p-5 border-b-2 border-gray-200 dark:border-gray-700">
                <div
                    class="-ml-4 -mt-2 flex flex-wrap items-center justify-between sm:flex-nowrap"
                >
                    <div class="ml-4 mt-2 max-w-lg">
                        <h3
                            class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100"
                        >
                            Default xAPI Endpoint
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Here is the default xAPI endpoint.
                            The username and password can be set in the following environment variables.


                        </p>
                    </div>
                </div>
            </div>
            <table
                class="table-auto min-w-full divide-y divide-gray-300 dark:divide-gray-500"
            >
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <tr>
                        <td
                            class="px-5 py-3 text-sm text-gray-900 dark:text-white w-1/2"
                        >
                            URL

                            <div class="mt-2 text-xs text-gray-500">
                                Not configurable
                            </div>
                        </td>
                        <td
                            class="px-5 py-3 text-sm text-gray-900 dark:text-white"
                        >
                            {{ settings.DEFAULT_ENDPOINT_URL }}
                        </td>
                    </tr>
                    <tr>
                        <td
                            class="px-5 py-3 text-sm text-gray-900 dark:text-white"
                        >
                            DEFAULT_ENDPOINT_USERNAME
                        </td>
                        <td
                            class="px-5 py-3 text-sm text-gray-900 dark:text-white"
                        >
                            {{ settings.DEFAULT_ENDPOINT_USERNAME }}
                        </td>
                    </tr>
                    <tr>
                        <td
                            class="px-5 py-3 text-sm text-gray-900 dark:text-white"
                        >
                            DEFAULT_ENDPOINT_PASSWORD
                        </td>
                        <td
                            class="px-5 py-3 text-sm text-gray-900 dark:text-white"
                        >
                            ********
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
    </AccessLayout>
</template>
