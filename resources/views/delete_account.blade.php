<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Delete Account - Opined Android App</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <style>
        /* Your provided styles */
        /* ... */

        /* Additional Styles for Page Specifics */
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #f3f4f6; /* Use a light background color */
            color: #1a202c; /* Dark text color */
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
            margin-top: 4rem; /* Adjust as needed */
        }

        .title {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .message {
            font-size: 1.25rem;
            line-height: 1.5;
            margin-bottom: 2rem;
        }

        .delete-button {
            background-color: #ef4444;
            color: #fff;
            padding: 0.75rem 1.5rem;
            border-radius: 0.25rem;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        .delete-button:hover {
            background-color: #dc2626;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="title">Delete Your Account - Opined Android App</div>
        <div class="message">
            <p>
                We're sorry to see you go. If you choose to proceed, all your account information for the Opined Android App
                will be permanently deleted. This action cannot be undone.
            </p>
        </div>
        <p>
            <strong>Important:</strong> Make sure to backup any important data before deleting your account.
        </p>
        <a href="{{ route('account.delete') }}" class="delete-button" onclick="return confirm('Are you sure you want to delete your account?');">
            Delete My Account
        </a>
    </div>
</body>

</html>
