<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import TextInput from '@/Components/Forms/TextInput.vue';
import PasswordInput from '@/Components/Forms/PasswordInput.vue';
import SubmitButton from '@/Components/Forms/SubmitButton.vue';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

const showSignup = usePage().props.multi_user;
</script>

<template>
    <AuthLayout>
        <Head title="Login" />

        <!-- Header Section -->
        <div class="px-8 pt-10 pb-6 text-center">
            <div class="mx-auto mb-6 flex items-center justify-center">
                <img :src="'/assets/images/logo.png'" alt="Logo" class="h-12 w-auto" />
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Welcome Back</h1>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Please enter your details to sign in.</p>
        </div>

        <!-- Form Section -->
        <div class="px-8 pb-10">
            <div v-if="$page.props.status" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400 text-center bg-green-50 dark:bg-green-900/30 p-3 rounded-lg border border-green-200 dark:border-green-800">
                {{ $page.props.status }}
            </div>

            <form @submit.prevent="submit" class="flex flex-col gap-5">
                <!-- Email Input -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-medium text-slate-900 dark:text-white" for="email">Email Address</label>
                    <div class="relative">
                        <TextInput
                            id="email"
                            v-model="form.email"
                            type="email"
                            placeholder="Enter your email"
                            required
                            autofocus
                        />
                        <div v-if="form.errors.email" class="mt-1 text-sm text-red-600 dark:text-red-400">
                            {{ form.errors.email }}
                        </div>
                    </div>
                </div>

                <!-- Password Input -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-medium text-slate-900 dark:text-white" for="password">Password</label>
                    <PasswordInput
                        id="password"
                        v-model="form.password"
                        placeholder="Enter your password"
                        required
                        :error="form.errors.password"
                    />
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <input
                            id="remember"
                            v-model="form.remember"
                            type="checkbox"
                            class="h-4 w-4 rounded border-slate-300 text-primary focus:ring-primary dark:border-slate-600 dark:bg-slate-700 dark:ring-offset-slate-800"
                        />
                        <label class="text-sm font-medium text-slate-600 dark:text-slate-300 select-none cursor-pointer" for="remember">Remember me</label>
                    </div>
                    <Link :href="route('password.request')" class="text-sm font-semibold text-primary hover:text-blue-600 hover:underline transition-colors">
                        Forgot Password?
                    </Link>
                </div>

                <!-- Login Button -->
                <SubmitButton :processing="form.processing">
                    Sign In
                </SubmitButton>

                <!-- Divider -->
                <div class="relative my-2">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200 dark:border-slate-700"></div>
                    </div>
                    <div class="relative flex justify-center text-xs uppercase">
                        <span class="bg-white dark:bg-[#1a2332] px-2 text-slate-400">Or continue with</span>
                    </div>
                </div>

                <!-- Social Login Buttons -->
                <div class="flex flex-col">
                    <button type="button" class="flex items-center justify-center gap-2 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 py-3 px-4 text-sm font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors focus:outline-none focus:ring-2 focus:ring-slate-200 dark:focus:ring-slate-700 shadow-sm">
                        <svg class="h-5 w-5" viewBox="0 0 24 24">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        Sign in with Google
                    </button>
                </div>

                <!-- Register Link -->
                <p v-if="showSignup" class="text-center text-sm text-slate-600 dark:text-slate-400">
                    Don't have an account? 
                    <Link href="#" class="font-semibold text-primary hover:text-blue-600 hover:underline transition-colors">Sign up</Link>
                </p>
            </form>
        </div>
    </AuthLayout>
</template>
