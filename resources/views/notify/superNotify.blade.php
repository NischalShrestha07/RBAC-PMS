@extends('layouts.app')

@section('customCss')
<link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
@endsection

@section('content')
<div class="page-content p-4">
    <div class="content-wrapper">

        <!-- Page Header -->
        <section class="content-header">
            <div class="">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Notifications</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Notifications</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Notifications Table -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="navbar p-3">
                                <h2 class="ml-2">All Notifications</h2>
                            </div>

                            <div class="card-body">
                                <!-- Flash Messages -->
                                @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Success!</strong> {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                @endif

                                @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>Error!</strong> {{ session('error') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                @endif

                                <div class="table-responsive">
                                    <table id="notificationsTable" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="selectAll"></th>
                                                <th>Title</th>
                                                <th>Message</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($notifications as $notification)
                                            <tr
                                                class="{{ $notification->status === 'unread' ? 'table-warning' : 'table-success' }}">
                                                <td><input type="checkbox" class="notificationCheck"
                                                        data-id="{{ $notification->id }}"></td>
                                                <td>{{ $notification->title }}</td>
                                                <td>{{ $notification->message }}</td>
                                                <td>
                                                    <span
                                                        class="badge {{ $notification->status === 'unread' ? 'bg-warning' : 'bg-success' }}">
                                                        {{ ucfirst($notification->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $notification->created_at->diffForHumans() }}</td>
                                                <td>
                                                    <!-- Mark as Read -->
                                                    <button class="btn btn-info btn-sm mark-as-read"
                                                        data-id="{{ $notification->id }}">
                                                        <i class="fas fa-check"></i> Mark as Read
                                                    </button>
                                                    <!-- Delete -->
                                                    <form
                                                        action="{{ route('notifications.delete', $notification->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn"
                                                            style="color: white;background: #dc3545; border-color: #dc3545">Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div> <!-- Card End -->
                    </div>
                </div>
            </div>
        </section>

    </div>
</div>
@endsection

@section('customJs')
<!-- DataTables Scripts -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>

<script>
    $(document).ready(function() {
        let table = $('#notificationsTable').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
        });

        // Select/Deselect all checkboxes
        $('#selectAll').on('click', function() {
            let isChecked = $(this).prop('checked');
            $('.notificationCheck').prop('checked', isChecked);
        });

        // Handle Mark as Read button click
        $('.mark-as-read').on('click', function() {
            let notificationId = $(this).data('id');
            $.ajax({
                url: '/notifications/read/' + notificationId,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                },
                success: function(response) {
                    if (response.status === 'success') {
                        // Change the notification row to 'read' style
                        $('button[data-id="' + notificationId + '"]').closest('tr').removeClass('table-warning').addClass('table-success');
                        $('button[data-id="' + notificationId + '"]').prop('disabled', true).text('Marked as Read');
                    }
                },
                error: function() {
                    alert('Failed to mark notification as read.');
                }
            });
        });
    });
</script>
@endsection
