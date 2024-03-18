@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Account Deletion</h1>
    <p>App Name: <strong>Your App Name Here</strong></p>
    <p>If you wish to delete your account, please note that all your data including your profile, posts, and any other information will be permanently deleted. This action cannot be undone.</p>
    
    <form method="POST" action="{{ route('account.delete') }}">
        @csrf
        <button type="submit" class="btn btn-danger">Delete My Account</button>
    </form>
</div>
@endsection
