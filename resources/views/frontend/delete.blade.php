<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h5>Delete Account</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Please confirm your email or phone number and understand that this action is irreversible. It will permanently delete your account and all associated data.</p>
                        <form method="POST" action="{{ route('account.delete') }}">
                            @csrf
                            <div class="form-group">
                                <label for="confirmation">Email or Phone Number</label>
                                <input type="text" class="form-control" id="confirmation" name="confirmation" required placeholder="Enter your email or phone number">
                            </div>
                            <button type="submit" class="btn btn-danger">Confirm Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
