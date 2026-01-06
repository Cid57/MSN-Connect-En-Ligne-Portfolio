<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    users: Object,
    filters: Object,
    stats: Object,
});

const search = ref(props.filters.search || '');
const roleFilter = ref(props.filters.role || 'all');
const statusFilter = ref(props.filters.status || 'all');

const performSearch = () => {
    router.get(route('admin.users.index'), {
        search: search.value,
        role: roleFilter.value,
        status: statusFilter.value,
    }, {
        preserveState: true,
        replace: true,
    });
};

const changeRole = (user, newRole) => {
    if (newRole === user.role) return; // Pas de changement

    const labels = {
        admin: 'Administrateur',
        user: 'Utilisateur',
    };

    if (confirm(`Changer le rôle de ${user.name} en ${labels[newRole]} ?`)) {
        router.post(route('admin.users.role', user.id), {
            role: newRole,
        }, {
            preserveScroll: true,
        });
    }
};

const toggleBan = (user) => {
    if (user.is_banned) {
        if (confirm(`Débannir ${user.name} ?`)) {
            router.post(route('admin.users.ban', user.id), {}, {
                preserveScroll: true,
            });
        }
    } else {
        const reason = prompt(`Raison du bannissement de ${user.name} :`, '');
        if (reason !== null) {
            router.post(route('admin.users.ban', user.id), {
                reason: reason,
            }, {
                preserveScroll: true,
            });
        }
    }
};

const deleteUser = (user) => {
    if (confirm(`ATTENTION: Supprimer définitivement ${user.name} ?\n\nCette action est irréversible et supprimera:\n- Le compte utilisateur\n- Tous ses messages\n- Son historique\n\nConfirmer ?`)) {
        router.delete(route('admin.users.destroy', user.id));
    }
};

const getRoleBadgeColor = (role) => {
    const colors = {
        admin: 'bg-red-100 text-red-800',
        user: 'bg-blue-100 text-blue-800',
    };
    return colors[role] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head title="Gestion des utilisateurs - Admin" />

    <div class="min-h-screen bg-gray-100">
        <!-- Header -->
        <div class="bg-white shadow">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-900">
                        📊 Gestion des utilisateurs
                    </h1>
                    <Link
                        :href="route('dashboard')"
                        class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                    >
                        ← Retour au Dashboard
                    </Link>
                </div>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Statistiques -->
            <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
                <div class="rounded-lg bg-white p-6 shadow">
                    <div class="text-sm font-medium text-gray-500">Total</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ stats.total }}</div>
                </div>
                <div class="rounded-lg bg-white p-6 shadow">
                    <div class="text-sm font-medium text-gray-500">Admins</div>
                    <div class="mt-2 text-3xl font-bold text-red-600">{{ stats.admins }}</div>
                </div>
                <div class="rounded-lg bg-white p-6 shadow">
                    <div class="text-sm font-medium text-gray-500">Utilisateurs</div>
                    <div class="mt-2 text-3xl font-bold text-blue-600">{{ stats.users }}</div>
                </div>
                <div class="rounded-lg bg-white p-6 shadow">
                    <div class="text-sm font-medium text-gray-500">Bannis</div>
                    <div class="mt-2 text-3xl font-bold text-orange-600">{{ stats.banned }}</div>
                </div>
                <div class="rounded-lg bg-white p-6 shadow">
                    <div class="text-sm font-medium text-gray-500">En ligne</div>
                    <div class="mt-2 text-3xl font-bold text-green-600">{{ stats.online }}</div>
                </div>
            </div>

            <!-- Filtres et recherche -->
            <div class="mb-6 rounded-lg bg-white p-6 shadow">
                <div class="grid gap-4 md:grid-cols-3">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">🔍 Recherche</label>
                        <input
                            v-model="search"
                            @input="performSearch"
                            type="text"
                            placeholder="Nom ou email..."
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">👥 Rôle</label>
                        <select
                            v-model="roleFilter"
                            @change="performSearch"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="all">Tous les rôles</option>
                            <option value="admin">Administrateurs</option>
                            <option value="user">Utilisateurs</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">📌 Statut</label>
                        <select
                            v-model="statusFilter"
                            @change="performSearch"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="all">Tous les statuts</option>
                            <option value="active">Actifs</option>
                            <option value="banned">Bannis</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Liste des utilisateurs -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                Utilisateur
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                Email
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                Rôle
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                Messages
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                Statut
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="user in users.data" :key="user.id" :class="{ 'bg-red-50': user.is_banned }">
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-r from-blue-500 to-purple-500 text-white font-semibold">
                                            {{ user.name.charAt(0).toUpperCase() }}
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                        <div class="text-sm text-gray-500">ID: {{ user.id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <div class="text-sm text-gray-900">{{ user.email }}</div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <select
                                    :value="user.role"
                                    @change="changeRole(user, $event.target.value)"
                                    :class="getRoleBadgeColor(user.role)"
                                    class="cursor-pointer rounded-full px-3 py-1 text-xs font-semibold border-0 focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="user">Utilisateur</option>
                                    <option value="admin">Administrateur</option>
                                </select>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                {{ user.messages_count || 0 }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span v-if="user.is_banned" class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-800">
                                    🚫 Banni
                                </span>
                                <span v-else-if="user.is_active" class="inline-flex rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">
                                    ✅ Actif
                                </span>
                                <span v-else class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-800">
                                    ⏸️ Inactif
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-3">
                                    <button
                                        @click="toggleBan(user)"
                                        :class="user.is_banned ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-orange-100 text-orange-700 hover:bg-orange-200'"
                                        class="rounded-lg px-3 py-1.5 font-medium transition-colors"
                                    >
                                        {{ user.is_banned ? '✓ Débannir' : '🚫 Bannir' }}
                                    </button>
                                    <button
                                        @click="deleteUser(user)"
                                        class="rounded-lg bg-red-100 px-3 py-1.5 font-medium text-red-700 hover:bg-red-200 transition-colors"
                                    >
                                        🗑️ Supprimer
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="users.links.length > 3" class="mt-6 flex justify-center">
                <nav class="inline-flex rounded-md shadow-sm -space-x-px">
                    <component
                        :is="link.url ? Link : 'span'"
                        v-for="(link, index) in users.links"
                        :key="index"
                        :href="link.url"
                        :class="{
                            'bg-blue-600 text-white border-blue-600': link.active,
                            'bg-white text-gray-700 hover:bg-gray-50 border-gray-300': !link.active && link.url,
                            'bg-gray-100 text-gray-400 cursor-not-allowed border-gray-300': !link.url,
                            'rounded-l-md': index === 0,
                            'rounded-r-md': index === users.links.length - 1,
                        }"
                        class="relative inline-flex items-center border px-4 py-2 text-sm font-medium"
                        v-html="link.label"
                    />
                </nav>
            </div>
        </div>
    </div>
</template>
