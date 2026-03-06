<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import SubmitButton from '@/Components/Forms/SubmitButton.vue';

const props = defineProps({
    email: {
        type: String,
        required: true,
    }
});

const form = useForm({
    email: props.email,
    otp: '',
});

const otpInputs = ref(['', '', '', '', '', '']);
const inputs = ref([]);

const handleInput = (index, event) => {
    const value = event.target.value;
    
    if (value && index < 5) {
        inputs.value[index + 1].focus();
    }
    
    updateFormOtp();
};

const handleKeydown = (index, event) => {
    // Handle backspace
    if (event.key === 'Backspace' && !otpInputs.value[index] && index > 0) {
        inputs.value[index - 1].focus();
    }
};

const handlePaste = (event) => {
    event.preventDefault();
    const pastedData = event.clipboardData.getData('text').slice(0, 6).split('');
    
    pastedData.forEach((char, index) => {
        if (index < 6 && /^\d+$/.test(char)) {
            otpInputs.value[index] = char;
        }
    });
    
    updateFormOtp();
    
    // Focus last filled input or next empty
    const lastFilledIndex = pastedData.length - 1;
    if (lastFilledIndex < 5) {
        inputs.value[lastFilledIndex + 1].focus();
    } else {
        inputs.value[5].focus();
    }
};

const updateFormOtp = () => {
    form.otp = otpInputs.value.join('');
};

const submit = () => {
    form.post(route('password.verify.otp'));
};
</script>

<template>
    <AuthLayout>
        <Head title="Verify OTP" />

        <div class="sm:mx-auto sm:w-full sm:max-w-[480px]">
            <div class="bg-white px-6 py-12 shadow sm:rounded-lg sm:px-12 dark:bg-[#1e293b] border border-slate-200 dark:border-slate-800 text-center">
                
                <div class="flex items-center justify-center mb-6">
                    <div class="p-3 bg-primary/10 rounded-xl">
                        <span class="material-symbols-outlined text-primary text-3xl block">lock_reset</span>
                    </div>
                </div>

                <h1 class="text-slate-900 dark:text-white text-2xl font-bold tracking-tight">Enter Verification Code</h1>
                
                <div class="mt-2 text-slate-500 dark:text-slate-400 text-sm flex flex-col justify-center">
                    <span>We sent a code to <span class="font-medium text-slate-900 dark:text-slate-200">{{ email }}</span></span>
                    <Link :href="route('password.request')" class="text-primary hover:text-primary/80 font-medium transition-colors mt-1">Change email</Link>
                </div>

                <div v-if="form.errors.otp || form.errors.email" class="mt-4 font-medium text-sm text-red-600 dark:text-red-400">
                    {{ form.errors.otp || form.errors.email }}
                </div>

                <form @submit.prevent="submit" class="mt-8 space-y-6">
                    <div>
                        <label class="sr-only" for="otp-0">OTP Code</label>
                        <div class="flex justify-between gap-2 sm:gap-4">
                            <input 
                                v-for="(digit, index) in 6" 
                                :key="index"
                                v-model="otpInputs[index]"
                                :ref="el => { if (el) inputs[index] = el }"
                                @input="handleInput(index, $event)"
                                @keydown="handleKeydown(index, $event)"
                                @paste="handlePaste"
                                :id="`otp-${index}`"
                                type="text"
                                inputmode="numeric"
                                maxlength="1"
                                class="w-full h-12 sm:h-14 text-center text-xl font-bold text-slate-900 bg-white border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary dark:bg-slate-800 dark:border-slate-700 dark:text-white outline-none transition-all"
                                :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': form.errors.otp }"
                                :autofocus="index === 0"
                                required
                            />
                        </div>
                    </div>

                    <div>
                        <SubmitButton 
                            :processing="form.processing"
                            :disabled="form.otp.length !== 6"
                        >
                            Verify Code
                        </SubmitButton>
                    </div>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Didn't receive the code?
                        <Link :href="route('password.request')" method="post" as="button" :data="{ email }" class="font-semibold leading-6 text-primary hover:text-primary/80 transition-colors ml-1">
                            Resend Code
                        </Link>
                    </p>
                    <p class="mt-8 text-sm text-slate-400 dark:text-slate-500">
                        <Link :href="route('login')" class="hover:text-slate-600 dark:hover:text-slate-300 transition-colors flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined" style="font-size: 16px;">arrow_back</span>
                            Back to login
                        </Link>
                    </p>
                </div>

            </div>
        </div>
    </AuthLayout>
</template>
