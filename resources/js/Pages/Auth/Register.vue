<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import PasswordInput from '@/Components/Forms/PasswordInput.vue';
import SubmitButton from '@/Components/Forms/SubmitButton.vue';

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    password: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <AuthLayout>
        <Head title="Create Account" />

        <div class="px-8 pt-8 pb-6 text-center">
            <div class="mx-auto mb-6 flex items-center justify-center">
                <img :src="'/assets/images/logo-obl.png'" alt="Logo" class="h-12 w-auto" />
            </div>
            <h1 class="text-slate-900 dark:text-white text-2xl font-bold tracking-tight">Create Account</h1>
            <p class="mt-2 text-slate-500 dark:text-slate-400 text-sm">Sign up to get started</p>
        </div>

        <div class="px-8 pb-10">
            <form @submit.prevent="submit" class="flex flex-col gap-5">

                <div class="flex flex-col sm:flex-row gap-5">
                    <!-- First Name -->
                    <div class="flex-1">
                        <label for="first_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">First Name</label>
                        <input
                            v-model="form.first_name"
                            id="first_name"
                            type="text"
                            class="w-full h-11 px-4 text-slate-900 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary dark:bg-slate-800 dark:border-slate-700 dark:text-white outline-none transition-all"
                            :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': form.errors.first_name }"
                            placeholder="John"
                            required
                            autofocus
                        />
                        <div v-if="form.errors.first_name" class="mt-1.5 text-sm text-red-600 dark:text-red-400">
                            {{ form.errors.first_name }}
                        </div>
                    </div>

                    <!-- Last Name -->
                    <div class="flex-1">
                        <label for="last_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Last Name</label>
                        <input
                            v-model="form.last_name"
                            id="last_name"
                            type="text"
                            class="w-full h-11 px-4 text-slate-900 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary dark:bg-slate-800 dark:border-slate-700 dark:text-white outline-none transition-all"
                            :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': form.errors.last_name }"
                            placeholder="Doe"
                            required
                        />
                        <div v-if="form.errors.last_name" class="mt-1.5 text-sm text-red-600 dark:text-red-400">
                            {{ form.errors.last_name }}
                        </div>
                    </div>
                </div>

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Email Address</label>
                    <input
                        v-model="form.email"
                        id="email"
                        type="email"
                        class="w-full h-11 px-4 text-slate-900 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary dark:bg-slate-800 dark:border-slate-700 dark:text-white outline-none transition-all"
                        :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': form.errors.email }"
                        placeholder="you@example.com"
                        required
                    />
                    <div v-if="form.errors.email" class="mt-1.5 text-sm text-red-600 dark:text-red-400">
                        {{ form.errors.email }}
                    </div>
                </div>

                <!-- Password Input -->
                <PasswordInput
                    v-model="form.password"
                    id="password"
                    label="Password"
                    placeholder="Create a strong password"
                    :error="form.errors.password"
                />

                <!-- Terms -->
                <div class="flex flex-col gap-1.5">
                    <div class="flex items-start">
                        <div class="flex h-6 items-center">
                            <input
                                id="terms"
                                v-model="form.terms"
                                type="checkbox"
                                class="h-4 w-4 rounded border-slate-300 text-primary focus:ring-primary dark:border-slate-600 dark:bg-slate-800 dark:ring-offset-slate-900"
                                required
                            />
                        </div>
                        <div class="ml-3 text-sm leading-6">
                            <label for="terms" class="text-slate-500 dark:text-slate-400">
                                I agree to the
                                <a href="#" class="font-medium text-primary hover:text-primary/80 transition-colors">Terms of Service</a>
                                and
                                <a href="#" class="font-medium text-primary hover:text-primary/80 transition-colors">Privacy Policy</a>
                            </label>
                        </div>
                    </div>
                    <div v-if="form.errors.terms" class="text-sm text-red-600 dark:text-red-400 ml-7">
                        {{ form.errors.terms }}
                    </div>
                </div>

                <!-- Submit Button -->
                <SubmitButton :processing="form.processing">
                    Create Account
                </SubmitButton>
            </form>

            <!-- Login Link -->
            <p class="mt-8 text-center text-sm text-slate-500 dark:text-slate-400">
                Already have an account?
                <Link :href="route('login')" class="font-semibold leading-6 text-primary hover:text-primary/80 transition-colors">
                    Log in
                </Link>
            </p>
        </div>
    </AuthLayout>
</template>
