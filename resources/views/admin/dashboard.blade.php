@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Admin Dashboard</h2>
    <div class="row mb-4">
        @foreach (['sent' => 'fa-envelope', 'failed' => 'fa-triangle-exclamation', 'pending' => 'fa-hourglass-half'] as $status => $icon)
        <div class="col-md-4 mb-3">
            <div class="p-3 rounded shadow-sm text-center"
                 style="background-color: {{ $status === 'sent' ? '#d4edda' : ($status === 'failed' ? '#f8d7da' : '#fff3cd') }}">
                <div class="fs-2 mb-2">
                    <i class="fa-solid {{ $icon }}"></i>
                </div>
                <h5 class="text-capitalize">{{ $status }}</h5>
                <p class="fs-3 fw-bold">{{ $summary[$status] ?? 0 }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <div class="table-responsive shadow-sm rounded">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>SN</th>
                    <th>User</th>
                    <th>Type</th>
                    <th>Title</th>
                    <th>Message</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recent as $key => $notification)
                    <tr>
                        {{-- Correct serial number across pages --}}
                        <td>{{ ($recent->currentPage() - 1) * $recent->perPage() + $key + 1 }}</td>
                        <td>
                            {{ $notification->user->name ?? 'N/A' }}<br>
                            <small class="text-muted">{{ $notification->user->email ?? 'N/A' }}</small>
                        </td>
                        <td>{{ ucfirst($notification->type) }}</td>
                        <td>{{ ucfirst($notification->title) }}</td>
                        <td>{{ Str::limit($notification->message, 50) }}</td>
                        <td>
                            <span class="badge"
                                  style="background-color: {{ $notification->status === 'sent' ? '#28a745' : ($notification->status === 'failed' ? '#dc3545' : '#ffc107') }}; color: #fff;">
                                {{ ucfirst($notification->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-3">No notifications found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($recent->hasPages())
    <nav>
        <ul class="pagination justify-content-center">

            @if ($recent->onFirstPage())
                <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
            @else
                <li class="page-item"><a class="page-link" href="{{ $recent->previousPageUrl() }}" rel="prev">&laquo;</a></li>
            @endif


            @foreach ($recent->links()->elements[0] as $page => $url)
                @if ($page == $recent->currentPage())
                    <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                @endif
            @endforeach


            @if ($recent->hasMorePages())
                <li class="page-item"><a class="page-link" href="{{ $recent->nextPageUrl() }}" rel="next">&raquo;</a></li>
            @else
                <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
            @endif
        </ul>
    </nav>
@endif

</div>
@endsection
