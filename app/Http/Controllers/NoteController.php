<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NoteController extends Controller
{
    /**
     * Get all notes for the authenticated user
     */
    public function index()//fetch notes
    {
        try {
            // Get the logged-in user
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['error' => 'User not found'], 401);
            }

            // Get user's notes with category information
            $notes = Note::with(['category', 'user'])
                ->where('user_id', $user->id) //fetch notes belong to that user
                ->orderBy('created_at', 'desc')
                ->get();

            // Format the data for frontend
            $formattedNotes = $notes->map(function ($note) {
                return [
                    'id' => $note->id,
                    'title' => $note->title,
                    'content' => $note->content,
                    'category_id' => $note->category_id,
                    'user_name' => $note->user->name ?? 'Unknown',
                    'category_name' => $note->category->name ?? 'General',
                    'created_at' => $note->created_at->format('Y-m-d H:i:s'),
                ];
            });

            return response()->json($formattedNotes);

        } catch (\Exception $e) {
            Log::error('Failed to load notes: ' . $e->getMessage());
            return response()->json(['error' => 'Could not load your notes'], 500);
        }
    }

    /**
     * Create a new note
     */
    public function store(Request $request)
    {
        try {
            // Get the logged-in user
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['error' => 'Please log in to create notes'], 401);
            }

            // Validate the input
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string|max:10000',
                'category_id' => 'nullable|integer|exists:categories,id'
            ]);

            // Create the note
            $note = Note::create([
                'title' => $validatedData['title'],
                'content' => $validatedData['content'],
                'category_id' => $validatedData['category_id'] ?? null,
                'user_id' => $user->id
            ]);

            // Load the note with its relationships
            $noteWithRelations = Note::with(['category', 'user'])->find($note->id);

            return response()->json([
                'message' => 'Note created successfully!',
                'note' => [
                    'id' => $noteWithRelations->id,
                    'title' => $noteWithRelations->title,
                    'content' => $noteWithRelations->content,
                    'category_id' => $noteWithRelations->category_id,
                    'user_name' => $noteWithRelations->user->name,
                    'category_name' => $noteWithRelations->category->name ?? 'General',
                    'created_at' => $noteWithRelations->created_at->format('Y-m-d H:i:s'),
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Please check your input',
                'details' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Note creation failed: ' . $e->getMessage());
            Log::error('Request data: ' . json_encode($request->all()));
            
            return response()->json([
                'error' => 'Failed to create note',
                'message' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }

    /**
     * Show a specific note
     */
    public function show($id)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['error' => 'User not found'], 401);
            }
            
            $note = Note::with(['category', 'user'])
                ->where('user_id', $user->id)
                ->where('id', $id)
                ->first();
                
            if (!$note) {
                return response()->json(['error' => 'Note not found'], 404);
            }
                
            return response()->json($note);

        } catch (\Exception $e) {
            Log::error('Failed to get note: ' . $e->getMessage());
            return response()->json(['error' => 'Could not find that note'], 404);
        }
    }

    /**
     * Update a note
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['error' => 'User not found'], 401);
            }
            
            $note = Note::where('user_id', $user->id)
                ->where('id', $id)
                ->first();
                
            if (!$note) {
                return response()->json(['error' => 'Note not found'], 404);
            }

            // Validate input
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string|max:10000',
                'category_id' => 'nullable|integer|exists:categories,id'
            ]);

            // Update the note
            $note->update($validatedData);

            return response()->json([
                'message' => 'Note updated successfully!',
                'note' => $note
            ]);

        } catch (\Exception $e) {
            Log::error('Note update failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update note'], 500);
        }
    }

    /**
     * Delete a note
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['error' => 'User not found'], 401);
            }
            
            $note = Note::where('user_id', $user->id)
                ->where('id', $id)
                ->first();
                
            if (!$note) {
                return response()->json(['error' => 'Note not found'], 404);
            }
            
            $note->delete();

            return response()->json(['message' => 'Note deleted successfully!']);

        } catch (\Exception $e) {
            Log::error('Note deletion failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete note'], 500);
        }
    }
}