<?php
namespace App\Http\Controllers;

use App\Models\Countdown;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CountdownController
{

    // Hàm này dùng để render lại card countdown sau khi tạo hoặc cập nhật, giúp trả về HTML mới nhất cho client
    private function renderCountdownCard(Countdown $countdown): string
    {
        return view('client.countdown.partials._countdown_card', compact('countdown'))->render();
    }

    // Hàm này trả về một query builder cho model Countdown, đã được lọc theo user_id của người dùng hiện tại
    private function countdowns()
    {
        return Countdown::where('user_id', Auth::id());
    }

    // Hàm này hiển thị danh sách các countdown của người dùng hiện tại, sắp xếp theo ngày sự kiện
    public function index()
    {
        $countdowns = $this->countdowns()
                    ->orderBy('event_date', 'asc')
                    ->get();

        return view('client.countdown.index', compact('countdowns')); // Trả về view với danh sách countdowns để hiển thị trên giao diện
    }

    // Hàm này xử lý việc tạo mới một countdown, bao gồm xác thực dữ liệu đầu vào và trả về kết quả dưới dạng JSON nếu yêu cầu là AJAX
    // Nếu không phải AJAX, nó sẽ chuyển hướng người dùng trở lại trang danh sách countdowns
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

    // Hàm này xử lý việc xóa một countdown dựa trên ID, đảm bảo rằng countdown đó thuộc về người dùng hiện tại trước khi xóa
    public function destroy(Request $request, $id) // double check xem có cần truyền Request vào đây không, nếu không cần thì bỏ đi để code gọn hơn
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

    // Hàm này xử lý việc cập nhật một countdown dựa trên ID, bao gồm xác thực dữ liệu đầu vào và trả về kết quả dưới dạng JSON nếu yêu cầu là AJAX
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
