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

    //đổi tên tag
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
        ]);

        $name = trim($validated['name']);

        //không được rỗng
        if ($name === '') {
            return response()->json(['success' => false], 422);
        }

        //tìm tag cần sửa
        $tag = $this->tags()->where('id', $id)->firstOrFail();

        //kiểm tra tag trùng tên
        $existing = $this->tags()
            ->where('name', $name)
            ->where('id', '!=', $tag->id)
            ->first();

        //nếu đã có task trùng tên thì gộp
        if ($existing) {
            $tag->tasks()->update(['tag_id' => $existing->id]); // thay tag của các task dùng tag cũ

            //lưu lại tên cũ và xóa task
            $oldName = $tag->name;
            $tag->delete();

            return response()->json([
                'success' => true,
                'tag' => $existing,
                'old_name' => $oldName,
                'merged' => true,
            ]);
        }

        //tag không trùng tên
        $oldName = $tag->name;
        $tag->update(['name' => $name]); //cập nhật tên mới

        return response()->json([
            'success' => true,
            'tag' => $tag,
            'old_name' => $oldName,
            'merged' => false,
        ]);
    }

    //xóa tag
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
