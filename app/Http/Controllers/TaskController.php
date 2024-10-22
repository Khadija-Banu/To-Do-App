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
    //show all task with permission
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Task::query();

        if ($user->role === 'admin') {
            if ($request->has('search') && !empty($request->search)) {
                $query->where('title', 'LIKE', '%' . $request->search . '%');
            }
            $tasks = $query->get();
            $employees = User::where('role', 'employee')->get();
        } else {

            if ($request->has('search') && !empty($request->search)) {

                $query->where('title', 'LIKE', '%' . $request->search . '%')
                    ->where('can_assign_tasks', $user->id);
            } else {

                $query->where('can_assign_tasks', $user->id);
            }
            $tasks = $query->get();
            $employees = collect();
        }

        return view('Task.index', compact('tasks', 'employees'));
    }

    //create page open which is authorized
    public function create()
    {
        if (Gate::allows('create-task')) {
            return view('Task.create');
        }

        abort(403, 'This action is unauthorized.');
    }

    //save task data with validation
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
            'created_by' => Auth::id(),
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
        $this->authorize('update', $task);

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
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }


    // Mark the task as completed
    public function markAsCompleted(Task $task)
    {
        if (auth()->user()->id !== $task->can_assign_tasks && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized to complete this task.');
        }
        $task->status = 'completed';
        $task->save();
        return redirect()->route('tasks.index')->with('success', 'Task marked as completed.');
    }

    //create a assign page show with task id
    public function assignCreate($id)
    {
        $task = Task::findOrFail($id);
        $employees = User::where('role', 'employee')->get();

        return view('Task.assign', compact('task', 'employees'));
    }

    //only authorized assign the task
    public function assign(Request $request, Task $task)
    {
        $user_id = Auth::user()->id;
        $task->user_id = $user_id;
        $task->can_assign_tasks = $request->can_assign_tasks;
        $task->update();
        return redirect()->route('tasks.index')->with('success', 'Task assigned successfully.');
    }

    //open task permission page with only employee
    public function createTaskPermissions()
    {;
        $employees = User::where('role', 'employee')->get();
        return view('Task.create_tasks', compact('employees'));
    }

    //save task create permission with admin
    public function updateEmployeePermissions(Request $request)
    {
        $employees = User::all();
        foreach ($employees as $employee) {

            if ($employee->id == $request->can_assign_tasks) {
                $employee->can_create_tasks = $request->can_assign_tasks;
                $employee->save();
            }
        }
        return redirect()->route('tasks.index')->with('success', 'Permissions updated successfully');
    }
}
