<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import TextInput from '@/Components/Forms/TextInput.vue';
import PasswordInput from '@/Components/Forms/PasswordInput.vue';
import SubmitButton from '@/Components/Forms/SubmitButton.vue';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AuthLayout>
        <Head title="Reset Password" />

        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-slate-900 dark:text-white">
                Set new password
            </h2>
            <p class="mt-2 text-center text-sm text-slate-500 dark:text-slate-400">
                Please enter a new secure password
            </p>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
            <div class="bg-white px-6 py-12 shadow sm:rounded-lg sm:px-12 dark:bg-[#1e293b] border border-slate-200 dark:border-slate-800">
                <form class="space-y-6" @submit.prevent="submit">
                    <div>
                        <label for="email" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">
                            Email Address
                        </label>
                        <TextInput 
                            v-model="form.email"
                            id="email" 
                            type="email" 
                            readonly 
                            class="mt-2 opacity-75 bg-slate-100 dark:bg-slate-700 cursor-not-allowed"
                        />
                        <div v-if="form.errors.email" class="mt-1 text-sm text-red-600 dark:text-red-400">
                            {{ form.errors.email }}
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">
                            New Password
                        </label>
                        <PasswordInput 
                            v-model="form.password"
                            id="password" 
                            required 
                            autofocus
                            class="mt-2"
                            :error="form.errors.password"
                        />
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium leading-6 text-slate-900 dark:text-slate-200">
                            Confirm Password
                        </label>
                        <PasswordInput 
                            v-model="form.password_confirmation"
                            id="password_confirmation" 
                            required 
                            class="mt-2"
                            :error="form.errors.password_confirmation"
                            placeholder="Confirm your password"
                        />
                        <div v-if="form.errors.token" class="mt-1 text-sm text-red-600 dark:text-red-400">
                            {{ form.errors.token }}
                        </div>
                    </div>

                    <div>
                        <SubmitButton :processing="form.processing">
                            Reset Password
                        </SubmitButton>
                    </div>
                </form>

                <p class="mt-10 text-center text-sm text-slate-500 dark:text-slate-400">
                    <Link :href="route('login')" class="hover:text-slate-600 dark:hover:text-slate-300 transition-colors flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined" style="font-size: 16px;">arrow_back</span>
                        Back to login
                    </Link>
                </p>
            </div>
        </div>
    </AuthLayout>
</template>
