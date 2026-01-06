<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserManagementController extends Controller
{
    /**
     * Liste tous les utilisateurs
     */
    public function index(Request $request)
    {
        $query = User::with('status')->withCount('messages');

        // Filtres
        if ($request->has('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        if ($request->has('status')) {
            if ($request->status === 'banned') {
                $query->where('is_banned', true);
            } elseif ($request->status === 'active') {
                $query->where('is_banned', false)->where('is_active', true);
            }
        }

        // Recherche
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['role', 'status', 'search']),
            'stats' => [
                'total' => User::count(),
                'admins' => User::where('role', 'admin')->count(),
                'moderators' => User::where('role', 'moderator')->count(),
                'users' => User::where('role', 'user')->count(),
                'banned' => User::where('is_banned', true)->count(),
                'online' => User::where('last_seen_at', '>=', now()->subMinutes(5))->count(),
            ],
        ]);
    }

    /**
     * Change le rôle d'un utilisateur
     */
    public function changeRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|in:user,moderator,admin',
        ]);

        // Ne pas permettre de se retirer le rôle admin à soi-même
        if ($user->id === auth()->id() && $validated['role'] !== 'admin') {
            return back()->withErrors(['role' => 'Vous ne pouvez pas retirer votre propre rôle admin']);
        }

        $user->update(['role' => $validated['role']]);

        return back()->with('success', 'Rôle mis à jour avec succès');
    }

    /**
     * Bannir/débannir un utilisateur
     */
    public function toggleBan(Request $request, User $user)
    {
        // Ne pas permettre de se bannir soi-même
        if ($user->id === auth()->id()) {
            return back()->withErrors(['ban' => 'Vous ne pouvez pas vous bannir vous-même']);
        }

        if ($user->is_banned) {
            $user->update([
                'is_banned' => false,
                'banned_at' => null,
                'ban_reason' => null,
            ]);
            $message = 'Utilisateur débanni avec succès';
        } else {
            $validated = $request->validate([
                'reason' => 'nullable|string|max:500',
            ]);

            $user->update([
                'is_banned' => true,
                'banned_at' => now(),
                'ban_reason' => $validated['reason'] ?? 'Aucune raison spécifiée',
            ]);
            $message = 'Utilisateur banni avec succès';
        }

        return back()->with('success', $message);
    }

    /**
     * Supprimer un utilisateur
     */
    public function destroy(User $user)
    {
        // Ne pas permettre de se supprimer soi-même
        if ($user->id === auth()->id()) {
            return back()->withErrors(['delete' => 'Vous ne pouvez pas supprimer votre propre compte']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur supprimé avec succès');
    }

    /**
     * Activer/désactiver un utilisateur
     */
    public function toggleActive(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        $message = $user->is_active ? 'Utilisateur activé' : 'Utilisateur désactivé';

        return back()->with('success', $message);
    }
}
