<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import TextInput from '@/Components/Forms/TextInput.vue';
import SubmitButton from '@/Components/Forms/SubmitButton.vue';

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <AuthLayout>
        <Head title="Forgot Password" />

        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-slate-900 dark:text-white">
                Reset your password
            </h2>
            <p class="mt-2 text-center text-sm text-slate-500 dark:text-slate-400">
                Enter your email and we'll send you a verification code
            </p>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
            <div class="bg-white px-6 py-12 shadow sm:rounded-lg sm:px-12 dark:bg-[#1e293b] border border-slate-200 dark:border-slate-800">
                <div v-if="$page.props.status" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                    {{ $page.props.status }}
                </div>

                <form class="space-y-6" @submit.prevent="submit">
                    <div>
                        <label for="email" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">
                            Email address
                        </label>
                        <TextInput 
                            v-model="form.email"
                            id="email" 
                            type="email" 
                            autocomplete="email" 
                            required 
                            :error="form.errors.email"
                            class="mt-2"
                        />
                    </div>

                    <div>
                        <SubmitButton :processing="form.processing">
                            Send verification code
                        </SubmitButton>
                    </div>
                </form>

                <p class="mt-10 text-center text-sm text-slate-500 dark:text-slate-400">
                    Remember your password?
                    <Link :href="route('login')" class="font-semibold leading-6 text-primary hover:text-primary/80 transition-colors">
                        Sign in
                    </Link>
                </p>
            </div>
        </div>
    </AuthLayout>
</template>
