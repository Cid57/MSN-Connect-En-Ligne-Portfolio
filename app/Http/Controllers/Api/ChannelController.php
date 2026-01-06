<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ChannelController extends Controller
{
    /**
     * Liste tous les channels de l'utilisateur connecté
     */
    public function index()
    {
        $channels = Auth::user()
            ->channels()
            ->with(['lastMessage.user', 'members' => function ($query) {
                $query->select('users.id', 'users.name', 'users.avatar', 'users.status_id', 'users.last_seen_at');
            }, 'members.status'])
            ->recentActivity()
            ->get();

        return response()->json($channels);
    }

    /**
     * Crée un nouveau channel (conversation privée ou groupe)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_group' => 'required|boolean',
            'member_ids' => 'required|array|min:1',
            'member_ids.*' => 'exists:users,id',
        ]);

        // Pour une conversation privée, vérifier qu'il y a exactement 1 autre membre
        if (!$validated['is_group'] && count($validated['member_ids']) !== 1) {
            return response()->json([
                'message' => 'Une conversation privée doit avoir exactement 1 autre membre'
            ], 422);
        }

        // Pour une conversation privée, vérifier si elle existe déjà
        if (!$validated['is_group']) {
            $existingChannel = Auth::user()->channels()
                ->private()
                ->whereHas('members', function ($query) use ($validated) {
                    $query->where('user_id', $validated['member_ids'][0]);
                })
                ->first();

            if ($existingChannel) {
                return response()->json($existingChannel->load(['lastMessage.user', 'members.status']), 200);
            }
        }

        // Créer le channel
        $channel = Channel::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_group' => $validated['is_group'],
            'is_active' => true,
            'created_by' => Auth::id(),
        ]);

        // Ajouter les membres (y compris l'utilisateur connecté)
        $members = array_unique(array_merge($validated['member_ids'], [Auth::id()]));

        foreach ($members as $userId) {
            $channel->members()->attach($userId, [
                'is_admin' => $userId === Auth::id(), // Le créateur est admin
                'joined_at' => now(),
            ]);
        }

        return response()->json(
            $channel->load(['lastMessage.user', 'members.status']),
            201
        );
    }

    /**
     * Affiche un channel spécifique
     */
    public function show(string $id)
    {
        $channel = Channel::with(['messages.user', 'members.status', 'creator'])
            ->findOrFail($id);

        // Vérifier que l'utilisateur est membre du channel
        if (!$channel->hasMember(Auth::user())) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        // Marquer les messages comme lus
        Auth::user()->channels()->updateExistingPivot($channel->id, [
            'last_read_at' => now(),
        ]);

        return response()->json($channel);
    }

    /**
     * Met à jour un channel (nom, description, etc.)
     */
    public function update(Request $request, string $id)
    {
        $channel = Channel::findOrFail($id);

        // Vérifier que l'utilisateur est admin du channel
        if (!$channel->isAdmin(Auth::user())) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'sometimes|boolean',
        ]);

        $channel->update($validated);

        return response()->json($channel->load(['lastMessage.user', 'members.status']));
    }

    /**
     * Supprime un channel (soft delete)
     */
    public function destroy(string $id)
    {
        $channel = Channel::findOrFail($id);

        // Vérifier que l'utilisateur est admin du channel
        if (!$channel->isAdmin(Auth::user())) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $channel->delete();

        return response()->json(['message' => 'Channel supprimé avec succès'], 200);
    }

    /**
     * Ajoute un membre au channel
     */
    public function addMember(Request $request, string $id)
    {
        $channel = Channel::findOrFail($id);

        // Seuls les groupes peuvent avoir des membres ajoutés
        if (!$channel->is_group) {
            return response()->json(['message' => 'Impossible d\'ajouter des membres à une conversation privée'], 422);
        }

        // Vérifier que l'utilisateur est admin du channel
        if (!$channel->isAdmin(Auth::user())) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Vérifier si le membre existe déjà
        if ($channel->hasMember(User::find($validated['user_id']))) {
            return response()->json(['message' => 'Cet utilisateur est déjà membre du channel'], 422);
        }

        $channel->members()->attach($validated['user_id'], [
            'is_admin' => false,
            'joined_at' => now(),
        ]);

        return response()->json($channel->load('members.status'), 200);
    }

    /**
     * Retire un membre du channel
     */
    public function removeMember(Request $request, string $id)
    {
        $channel = Channel::findOrFail($id);

        // Vérifier que l'utilisateur est admin du channel
        if (!$channel->isAdmin(Auth::user())) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $channel->members()->detach($validated['user_id']);

        return response()->json($channel->load('members.status'), 200);
    }

    /**
     * Quitte un channel
     */
    public function leave(string $id)
    {
        $channel = Channel::findOrFail($id);

        if (!$channel->hasMember(Auth::user())) {
            return response()->json(['message' => 'Vous n\'êtes pas membre de ce channel'], 422);
        }

        $channel->members()->detach(Auth::id());

        return response()->json(['message' => 'Vous avez quitté le channel'], 200);
    }
}
