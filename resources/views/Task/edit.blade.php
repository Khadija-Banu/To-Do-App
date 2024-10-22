<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Create Task</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-4">Edit Task</h1>

        <form action="{{ route('tasks.update', $task->id) }}" method="POST"
            style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
            @csrf
            @method('PUT')

            <div class="form-group mb-3">
                <label for="title" class="font-weight-bold">Task Title</label>
                <input type="text" name="title" id="title" class="form-control" value="{{ $task->title }}"
                    required>
            </div>

            <div class="form-group mb-3">
                <label for="description" class="font-weight-bold">Task Description</label>
                <textarea name="description" id="description" class="form-control" rows="4">{{ $task->description }}</textarea>
            </div>

            <div class="form-group mb-3">
                <label for="due_date" class="font-weight-bold">Due Date</label>
                <input type="date" name="due_date" id="due_date" class="form-control" value="{{ $task->due_date }}">
            </div>

            <div class="form-group mb-3">
                <label for="status" class="font-weight-bold">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update Task</button>
        </form>
    </div>
    <!-- Bootstrap JS Bundle (with Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoF6cf5VycGFpXzcLrPEnb7JOcK7q1iK5fQJw5ATgjUkQJa" crossorigin="anonymous">
    </script>
</body>

</html>
