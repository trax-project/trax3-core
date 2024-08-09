<script setup>
import GuestLayout from "@starter/Layouts/GuestLayout.vue";
import ApplicationLogo from "@starter/Layouts/Parts/ApplicationLogo.vue";
import TextInput from "@starter/Components/Forms/Classic/TextInput.vue";
import InputLabel from "@starter/Components/Forms/Classic/InputLabel.vue";
import InputError from "@starter/Components/Forms/Classic/InputError.vue";
import { Head, useForm } from "@inertiajs/vue3";

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login-api"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log In" />

        <div class="mb-8">
            <ApplicationLogo />
        </div>

        <div
            class="mx-auto w-full max-w-md rounded-lg shadow-md p-8 bg-white dark:bg-gray-800"
        >
            <form @submit.prevent="submit">
                <div>
                    <InputLabel for="email" value="Email address" />

                    <div class="mt-2">
                        <TextInput
                            id="email"
                            type="email"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                        />
                    </div>

                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mt-3">
                    <InputLabel for="password" value="Password" />

                    <div class="mt-2">
                        <TextInput
                            id="password"
                            type="password"
                            class="mt-1 block w-full"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                        />
                    </div>

                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="mt-8">
                    <button
                        type="submit"
                        class="flex w-full justify-center rounded-md px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-500 dark:hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Sign in
                    </button>
                </div>
            </form>

            <p class="mt-10 text-center text-sm text-gray-500">
                Join the TRAX community and get extra features on
                <a
                    href="http://traxlrs.com"
                    target="_blank"
                    class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300"
                    >traxlrs.com</a
                >
            </p>
        </div>
    </GuestLayout>
</template>
