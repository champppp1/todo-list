<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    public function __construct()
    {
        // เปลี่ยนจาก auth:api เป็น auth ปกติ
        $this->middleware('auth');
    }

    public function index()
    {
        try {
            $tasks = Task::where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($tasks);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch tasks',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        Log::info('Task Store Request:', [
            'user_id' => auth()->id(),
            'request_data' => $request->all(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            Log::debug('Validated Data:', $validated);

            $task = Task::create([
                'user_id' => auth()->id(),
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'completed' => false
            ]);

            Log::info('Task Created Successfully:', [
                'task_id' => $task->id,
                'task_title' => $task->title
            ]);

            return response()->json($task, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error:', [
                'errors' => $e->errors(),
                'request' => $request->all()
            ]);
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Task Creation Failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json([
                'message' => 'Failed to create task',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // เพิ่ม method create และ edit สำหรับแสดงฟอร์ม
    public function create()
    {
        return view('tasks.create');
    }

    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        try {
            $this->authorize('update', $task);

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'status' => 'required|in:low,medium,high',
            ]);

            $task->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'status' => $validated['status']
            ]);

            return response()->json($task);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update task',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, Task $task)
    {
        try {
            $this->authorize('update', $task);

            $validated = $request->validate([
                'completed' => 'required|boolean'
            ]);

            $task->update(['completed' => $validated['completed']]);

            return response()->json($task);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update task status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Task $task)
    {
        try {
            $this->authorize('delete', $task);

            $task->delete();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete task',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // เพิ่ม method show สำหรับแสดง task เดียว
    public function show(Task $task)
    {
        $this->authorize('view', $task);
        return view('tasks.show', compact('task'));
    }
}
