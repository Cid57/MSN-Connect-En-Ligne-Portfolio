<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});
</script>

<template>
    <Head title="MSN Connect - Messagerie instantanée" />

    <div class="min-h-screen bg-gradient-to-br from-blue-600 via-purple-600 to-pink-500 flex items-center justify-center p-4">
        <!-- Navigation -->
        <nav v-if="canLogin" class="fixed top-0 right-0 p-6 z-10">
            <Link
                v-if="canLogin"
                :href="route('login')"
                class="px-6 py-3 bg-white text-blue-600 rounded-full font-semibold hover:bg-blue-50 transition-all duration-200 shadow-lg mr-3"
            >
                Connexion
            </Link>

            <Link
                v-if="canRegister"
                :href="route('register')"
                class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-full font-semibold hover:from-blue-600 hover:to-purple-600 transition-all duration-200 shadow-lg"
            >
                Inscription
            </Link>
        </nav>

        <!-- Contenu principal -->
        <div class="max-w-6xl mx-auto text-center">
            <!-- Logo et titre -->
            <div class="mb-12 animate-fade-in">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-white rounded-3xl shadow-2xl mb-6 transform hover:scale-110 transition-transform duration-300">
                    <svg class="w-14 h-14 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>

                <h1 class="text-6xl md:text-7xl font-bold text-white mb-4 drop-shadow-lg">
                    MSN Connect
                </h1>

                <p class="text-2xl md:text-3xl text-white/90 font-light mb-8">
                    La messagerie instantanée moderne
                </p>
            </div>

            <!-- Fonctionnalités -->
            <div class="grid md:grid-cols-3 gap-8 mb-12">
                <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 hover:bg-white/20 transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">Ultra rapide</h3>
                    <p class="text-white/80">Messages instantanés en temps réel</p>
                </div>

                <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 hover:bg-white/20 transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">Sécurisé</h3>
                    <p class="text-white/80">Vos conversations sont protégées</p>
                </div>

                <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 hover:bg-white/20 transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">Collaboratif</h3>
                    <p class="text-white/80">Groupes et conversations privées</p>
                </div>
            </div>

            <!-- Call to action -->
            <div class="space-y-4">
                <Link
                    v-if="canLogin"
                    :href="route('login')"
                    class="inline-block px-12 py-4 bg-white text-blue-600 rounded-full text-lg font-bold hover:bg-blue-50 transition-all duration-200 shadow-2xl transform hover:scale-105"
                >
                    Commencer à discuter →
                </Link>

                <p class="text-white/80 text-sm">
                    Rejoignez des milliers d'utilisateurs déjà connectés
                </p>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-3 gap-8 mt-16 pt-16 border-t border-white/20">
                <div>
                    <div class="text-4xl font-bold text-white mb-2">10k+</div>
                    <div class="text-white/80">Utilisateurs actifs</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-white mb-2">50k+</div>
                    <div class="text-white/80">Messages envoyés</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-white mb-2">99.9%</div>
                    <div class="text-white/80">Disponibilité</div>
                </div>
            </div>
        </div>

        <!-- Animations de bulles flottantes -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-white/5 rounded-full blur-3xl animate-float"></div>
            <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl animate-float-delayed"></div>
            <div class="absolute top-1/2 right-1/3 w-48 h-48 bg-pink-500/10 rounded-full blur-3xl animate-float-slow"></div>
        </div>
    </div>
</template>

<style scoped>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes float {
    0%, 100% {
        transform: translateY(0) translateX(0);
    }
    50% {
        transform: translateY(-20px) translateX(10px);
    }
}

@keyframes float-delayed {
    0%, 100% {
        transform: translateY(0) translateX(0);
    }
    50% {
        transform: translateY(20px) translateX(-15px);
    }
}

@keyframes float-slow {
    0%, 100% {
        transform: translateY(0) translateX(0);
    }
    50% {
        transform: translateY(-30px) translateX(20px);
    }
}

.animate-fade-in {
    animation: fade-in 1s ease-out;
}

.animate-float {
    animation: float 8s ease-in-out infinite;
}

.animate-float-delayed {
    animation: float-delayed 10s ease-in-out infinite;
}

.animate-float-slow {
    animation: float-slow 12s ease-in-out infinite;
}
</style>
