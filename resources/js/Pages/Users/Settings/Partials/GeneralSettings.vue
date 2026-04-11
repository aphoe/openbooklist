<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import SubmitButton from '@/Components/Forms/SubmitButton.vue';
import TextInput from '@/Components/Forms/TextInput.vue';

const page = usePage();
const user = page.props.auth.user;
const languageOptions = page.props.languageOptions || [];

const profileForm = useForm({
    first_name: user?.first_name || '',
    last_name: user?.last_name || '',
    email: user?.email || '',
    language: user?.language || 'en',
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const scrollToTop = () => {
    const scrollContainer = document.querySelector('main .overflow-y-auto');

    if (scrollContainer instanceof HTMLElement) {
        scrollContainer.scrollTo({ top: 0, behavior: 'smooth' });

        return;
    }

    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const updateProfileInformation = () => {
    profileForm.put(route('settings.general'), {
        preserveScroll: true,
        onSuccess: () => scrollToTop(),
    });
};

const updatePassword = () => {
    passwordForm.put(route('settings.password'), {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset();
            scrollToTop();
        },
    });
};
</script>

<template>
    <div class="space-y-8">
        <!-- Profile Form Card -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
            <div class="flex flex-col gap-1 mb-6 border-b border-slate-200 dark:border-slate-800 pb-6">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Profile Information</h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm">Update your account's profile information and
                    email address.</p>
            </div>

            <form @submit.prevent="updateProfileInformation" class="space-y-6 max-w-3xl">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-900 dark:text-white mb-2">First Name</label>
                        <TextInput v-model="profileForm.first_name" type="text" />
                        <span v-if="profileForm.errors.first_name" class="text-xs text-red-500 mt-1">{{
                            profileForm.errors.first_name }}</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-900 dark:text-white mb-2">Last Name</label>
                        <TextInput v-model="profileForm.last_name" type="text" />
                        <span v-if="profileForm.errors.last_name" class="text-xs text-red-500 mt-1">{{
                            profileForm.errors.last_name }}</span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-900 dark:text-white mb-2">Email Address</label>
                    <TextInput v-model="profileForm.email" type="email" />
                    <span v-if="profileForm.errors.email" class="text-xs text-red-500 mt-1">{{ profileForm.errors.email
                    }}</span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-900 dark:text-white mb-2">Preferred Language</label>
                    <select
                        v-model="profileForm.language"
                        class="form-input flex w-full items-center justify-between rounded-lg border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white h-12 px-4 text-base transition-colors"
                    >
                        <option
                            v-for="option in languageOptions"
                            :key="option.id"
                            :value="option.id"
                        >
                            {{ option.name }}
                        </option>
                    </select>
                    <span v-if="profileForm.errors.language" class="text-xs text-red-500 mt-1">{{ profileForm.errors.language }}</span>
                </div>

                <div class="h-px bg-slate-200 dark:bg-slate-800 w-full mt-6"></div>

                <div class="flex justify-end pt-2">
                    <SubmitButton :processing="profileForm.processing" class="!w-auto !px-6">
                        Save Profile
                    </SubmitButton>
                </div>
            </form>
        </div>

        <!-- Password Form Card -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">
            <div class="flex flex-col gap-1 mb-6 border-b border-slate-200 dark:border-slate-800 pb-6">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Change Password</h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm">Ensure your account is using a long, random
                    password to stay secure.
                </p>
            </div>

            <form @submit.prevent="updatePassword" class="space-y-6 max-w-3xl">
                <div>
                    <label class="block text-sm font-medium text-slate-900 dark:text-white mb-2">Current
                        Password</label>
                    <TextInput v-model="passwordForm.current_password" type="password" class="max-w-md" />
                    <span v-if="passwordForm.errors.current_password" class="text-xs text-red-500 mt-1">{{
                        passwordForm.errors.current_password }}</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-900 dark:text-white mb-2">New
                            Password</label>
                        <TextInput v-model="passwordForm.password" type="password" />
                        <span v-if="passwordForm.errors.password" class="text-xs text-red-500 mt-1">{{
                            passwordForm.errors.password }}</span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-900 dark:text-white mb-2">Confirm New
                            Password</label>
                        <TextInput v-model="passwordForm.password_confirmation" type="password" />
                    </div>
                </div>

                <div class="h-px bg-slate-200 dark:bg-slate-800 w-full mt-6"></div>

                <div class="flex justify-end pt-2">
                    <SubmitButton :processing="passwordForm.processing" class="!w-auto !px-6">
                        Update Password
                    </SubmitButton>
                </div>
            </form>
        </div>
    </div>
</template>
