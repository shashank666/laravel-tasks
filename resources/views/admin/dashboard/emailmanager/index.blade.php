@extends('admin.layouts.app')

@section('content')
    <div class="header">
            <div class="container">
            <div class="header-body">
                <div class="row align-items-center">
                    <div class="col">
                        <h1 class="header-title">
                        Email Manager
                        </h1>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.email.create_form') }}" class="btn btn-primary">Send New Email</a>
                    </div>
                </div>
            </div>
    </div>
    <div class="container my-5">
            @include('admin.partials.message')

            <div class="card">
                    <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Subject</th>
                                    <th>To</th>
                                    <th>Created at</th>
                                    <th>Scheduled at</th>
                                    <th>Status</th>
                                    <th>Is Active</th>
                                    <th>Preview</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($emails as $email)
                                    <tr>
                                        <td>{{ $email->id }}</td>
                                        <td>{{ $email->email_subject }}</td>
                                        <td>{{ $email->email_to }}</td>
                                        <td>{{ $email->created_at }}</td>
                                        <td>{{ $email->scheduled_at }}</td>
                                        <td>
                                            @if($email->status=='not_scheduled')
                                            <span class="badge badge-warning">{{ $email->status }}</span>
                                            @elseif($email->status=='scheduled')
                                            <span class="badge badge-success">{{ $email->status }}</span>
                                            @elseif($email->status=='stopped')
                                            <span class="badge badge-danger">{{ $email->status }}</span>
                                            @elseif($email->status=='completed')
                                            <span class="badge badge-dark">{{ $email->status }}</span>
                                            @endif
                                        </td>
                                        <td> @if($email->is_active==0)
                                            <span class="badge badge-danger">No</span>
                                            @else
                                            <span class="badge badge-success">Yes</span>
                                            @endif
                                         </td>
                                        <td><a href="{{ route('admin.email.preview',['id'=>$email->id]) }}" class="btn btn-sm btn-primary">Preview</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                    </table>
            </div>

    </div>
@endsection
