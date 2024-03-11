<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    // Display all tasks in a paginated manner
    public function index()
    {
        $tasks = Task::paginate(100);
        return view('task', compact('tasks'));
    }

    // Store the submitted task data in the database
    public function store(Request $request)
{
    try {
        // Validate the request data
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|integer',
            'due_date' => 'required|date',
        ]);

        // Create the task
        $task = Task::create($validatedData);

        // Return the newly created task as JSON
        return response()->json(['message' => 'Task created successfully', 'task' => $task]);
    } catch (ValidationException $e) {
        // Handle validation errors
        return response()->json(['errors' => $e->errors()], 422);
    }
}


// Mark a task as complete
  public function markAsComplete(Task $task)
  {
    $task->update(['completed' => true]);

     return $task;

    return redirect()->route('tasks.index')->with('success', 'Task marked as complete successfully.');
   }

    public function sortByPriority()
    {
        $tasks = Task::orderBy('priority', 'desc')->get();
        return response()->json(['tasks' => $tasks]);
    }

    // Update the priority of a task
    public function updatePriority(Request $request, Task $task)
{
    // Validate the request data
    $validatedData = $request->validate([
        'priority' => 'required|integer',
    ]);

    // Update the task priority
    $task->update(['priority' => $validatedData['priority']]);

    // Return a response
    return response()->json(['message' => 'Task priority updated successfully', 'task' => $task]);
}

    

}
