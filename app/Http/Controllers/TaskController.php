<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Request $request)
    {

        $user = Auth::user();

        if ($user->role === 'admin') {
            $tasks = Task::all();
            $employees = User::where('role', 'employee')->get();  // Fetch employees for assignment
        } else {
            $tasks = Task::where('can_assign_tasks', $user->id)->get();
            $employees = collect();  // Empty collection for non-admins
        }

        return view('Task.index', compact('tasks', 'employees'));
    }



    public function create()
    {
        if (Gate::allows('create-task')) {
            return view('Task.create');
        }

        // If not authorized, show a 403 error
        abort(403, 'This action is unauthorized.');
    }

    public function store(Request $request)
    {
        $user_id = Auth::user()->id;

        $this->authorize('create', Task::class);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,completed',
        ]);
        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'status' => 'pending',
            'user_id' => $user_id,
            'created_by' => Auth::id(), // Current admin
        ]);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    // Show the form for editing a task
    public function edit($id)
    {
        $task = Task::findOrFail($id);
        $employees = User::where('role', 'employee')->get(); // Fetch only employees

        return view('Task.edit', compact('task', 'employees'));
    }

    // Update the task in the database
    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task); // Ensure the user can update this task

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,completed',
        ]);

        $task->update($request->all());

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    // Delete a task from the database
    // app/Http/Controllers/TaskController.php

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task); // Authorize the delete action

        // Proceed to delete the task
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }


    public function markAsCompleted(Task $task)
    {

        if (auth()->user()->id !== $task->assigned_to && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized to complete this task.');
        }

        // Mark the task as completed
        $task->status = 'completed';
        $task->save();

        return redirect()->route('tasks.index')->with('success', 'Task marked as completed.');
    }

    public function assignCreate($id)
    {
        $task = Task::findOrFail($id);
        $employees = User::where('role', 'employee')->get(); // Fetch only employees

        return view('Task.assign', compact('task', 'employees'));
    }
    public function assign(Request $request, Task $task)
    {

        $user_id = Auth::user()->id;
        $task->user_id = $user_id;
        $task->can_assign_tasks = $request->can_assign_tasks; // Assume `assigned_to` is the user_id of the employee
        $task->update();

        return redirect()->route('tasks.index')->with('success', 'Task assigned successfully.');
    }
}
