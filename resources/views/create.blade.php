<!-- resources/views/opinion/create.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <title>Create Opinion</title>
</head>
<body>

<h1>Create Opinion</h1>

<form method="POST" action="{{ route('admin.store') }}">
    @csrf

    <div>
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" required>
    </div>

    <div>
        <label for="description">Description:</label>
        <textarea name="description" id="description" rows="4" required></textarea>
    </div>

    <div>
        <button type="submit">Post</button>
    </div>
</form>

</body>
</html>
