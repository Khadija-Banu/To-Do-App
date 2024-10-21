<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-4">Your Tasks</h1>

        <div class="d-flex justify-content-between mb-3">
            <!-- Create Task Button (Left) -->
            <a href="{{ route('tasks.create') }}" class="btn btn-primary">Create Task</a>

            <!-- Logout Button (Right) -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        </div>

        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($tasks as $task)
                    @if ($task->status === 'pending')
                        <tr>
                            <td>{{ $task->title }}</td>
                            <td>{{ $task->description }}</td>
                            <td>{{ $task->due_date->format('Y-m-d') }}</td>
                            <td>

                                <span class="badge badge-warning text-black">Pending</span>


                            </td>
                            <td>
                                {{-- Mark as Completed if Pending --}}
                                @if ($task->status === 'pending')
                                    <form action="{{ route('tasks.complete', $task->id) }}" method="POST"
                                        style="display:inline-block;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success">Mark as Completed</button>
                                    </form>
                                @endif


                                {{-- Admin Actions: Assign/Edit/Delete --}}
                                @if (auth()->user()->role === 'admin')
                                    <a href="{{ route('create.assign', $task->id) }}"
                                        class="btn btn-sm btn-primary">Assign Task</a>
                                    {{-- Edit/Delete Actions --}}
                                    <a href="{{ route('tasks.edit', $task->id) }}"
                                        class="btn btn-sm btn-warning">Edit</a>


                                    {{-- Admin Actions: Edit/Delete --}}
                                    @if (auth()->user()->role === 'admin')
                                        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST"
                                            style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>


        @if ($tasks->isEmpty())
            <p>No tasks available.</p>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>
