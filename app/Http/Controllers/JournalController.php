<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class JournalController
{
    private function journalPayload(Journal $journal): array
    {
        $createdAt = Carbon::parse($journal->created_at);

        return [
            'id' => $journal->id,
            'title' => $journal->title,
            'content' => $journal->content,
            'created_date' => $createdAt->format('Y-m-d'),
            'short_date' => $createdAt->format('d/m'),
            'excerpt' => Str::limit(strip_tags($journal->content), 120),
        ];
    }

    private function journals()
    {
        return Journal::where('user_id', Auth::id());
    }

    public function index(Request $request)
    {
        $query = $this->journals();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('content', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $journals = $query->orderBy('created_at', 'desc')
            ->paginate(6)
            ->withQueryString();

        return view('client.journals.index', compact('journals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'created_date' => 'required|date|before_or_equal:today',
        ]);

        $time = Carbon::now()->format('H:i:s');

        $journal = new Journal;
        $journal->user_id = Auth::id();
        $journal->title = $validated['title'];
        $journal->content = $validated['content'];
        $journal->created_at = $validated['created_date'].' '.$time;
        $journal->updated_at = $validated['created_date'].' '.$time;
        $journal->save();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'journal' => $this->journalPayload($journal),
            ]);
        }

        return redirect()->route('journals.index');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'created_date' => 'required|date|before_or_equal:today',
        ]);

        $journal = $this->journals()->where('id', $id)->firstOrFail();

        $time = Carbon::parse($journal->created_at)->format('H:i:s');

        $journal->title = $validated['title'];
        $journal->content = $validated['content'];
        $journal->created_at = $validated['created_date'].' '.$time;
        $journal->updated_at = Carbon::now();
        $journal->save();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'journal' => $this->journalPayload($journal),
            ]);
        }

        return redirect()->route('journals.index');
    }

    public function destroy(Request $request, $id)
    {
        $journal = $this->journals()->where('id', $id)->firstOrFail();

        $deletedId = $journal->id;
        $journal->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'deleted_id' => $deletedId,
            ]);
        }

        return redirect()->route('journals.index');
    }
}
