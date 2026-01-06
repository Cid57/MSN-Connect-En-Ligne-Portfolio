<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

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
</script>

<template>
    <Head title="Connexion - MSN Connect" />

    <div class="min-h-screen bg-gradient-to-br from-blue-600 via-purple-600 to-pink-500 flex items-center justify-center p-4">
        <!-- Carte de connexion -->
        <div class="w-full max-w-md">
            <!-- Logo et titre -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-3xl shadow-2xl mb-4">
                    <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-white mb-2">MSN Connect</h1>
                <p class="text-white/90">Connectez-vous à votre compte</p>
            </div>

            <!-- Formulaire -->
            <div class="bg-white/95 backdrop-blur-lg rounded-3xl shadow-2xl p-8">
                <div v-if="status" class="mb-4 text-sm font-medium text-green-600 bg-green-50 rounded-lg p-3">
                    {{ status }}
                </div>

                <form @submit.prevent="submit">
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            Email
                        </label>

                        <input
                            id="email"
                            type="email"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                        />

                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="mt-5">
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                            Mot de passe
                        </label>

                        <input
                            id="password"
                            type="password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                        />

                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="mt-5 flex items-center justify-between">
                        <label class="flex items-center">
                            <Checkbox name="remember" v-model:checked="form.remember" />
                            <span class="ms-2 text-sm text-gray-600">Se souvenir de moi</span>
                        </label>

                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-sm text-blue-600 hover:text-blue-700 font-medium"
                        >
                            Mot de passe oublié ?
                        </Link>
                    </div>

                    <button
                        type="submit"
                        :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                        :disabled="form.processing"
                        class="w-full mt-6 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-xl font-semibold hover:from-blue-600 hover:to-purple-600 transition-all duration-200 shadow-lg"
                    >
                        Se connecter
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Pas encore de compte ?
                        <Link
                            :href="route('register')"
                            class="text-blue-600 hover:text-blue-700 font-semibold"
                        >
                            Inscrivez-vous
                        </Link>
                    </p>
                </div>
            </div>

            <!-- Retour à l'accueil -->
            <div class="mt-6 text-center">
                <Link
                    href="/"
                    class="text-white/90 hover:text-white text-sm font-medium inline-flex items-center"
                >
                    ← Retour à l'accueil
                </Link>
            </div>
        </div>
    </div>
</template>
