<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    /**
     * Liste les messages d'un channel
     */
    public function index(Request $request, string $channelId)
    {
        $channel = Channel::findOrFail($channelId);

        // Vérifier que l'utilisateur est membre du channel
        if (!$channel->hasMember(Auth::user())) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $messages = $channel->messages()
            ->with('user:id,name,avatar,status_id')
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 50));

        return response()->json($messages);
    }

    /**
     * Envoie un nouveau message dans un channel
     */
    public function store(Request $request, string $channelId)
    {
        $channel = Channel::findOrFail($channelId);

        // Vérifier que l'utilisateur est membre du channel
        if (!$channel->hasMember(Auth::user())) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $validated = $request->validate([
            'content' => 'required_without:attachment|string|max:5000',
            'attachment' => 'nullable|file|max:10240', // 10MB max
        ]);

        $attachmentPath = null;

        // Gérer l'upload de fichier si présent
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('attachments', 'public');
        }

        $message = Message::create([
            'channel_id' => $channel->id,
            'user_id' => Auth::id(),
            'content' => $validated['content'] ?? null,
            'attachment' => $attachmentPath,
            'is_read' => false,
        ]);

        return response()->json(
            $message->load('user:id,name,avatar,status_id'),
            201
        );
    }

    /**
     * Affiche un message spécifique
     */
    public function show(string $channelId, string $id)
    {
        $message = Message::with('user', 'channel')
            ->where('channel_id', $channelId)
            ->findOrFail($id);

        // Vérifier que l'utilisateur est membre du channel
        if (!$message->channel->hasMember(Auth::user())) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        return response()->json($message);
    }

    /**
     * Met à jour un message
     */
    public function update(Request $request, string $channelId, string $id)
    {
        $message = Message::where('channel_id', $channelId)->findOrFail($id);

        // Seul l'auteur peut modifier son message
        if ($message->user_id !== Auth::id()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        $message->update($validated);

        return response()->json($message->load('user:id,name,avatar,status_id'));
    }

    /**
     * Supprime un message (soft delete)
     */
    public function destroy(string $channelId, string $id)
    {
        $message = Message::where('channel_id', $channelId)->findOrFail($id);

        // Seul l'auteur ou un admin du channel peut supprimer le message
        if ($message->user_id !== Auth::id() && !$message->channel->isAdmin(Auth::user())) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        // Supprimer le fichier attaché si présent
        if ($message->attachment) {
            Storage::disk('public')->delete($message->attachment);
        }

        $message->delete();

        return response()->json(['message' => 'Message supprimé avec succès'], 200);
    }

    /**
     * Marque les messages comme lus
     */
    public function markAsRead(Request $request, string $channelId)
    {
        $channel = Channel::findOrFail($channelId);

        // Vérifier que l'utilisateur est membre du channel
        if (!$channel->hasMember(Auth::user())) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        // Mettre à jour le pivot last_read_at
        Auth::user()->channels()->updateExistingPivot($channel->id, [
            'last_read_at' => now(),
        ]);

        return response()->json(['message' => 'Messages marqués comme lus'], 200);
    }

    /**
     * Récupère le nombre de messages non lus par channel
     */
    public function unreadCount()
    {
        $user = Auth::user();
        $channels = $user->channels()->get();

        $unreadCounts = [];

        foreach ($channels as $channel) {
            $lastReadAt = $channel->pivot->last_read_at;

            $unreadCount = Message::where('channel_id', $channel->id)
                ->where('user_id', '!=', $user->id) // Exclure ses propres messages
                ->when($lastReadAt, function ($query) use ($lastReadAt) {
                    $query->where('created_at', '>', $lastReadAt);
                })
                ->count();

            $unreadCounts[$channel->id] = $unreadCount;
        }

        return response()->json($unreadCounts);
    }
}
