<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management - Talha sahi</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container mt-5">
        <!-- Priority update modal -->
<div class="modal fade" id="priorityModal" tabindex="-1" role="dialog" aria-labelledby="priorityModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="priorityModalLabel">Update Task Priority</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <label for="priorityInput">Priority:</label>
                <input type="number" id="priorityInput" class="form-control" name="priority">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" id="updatePriorityBtn" class="btn btn-primary">Update Priority</button>
            </div>
        </div>
    </div>
</div>

        <h1>Task Management</h1>
        <div class="mb-3">
            <h2>Tasks List</h2>
            <button id="sort-by-priority-btn" class="btn btn-info">Sort by Priority</button>
            <table class="table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Priority</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Priority update</th>
                        <th>Create At</th>
                        <th>Update At</th>
                    </tr>
                </thead>
                <tbody id="task-list">
                    <!-- Display tasks dynamically here -->
                    @foreach ($tasks as $task)
                    <tr id="task-{{ $task->id }}">
                    <td>{{ $task->title }}</td>
                    <td>{{ $task->description }}</td>
                    <td class="task-priority">{{ $task->priority }}</td>
                    <td>{{ $task->due_date }}</td>
                    <td>
                                @if ($task->completed)
                            <button class="btn btn-success" disabled>Completed</button>
                            @else
                            <button class="btn btn-primary mark-as-complete" data-task-id="{{ $task->id }}">Mark as Complete</button>
                            @endif
                            </td>

                            <td class="text-center">
                            <!-- Priority icon to open modal -->
                            <i class="fa fa-edit priority-icon pointer" data-task-id="{{ $task->id }}" data-priority="{{ $task->priority }}"></i>
                            </td>
                            <td>{{ $task->created_at }}</td>
                            <td>{{ $task->updated_at }}</td>
                </tr>
@endforeach

                </tbody>
            </table>
        </div>
        <hr>
        <h2>Create New Task</h2>
        <form id="create-task-form">
            <!-- Form fields -->
            <div class="form-group">
                <input type="text" class="form-control" name="title" placeholder="Title" required>
                @error('title')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <textarea class="form-control" name="description" placeholder="Description" required></textarea>
                @error('description')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <input type="number" class="form-control" name="priority" placeholder="Priority" required>
                @error('priority')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <input type="date" class="form-control" name="due_date" required>
                @error('due_date')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            
            <button type="submit" class="btn btn-primary mt-4 mb-4">Create Task</button>
        </form>
    </div>

    <!-- Bootstrap JS and jQuery (required for Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript for AJAX -->
    <script>
      function appendTaskRow(task) {
    const taskList = document.getElementById('task-list');
    const taskRow = document.createElement('tr');
    taskRow.id = `task-${task.id}`;
    taskRow.innerHTML = `
        <td>${task.title}</td>
        <td>${task.description}</td>
        <td class="task-priority">${task.priority}</td>
        <td>${task.due_date}</td>
        <td>
            ${task.completed ?
                '<button class="btn btn-success" disabled>Completed</button>' :
                `<button class="btn btn-primary mark-as-complete" data-task-id="${task.id}">Mark as Complete</button>`
            }
        </td>
        <td class="text-center">
            <i class="fa fa-edit priority-icon pointer" data-task-id="${task.id}" data-priority="${task.priority}"></i>
        </td>
        <td>${task.created_at}</td>
        <td>${task.updated_at}</td>
    `;
    taskList.appendChild(taskRow);
}


        document.getElementById('create-task-form').addEventListener('submit', function (event) {
            event.preventDefault();
    
            const formData = new FormData(this);
            fetch('/tasks', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (response.ok) {
                    return response.json();
                } else {
                    return response.json().then(data => {
                        throw new Error(JSON.stringify(data.errors));
                    });
                }
            })
            .then(data => {
                alert(data.message);
                // Clear form fields after successful submission
                this.reset();
                // Add new task dynamically to the task list
                appendTaskRow(data.task);
            })
            .catch(error => {
                // Display validation errors
                const errorContainer = document.getElementById('validation-errors');
                errorContainer.innerHTML = JSON.parse(error.message).join('<br>');
            });
        });

        document.getElementById('task-list').addEventListener('click', function (event) {
            if (event.target.tagName === 'BUTTON' && event.target.textContent === 'Mark as Complete') {
                const taskId = event.target.dataset.taskId;

                fetch(`/tasks/${taskId}/complete`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    } else {
                        throw new Error('Failed to mark task as complete.');
                    }
                })
                .then(data => {
                    alert("Updated");
                    // Update the status of the task in the UI
                    const taskButton = event.target;
                    taskButton.textContent = 'Completed';
                    taskButton.classList.remove('btn-primary');
                    taskButton.classList.add('btn-success');
                    taskButton.disabled = true;
                })
                .catch(error => {
                    console.error(error);
                    alert('An error occurred while marking the task as complete.');
                });
            }
        });
    </script>



   

   <!-- Update the JavaScript in your HTML file -->

<script>
    // Function to fetch and update tasks sorted by priority
    function sortTasksByPriority() {
        fetch('/tasks/sortByPriority')
            .then(response => {
                if (response.ok) {
                    return response.json();
                } else {
                    throw new Error('Failed to fetch sorted tasks.');
                }
            })
            .then(data => {
                // Clear existing task list
                const taskList = document.getElementById('task-list');
                taskList.innerHTML = '';

                // Append sorted tasks to the task list
                data.tasks.forEach(task => {
                    appendTaskRow(task);
                });
            })
            .catch(error => {
                console.error(error);
                alert('An error occurred while sorting tasks.');
            });
    }

    // Call the sortTasksByPriority function when needed, such as clicking a button
    document.getElementById('sort-by-priority-btn').addEventListener('click', sortTasksByPriority);


// Custom JavaScript for AJAX

// Function to handle priority update modal
function openPriorityModal(taskId, currentPriority) {
    const modal = document.getElementById('priorityModal');
    const priorityInput = modal.querySelector('#priorityInput');
    priorityInput.value = currentPriority;
    
    modal.dataset.taskId = taskId;
    $('#priorityModal').modal('show');
}

document.getElementById('updatePriorityBtn').addEventListener('click', function (event) {
    // Get the modal element
    const modal = document.getElementById('priorityModal');
    
    // Retrieve the task ID from the modal's dataset
    const taskId = modal.dataset.taskId;
    
    // Retrieve the new priority value
    const newPriority = document.getElementById('priorityInput').value;

    fetch(`/tasks/${taskId}/update-priority`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ priority: newPriority }) // Include the new priority in the request body
    })
    .then(response => {
        if (response.ok) {
            return response.json();
        } else {
            throw new Error('Failed to update task priority.');
        }
    })
    .then(data => {
        // Log the response from the controller
        console.log("Response:", data);
        $('#priorityModal').modal('hide');

         // Update task priority visually in the table row
        const taskRow = document.getElementById(`task-${taskId}`);
        taskRow.querySelector('.task-priority').textContent = data.task.priority;

        
        
        
    })
    .catch(error => {
        console.error(error);
        alert('An error occurred while updating task priority.');
    });
});




// Function to open priority modal when priority icon is clicked
document.querySelectorAll('.priority-icon').forEach(icon => {
    icon.addEventListener('click', function (event) {
        const taskId = event.target.dataset.taskId;
        const currentPriority = event.target.dataset.priority;
        openPriorityModal(taskId, currentPriority);


       
    });
});

</script>

</body>
</html>
