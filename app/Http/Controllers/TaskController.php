<?php

namespace App\Http\Controllers;

use App\Models\Tag; //đại diện cho bảng tag
use App\Models\Task; //đại điện cho bảng task
use Carbon\Carbon; //xử lý ngày tháng
use Illuminate\Http\Request; //chứa dữ liệu người dùng gửi
use Illuminate\Support\Facades\Auth; //lấy user đang đăng nhập

class TaskController
{
    //chỉ lấy task của user đang đăng nhập
    private function tasks()
    {
        return Task::where('user_id', Auth::id());
    }

    //xử lý ngày, tránh ngày không hợp lệ
    private function parseDateOrToday(?string $date): string
    {
        try {
            return Carbon::parse($date ?: Carbon::today()->toDateString())->toDateString();
        } catch (\Throwable) {
            return Carbon::today()->toDateString();
        }
    }

    //hiển thị danh sách task
    public function index(Request $request)
    {
        $view = $request->query('view', 'daily'); //mặc định xem task trong ngày
        $today = Carbon::today()->toDateString();
        $selectedDate = $today;

        //tablet trở lên hiện 8 task 1 trang, mobile hiện 4 task 1 trang. chỉ cho phép 4 hoặc 8
        $requestedPerPage = (int) $request->query('per_page', 8);
        $perPage = in_array($requestedPerPage, [4, 8], true) ? $requestedPerPage : 8;

        //lấy dữ liệu search, lọc từ url
        $search = trim($request->query('search', ''));
        $tagFilter = trim($request->query('tag', ''));
        $priorityFilter = trim($request->query('priority', ''));

        //lấy task và tag của task
        $query = $this->tasks()->with('tag');

        //nếu là task trong ngày thì due date là ngày đang chọn
        if ($view === 'daily') {
            $selectedDate = $this->parseDateOrToday($request->query('date', $today));
            $query->whereDate('due_date', $selectedDate);
        } else { //task dài ngày thì due date là null hoặc khác ngày hôm nay
            $view = 'multi';
            $query->where(function ($q) use ($today) {
                $q->whereNull('due_date')
                    ->orWhereDate('due_date', '!=', $today);
            });
        }

        //tìm kiếm và filter
        if ($search !== '') {
            $query->where('title', 'like', '%'.$search.'%');
        }
        if ($tagFilter !== '') {
            $query->whereHas('tag', function ($tagQuery) use ($tagFilter) {
                $tagQuery->where('name', $tagFilter);
            });
        }
        if ($priorityFilter !== '') {
            $query->where('priority', $priorityFilter);
        }

        //phân trang
        $tasks = $query
            ->orderBy('created_at', 'desc') //task mới tạo hiện trước
            ->paginate($perPage) //phân trang, 4 hoặc 8
            ->withQueryString(); //giữ lại query filter khi chuyển trang

        //lấy danh sách tag
        $tags = Tag::where('user_id', Auth::id())->get();

        //chuyển dữ liệu sang view
        return view('client.tasks.index', compact(
            'tasks',
            'tags',
            'view',
            'selectedDate',
            'search',
            'tagFilter',
            'priorityFilter'
        ));
    }

