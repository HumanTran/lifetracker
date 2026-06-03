<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagController
{
    private function tags()
    {
        return Tag::where('user_id', Auth::id());
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
        ]);

        $name = trim($validated['name']);

        if ($name === '') {
            return response()->json(['success' => false], 422);
        }

        $tag = $this->tags()->where('id', $id)->firstOrFail();

        $existing = $this->tags()
            ->where('name', $name)
            ->where('id', '!=', $tag->id)
            ->first();

        if ($existing) {
            $tag->tasks()->update(['tag_id' => $existing->id]);

            $oldName = $tag->name;
            $tag->delete();

            return response()->json([
                'success' => true,
                'tag' => $existing,
                'old_name' => $oldName,
                'merged' => true,
            ]);
        }

        $oldName = $tag->name;
        $tag->update(['name' => $name]);

        return response()->json([
            'success' => true,
            'tag' => $tag,
            'old_name' => $oldName,
            'merged' => false,
        ]);
    }

    public function destroy(int $id)
    {
        $tag = $this->tags()->where('id', $id)->firstOrFail();

        $tagName = $tag->name;
        $tag->delete();

        return response()->json([
            'success' => true,
            'tag' => [
                'id' => $id,
                'name' => $tagName,
            ],
        ]);
    }
}
