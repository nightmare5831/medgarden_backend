<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Events\MessageUpdated;

class MessageController extends Controller
{
    public function index()
    {
        $messages = Message::with(['user', 'comments.user'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'owner' => $message->user->email,
                    'ownerName' => $message->user->name,
                    'ownerRole' => $message->user->role,
                    'content' => $message->content,
                    'timestamp' => $message->created_at->diffForHumans(),
                    'comments' => $message->comments->map(function ($comment) {
                        return [
                            'id' => $comment->id,
                            'owner' => $comment->user->email,
                            'ownerName' => $comment->user->name,
                            'ownerRole' => $comment->user->role,
                            'content' => $comment->content,
                            'timestamp' => $comment->created_at->diffForHumans(),
                        ];
                    }),
                    'favorite' => $message->favorite ?? [],
                    'good' => $message->good ?? [],
                    'bad' => $message->bad ?? [],
                ];
            });

        return response()->json($messages);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'content' => 'required|string|max:1000',
            ]);

            $message = Message::create([
                'user_id' => $request->user()->id,
                'content' => $request->content,
                'favorite' => [],
                'good' => [],
                'bad' => [],
            ]);

            $message->load('user');

            // Broadcast message created
            broadcast(new MessageUpdated('created', $message->id));

            return response()->json([
                'id' => $message->id,
                'owner' => $message->user->email,
                'ownerName' => $message->user->name,
                'ownerRole' => $message->user->role,
                'content' => $message->content,
                'timestamp' => $message->created_at->diffForHumans(),
                'comments' => [],
                'favorite' => $message->favorite ?? [],
                'good' => $message->good ?? [],
                'bad' => $message->bad ?? [],
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Failed to create message: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => $request->user()->id ?? null,
            ]);

            return response()->json([
                'error' => 'Failed to create message',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function addComment(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $message = Message::findOrFail($id);

        $comment = Comment::create([
            'user_id' => $request->user()->id,
            'message_id' => $message->id,
            'content' => $request->content,
        ]);

        // Broadcast comment added
        broadcast(new MessageUpdated('commented', $message->id));

        return response()->json([
            'id' => $comment->id,
            'owner' => $comment->user->email,
            'ownerName' => $comment->user->name,
            'ownerRole' => $comment->user->role,
            'content' => $comment->content,
            'timestamp' => $comment->created_at->diffForHumans(),
        ], 201);
    }

    public function toggleInteraction(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:favorite,good,bad',
        ]);

        $message = Message::findOrFail($id);
        $userEmail = $request->user()->email;
        $type = $request->type;

        $interactions = $message->{$type} ?? [];

        if (in_array($userEmail, $interactions)) {
            $interactions = array_values(array_filter($interactions, fn($email) => $email !== $userEmail));
        } else {
            $interactions[] = $userEmail;

            // Rule: User can only have one action between 'good' or 'bad'
            if ($type === 'good') {
                $bad = $message->bad ?? [];
                $bad = array_values(array_filter($bad, fn($email) => $email !== $userEmail));
                $message->bad = $bad;
            } elseif ($type === 'bad') {
                $good = $message->good ?? [];
                $good = array_values(array_filter($good, fn($email) => $email !== $userEmail));
                $message->good = $good;
            }
        }

        $message->{$type} = $interactions;
        $message->save();

        // Broadcast interaction updated
        broadcast(new MessageUpdated('interacted', $message->id));

        return response()->json([
            'favorite' => $message->favorite ?? [],
            'good' => $message->good ?? [],
            'bad' => $message->bad ?? [],
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $message = Message::findOrFail($id);

        // Only allow owner to delete
        if ($message->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->delete();

        // Broadcast message deleted
        broadcast(new MessageUpdated('deleted', $id));

        return response()->json(['message' => 'Message deleted successfully']);
    }

    public function destroyMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer',
        ]);

        // Only delete messages owned by the user
        $deleted = Message::whereIn('id', $request->ids)
            ->where('user_id', $request->user()->id)
            ->delete();

        // Broadcast multiple messages deleted
        foreach ($request->ids as $id) {
            broadcast(new MessageUpdated('deleted', $id));
        }

        return response()->json([
            'message' => 'Messages deleted successfully',
            'deleted' => $deleted,
        ]);
    }
}