    //thêm task
    public function store(Request $request)
    {
        $view = $request->input('view', 'multi'); // lấy chế độ hiện tại từ request, mặc định là multi

        //đảm bảo dữ liệu hợp lệ
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'priority' => 'required|in:high,med,low',
            'due_date' => 'nullable|date',
        ]);

        //deadline, daily thì là ngày đang xem, multi thì lấy từ form user nhập, không nhập thì null
        $dueDate = $view === 'daily'
            ? $this->parseDateOrToday($request->input('date', Carbon::today()->toDateString()))
            : ($validated['due_date'] ?? null);

        //tạo task
        $task = Task::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'status' => 'pending',
            'priority' => $validated['priority'],
            'due_date' => $dueDate,
        ]);

        //nếu request là ajax/json, controller trả json, nếu không phải thì sẽ redirect về trang task phù hợp
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'task' => $task,
            ]);
        }
        return redirect()->route('tasks.index', $view === 'daily'
            ? ['view' => 'daily', 'date' => $dueDate]
            : ['view' => 'multi']);
    }

    //sửa task
    public function update(Request $request, $id)
    {
        //kiểm tra tính hợp lệ
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'in:high,med,low'],
            'due_date' => ['nullable', 'date'],
            'view' => ['nullable', 'in:daily,multi'],
            'date' => ['nullable', 'date'],
        ]);

        //chỉ tìm task của user đó
        $task = $this->tasks()->where('id', $id)->firstOrFail();

        // xử lý ngày
        $view = $validated['view'] ?? 'multi';
        if ($view === 'daily') {
            $dueDate = $this->parseDateOrToday($validated['date'] ?? null);
        } else {
            $dueDate = ! empty($validated['due_date'])
                ? Carbon::parse($validated['due_date'])->toDateString()
                : null;
        }

        //cập nhật
        $task->update([
            'title' => $validated['title'],
            'priority' => $validated['priority'],
            'due_date' => $dueDate,
        ]);

        //tải lại dữ liệu từ db
        $task->refresh();

        //nếu request đến từ js/ajax và frontend muộn nhận json
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'task' => $task,
            ]);
        }
        //nếu phải request json thì quay lại trang trước
        return back();
    }

    //xóa task
    public function destroy(Request $request, $id)
    {
        //tìm task cần xóa
        $task = $this->tasks()->where('id', $id)->firstOrFail();

        $deletedId = $task->id; //lưu lại id trước khi xóa để frontend xóa khỏi giao diện
        $task->delete(); //xóa khỏi db

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'deleted_id' => $deletedId,
            ]);
        }

        return back();
    }

    //update trạng thái (tích hoàn thành, bỏ tích hoàn thành)
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,done',
        ]);

        $task = $this->tasks()->where('id', $id)->firstOrFail();

        $task->status = $validated['status']; //gán trạng thái mới cho task
        $task->save();

        //trả json cho frontend
        return response()->json(['success' => true, 'status' => $task->status]);
    }

    //sửa tag, ưu tiên, due date không cần mở form
    public function updateField(Request $request, $id)
    {
        $task = $this->tasks()->where('id', $id)->firstOrFail();

        //lấy dữ liệu frontend gửi
        $field = $request->input('field');
        $value = $request->input('value');

        //sửa ưu tiên
        if ($field === 'priority') {
            //nếu không hợp lệ trả về lỗi
            if (! in_array($value, ['high', 'med', 'low'], true)) {
                return response()->json(['success' => false], 422);
            }

            //cập nhật và lưu vào db
            $task->priority = $value;
            $task->save();

            return response()->json(['success' => true]);
        }

        //sửa task
        if ($field === 'tag') {
            //bỏ khoảng trắng, tag không được rỗng, không dài quá 50
            if (empty(trim($value)) || mb_strlen(trim($value)) > 50) {
                return response()->json(['success' => false], 422);
            }

            //nếu tag đã tồn tại thì lấy tag đó, chưa tồn tại thì tạo mới
            $tag = Tag::firstOrCreate([
                'name' => trim($value),
                'user_id' => Auth::id(),
            ]);

            //gắn tag và lưu
            $task->tag_id = $tag->id;
            $task->save();

            return response()->json([
                'success' => true,
                'tag' => $tag,
            ]);
        }

        //sửa deadline
        if ($field === 'due_date') {
            //nếu bỏ trống
            if ($value === null || trim((string) $value) === '') {
                $task->due_date = null;
                $task->save();

                return response()->json(['success' => true]);
            }

            //không bỏ trống
            try {
                $task->due_date = Carbon::parse($value)->toDateString(); //nếu ngày hợp lệ, lưu về dạng YYYY-MM-DD
            } catch (\Throwable $e) {
                return response()->json(['success' => false], 422); //ngày không hợp lệ
            }

            $task->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 422); //nếu không phải priority, tag, due_date thì lỗi
    }
}
