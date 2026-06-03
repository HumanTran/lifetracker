<?php

namespace App\Http\Controllers;

use App\Models\Countdown;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CountdownController
{
    private function renderCountdownCard(Countdown $countdown): string
    {
        return view('client.countdown.partials._countdown_card', compact('countdown'))->render();
    }

    private function countdowns()
    {
        return Countdown::where('user_id', Auth::id());
    }

    public function index()
    {
        $countdowns = $this->countdowns()
            ->orderBy('event_date', 'asc')
            ->get();

        return view('client.countdown.index', compact('countdowns'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date',
            'color_code' => 'nullable|string|max:20',
        ]);

        $countdown = Countdown::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'event_date' => $validated['event_date'],
            'color_code' => $validated['color_code'] ?? '#3b82f6',
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'card_html' => $this->renderCountdownCard($countdown),
            ]);
        }

        return redirect()->route('countdown.index');
    }

    public function destroy(Request $request, $id)
    {
        $countdown = $this->countdowns()->where('id', $id)->firstOrFail();

        $deletedId = $countdown->id;
        $countdown->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'deleted_id' => $deletedId,
            ]);
        }

        return redirect()->route('countdown.index');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date',
            'color_code' => 'nullable|string|max:20',
        ]);

        $countdown = $this->countdowns()->where('id', $id)->firstOrFail();

        $countdown->update([
            'title' => $validated['title'],
            'event_date' => $validated['event_date'],
            'color_code' => $validated['color_code'] ?? '#3b82f6',
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'card_html' => $this->renderCountdownCard($countdown),
            ]);
        }

        return redirect()->route('countdown.index');
    }
}
