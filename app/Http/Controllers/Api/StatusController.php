<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatusController extends Controller
{
    /**
     * Liste tous les statuts disponibles
     */
    public function index()
    {
        $statuses = Status::available()->ordered()->get();

        return response()->json($statuses);
    }

    /**
     * Met à jour le statut de l'utilisateur connecté
     */
    public function updateUserStatus(Request $request)
    {
        $validated = $request->validate([
            'status_id' => 'required|exists:statuses,id',
            'status_message' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $user->update([
            'status_id' => $validated['status_id'],
            'status_message' => $validated['status_message'] ?? null,
            'last_seen_at' => now(),
        ]);

        return response()->json($user->load('status'));
    }

    /**
     * Met à jour le last_seen_at de l'utilisateur (heartbeat)
     */
    public function heartbeat()
    {
        $user = Auth::user();
        $user->update(['last_seen_at' => now()]);

        return response()->json([
            'message' => 'Heartbeat enregistré',
            'last_seen_at' => $user->last_seen_at,
        ]);
    }

    /**
     * Récupère les utilisateurs en ligne
     */
    public function onlineUsers()
    {
        $users = \App\Models\User::where('last_seen_at', '>=', now()->subMinutes(5))
            ->where('id', '!=', Auth::id())
            ->with('status')
            ->select('id', 'name', 'avatar', 'status_id', 'status_message', 'last_seen_at')
            ->get();

        return response()->json($users);
    }
}
