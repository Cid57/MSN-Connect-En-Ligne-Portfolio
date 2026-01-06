<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

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
    if (confirm(`Changer le rôle de ${user.name} en ${newRole} ?`)) {
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
    if (confirm(`ATTENTION: Supprimer définitivement ${user.name} ? Cette action est irréversible.`)) {
        router.delete(route('admin.users.destroy', user.id), {
            preserveScroll: true,
        });
    }
};

const getRoleBadgeColor = (role) => {
    const colors = {
        admin: 'bg-red-100 text-red-800',
        moderator: 'bg-purple-100 text-purple-800',
        user: 'bg-blue-100 text-blue-800',
    };
    return colors[role] || 'bg-gray-100 text-gray-800';
};

const getRoleLabel = (role) => {
    const labels = {
        admin: 'Administrateur',
        moderator: 'Modérateur',
        user: 'Utilisateur',
    };
    return labels[role] || role;
};
</script>

<template>
    <Head title="Gestion des utilisateurs - Admin" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Gestion des utilisateurs
                </h2>
                <Link
                    :href="route('dashboard')"
                    class="text-sm text-gray-600 hover:text-gray-900"
                >
                    ← Retour au Dashboard
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Statistiques -->
                <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                    <div class="rounded-lg bg-white p-4 shadow">
                        <div class="text-sm text-gray-500">Total</div>
                        <div class="text-2xl font-bold text-gray-900">{{ stats.total }}</div>
                    </div>
                    <div class="rounded-lg bg-white p-4 shadow">
                        <div class="text-sm text-gray-500">Admins</div>
                        <div class="text-2xl font-bold text-red-600">{{ stats.admins }}</div>
                    </div>
                    <div class="rounded-lg bg-white p-4 shadow">
                        <div class="text-sm text-gray-500">Modérateurs</div>
                        <div class="text-2xl font-bold text-purple-600">{{ stats.moderators }}</div>
                    </div>
                    <div class="rounded-lg bg-white p-4 shadow">
                        <div class="text-sm text-gray-500">Utilisateurs</div>
                        <div class="text-2xl font-bold text-blue-600">{{ stats.users }}</div>
                    </div>
                    <div class="rounded-lg bg-white p-4 shadow">
                        <div class="text-sm text-gray-500">Bannis</div>
                        <div class="text-2xl font-bold text-orange-600">{{ stats.banned }}</div>
                    </div>
                    <div class="rounded-lg bg-white p-4 shadow">
                        <div class="text-sm text-gray-500">En ligne</div>
                        <div class="text-2xl font-bold text-green-600">{{ stats.online }}</div>
                    </div>
                </div>

                <!-- Filtres et recherche -->
                <div class="mb-6 rounded-lg bg-white p-6 shadow">
                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Recherche</label>
                            <input
                                v-model="search"
                                @input="performSearch"
                                type="text"
                                placeholder="Nom ou email..."
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            />
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Rôle</label>
                            <select
                                v-model="roleFilter"
                                @change="performSearch"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="all">Tous les rôles</option>
                                <option value="admin">Administrateurs</option>
                                <option value="moderator">Modérateurs</option>
                                <option value="user">Utilisateurs</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">Statut</label>
                            <select
                                v-model="statusFilter"
                                @change="performSearch"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
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
                                        class="rounded-full px-3 py-1 text-xs font-semibold border-0"
                                    >
                                        <option value="user">Utilisateur</option>
                                        <option value="moderator">Modérateur</option>
                                        <option value="admin">Administrateur</option>
                                    </select>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                    {{ user.messages_count || 0 }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span v-if="user.is_banned" class="inline-flex rounded-full bg-red-100 px-2 text-xs font-semibold leading-5 text-red-800">
                                        Banni
                                    </span>
                                    <span v-else-if="user.is_active" class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">
                                        Actif
                                    </span>
                                    <span v-else class="inline-flex rounded-full bg-gray-100 px-2 text-xs font-semibold leading-5 text-gray-800">
                                        Inactif
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button
                                            @click="toggleBan(user)"
                                            :class="user.is_banned ? 'text-green-600 hover:text-green-900' : 'text-orange-600 hover:text-orange-900'"
                                            class="font-medium"
                                        >
                                            {{ user.is_banned ? 'Débannir' : 'Bannir' }}
                                        </button>
                                        <button
                                            @click="deleteUser(user)"
                                            class="font-medium text-red-600 hover:text-red-900"
                                        >
                                            Supprimer
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="users.links.length > 3" class="mt-6 flex justify-center">
                    <nav class="inline-flex rounded-md shadow-sm">
                        <Link
                            v-for="(link, index) in users.links"
                            :key="index"
                            :href="link.url"
                            :class="{
                                'bg-blue-500 text-white': link.active,
                                'bg-white text-gray-700 hover:bg-gray-50': !link.active,
                                'cursor-not-allowed opacity-50': !link.url,
                                'rounded-l-md': index === 0,
                                'rounded-r-md': index === users.links.length - 1,
                            }"
                            class="relative inline-flex items-center border border-gray-300 px-4 py-2 text-sm font-medium"
                            v-html="link.label"
                        />
                    </nav>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
