<!DOCTYPE html>
<html>
<head>
    <title>Message Form</title>
</head>
<body>
    <form method="post" action="{{ route('message.store') }}">
        @csrf
        <label for="start_range">Start Range:</label><br>
        <input type="text" id="start_range" name="start_range"><br><br>
        <label for="end_range">End Range:</label><br>
        <input type="text" id="end_range" name="end_range"><br><br>
        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title"><br><br>
        <label for="message">Message:</label><br>
        <input type="text" id="message" name="message"><br><br>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
