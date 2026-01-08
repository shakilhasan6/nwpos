@extends('welcome')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h3 class="mb-4 d-none d-md-block"style="font-family: serif; color: #535353; text-transform: uppercase">Engineer Convince Reports</h3>
                <h3 class="mb-4 d-block d-md-none"style="font-family: serif; color: #535353; text-transform: uppercase">Engineer Reports</h3>

                @if ($grouped)
                    @foreach ($grouped as $engineer => $months)
                        @php $collapseId = str_replace(' ', '', $engineer) @endphp

                        <!-- Desktop/Tablet View: Table -->
                        <div class="d-none d-md-block">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link text-left w-100 d-flex justify-content-between align-items-center" type="button" data-toggle="collapse"
                                            data-target="#collapse{{ $collapseId }}" aria-expanded="true"
                                            aria-controls="collapse{{ $collapseId }}">
                                            {{ $engineer }}
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                    </h5>
                                </div>
                                <div id="collapse{{ $collapseId }}" class="collapse show">
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-striped mb-0">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Month Name</th>
                                                        <th>Check</th>
                                                        <th>Verify</th>
                                                        <th>Completed</th>
                                                        <th>Grand Total</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($months as $month => $monthData)
                                                        <tr>
                                                            <td>{{ $month }}</td>
                                                            <td>
                                                                <span class="badge badge-{{ $monthData['status'] == 'approved' ? 'success' : 'warning' }}">
                                                                    {{ ucfirst($monthData['status']) }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-{{ $monthData['verify'] == 'approved' ? 'success' : 'warning' }}">
                                                                    {{ ucfirst($monthData['verify']) }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-{{ $monthData['completed'] == 'approved' ? 'success' : 'warning' }}">
                                                                    {{ ucfirst($monthData['completed']) }}
                                                                </span>
                                                            </td>
                                                            <td>{{ number_format($monthData['grand_total'], 2) }}</td>
                                                            <td>
                                                                <div class="btn-group" role="group">
                                                                    @if (auth()->user()->isAdmin() || auth()->user()->isChecker() || auth()->user()->isVerify())
                                                                        <a href="{{ route('engineer_logs.month_view', ['engineer' => urlencode($monthData['engineer']), 'month' => urlencode($month)]) }}"
                                                                            class="btn btn-info btn-sm" title="View">
                                                                            <i class="fas fa-eye"></i>
                                                                        </a>

                                                                        @if (auth()->user()->isAdmin() || auth()->user()->isChecker())
                                                                            <form action="{{ route('engineer_logs.bulk_approve', ['engineer' => urlencode($monthData['engineer']), 'month' => urlencode($month)]) }}"
                                                                                method="POST" class="d-inline">
                                                                                @csrf
                                                                                <button type="submit" class="btn btn-success btn-sm" title="Approve">
                                                                                    <i class="fas fa-check"></i>
                                                                                </button>
                                                                            </form>
                                                                        @endif

                                                                        @if (auth()->user()->isAdmin() || auth()->user()->isVerify())
                                                                            <form action="{{ route('engineer_logs.bulk_verify', ['engineer' => urlencode($monthData['engineer']), 'month' => urlencode($month)]) }}"
                                                                                method="POST" class="d-inline">
                                                                                @csrf
                                                                                <button type="submit" class="btn btn-primary btn-sm" title="Verify">
                                                                                    <i class="fas fa-check-double"></i>
                                                                                </button>
                                                                            </form>
                                                                        @endif

                                                                        @if (auth()->user()->isAdmin())
                                                                            <form action="{{ route('engineer_logs.bulk_complete', ['engineer' => urlencode($monthData['engineer']), 'month' => urlencode($month)]) }}"
                                                                                method="POST" class="d-inline">
                                                                                @csrf
                                                                                <button type="submit" class="btn btn-info btn-sm" title="Complete">
                                                                                    <i class="fas fa-check-circle"></i>
                                                                                </button>
                                                                            </form>
                                                                            <form action="{{ route('engineer_logs.month_delete', ['engineer' => urlencode($monthData['engineer']), 'month' => urlencode($month)]) }}"
                                                                                method="POST" class="d-inline"
                                                                                onsubmit="return confirm('Are you sure you want to delete all logs for this month?')">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                                                    <i class="fas fa-trash"></i>
                                                                                </button>
                                                                            </form>
                                                                            <a href="{{ route('engineer_logs.export_month', ['engineer' => urlencode($monthData['engineer']), 'month' => urlencode($month)]) }}"
                                                                                class="btn btn-primary btn-sm" title="Export">
                                                                                <i class="fas fa-download"></i>
                                                                            </a>
                                                                        @endif
                                                                    @else
                                                                        <a href="{{ route('engineer_logs.month_view', ['engineer' => urlencode($monthData['engineer']), 'month' => urlencode($month)]) }}"
                                                                            class="btn btn-info btn-sm" title="View">
                                                                            <i class="fas fa-eye"></i>
                                                                        </a>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile View: Cards -->
                        <div class="d-block d-md-none">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link text-left w-100 d-flex justify-content-between align-items-center p-0" type="button" data-toggle="collapse"
                                            data-target="#mobileCollapse{{ $collapseId }}" aria-expanded="false"
                                            aria-controls="mobileCollapse{{ $collapseId }}">
                                            <strong>{{ $engineer }}</strong>
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                    </h5>
                                </div>
                                <div id="mobileCollapse{{ $collapseId }}" class="collapse">
                                    <div class="card-body p-2">
                                        @foreach ($months as $month => $monthData)
                                            <div class="card mb-2 border">
                                                <div class="card-body p-3">
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <strong>Month:</strong> {{ $month }}
                                                        </div>
                                                        <div class="col-6 text-right">
                                                            <strong>Total:</strong> {{ number_format($monthData['grand_total'], 2) }}
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-4 text-center">
                                                            <small class="text-muted d-block">Check</small>
                                                            <span class="badge badge-{{ $monthData['status'] == 'approved' ? 'success' : 'warning' }} badge-sm">
                                                                {{ ucfirst($monthData['status']) }}
                                                            </span>
                                                        </div>
                                                        <div class="col-4 text-center">
                                                            <small class="text-muted d-block">Verify</small>
                                                            <span class="badge badge-{{ $monthData['verify'] == 'approved' ? 'success' : 'warning' }} badge-sm">
                                                                {{ ucfirst($monthData['verify']) }}
                                                            </span>
                                                        </div>
                                                        <div class="col-4 text-center">
                                                            <small class="text-muted d-block">Completed</small>
                                                            <span class="badge badge-{{ $monthData['completed'] == 'approved' ? 'success' : 'warning' }} badge-sm">
                                                                {{ ucfirst($monthData['completed']) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-3">
                                                        <div class="col-12">
                                                            <div class="btn-group-vertical w-100" role="group">
                                                                @if (auth()->user()->isAdmin() || auth()->user()->isChecker() || auth()->user()->isVerify())
                                                                    <a href="{{ route('engineer_logs.month_view', ['engineer' => urlencode($monthData['engineer']), 'month' => urlencode($month)]) }}"
                                                                        class="btn btn-info btn-sm mb-1">
                                                                        <i class="fas fa-eye"></i> View
                                                                    </a>

                                                                    @if (auth()->user()->isAdmin() || auth()->user()->isChecker())
                                                                        <form action="{{ route('engineer_logs.bulk_approve', ['engineer' => urlencode($monthData['engineer']), 'month' => urlencode($month)]) }}"
                                                                            method="POST" class="mb-1">
                                                                            @csrf
                                                                            <button type="submit" class="btn btn-success btn-sm w-100">
                                                                                <i class="fas fa-check"></i> Approve
                                                                            </button>
                                                                        </form>
                                                                    @endif

                                                                    @if (auth()->user()->isAdmin() || auth()->user()->isVerify())
                                                                        <form action="{{ route('engineer_logs.bulk_verify', ['engineer' => urlencode($monthData['engineer']), 'month' => urlencode($month)]) }}"
                                                                            method="POST" class="mb-1">
                                                                            @csrf
                                                                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                                                                <i class="fas fa-check-double"></i> Verify
                                                                            </button>
                                                                        </form>
                                                                    @endif

                                                                    @if (auth()->user()->isAdmin())
                                                                        <form action="{{ route('engineer_logs.bulk_complete', ['engineer' => urlencode($monthData['engineer']), 'month' => urlencode($month)]) }}"
                                                                            method="POST" class="mb-1">
                                                                            @csrf
                                                                            <button type="submit" class="btn btn-info btn-sm w-100">
                                                                                <i class="fas fa-check-circle"></i> Complete
                                                                            </button>
                                                                        </form>
                                                                        <form action="{{ route('engineer_logs.month_delete', ['engineer' => urlencode($monthData['engineer']), 'month' => urlencode($month)]) }}"
                                                                            method="POST" class="mb-1"
                                                                            onsubmit="return confirm('Are you sure you want to delete all logs for this month?')">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="btn btn-danger btn-sm w-100">
                                                                                <i class="fas fa-trash"></i> Delete
                                                                            </button>
                                                                        </form>
                                                                        <a href="{{ route('engineer_logs.export_month', ['engineer' => urlencode($monthData['engineer']), 'month' => urlencode($month)]) }}"
                                                                            class="btn btn-primary btn-sm w-100">
                                                                            <i class="fas fa-download"></i> Export Excel
                                                                        </a>
                                                                    @endif
                                                                @else
                                                                    <a href="{{ route('engineer_logs.month_view', ['engineer' => urlencode($monthData['engineer']), 'month' => urlencode($month)]) }}"
                                                                        class="btn btn-info btn-sm w-100">
                                                                        <i class="fas fa-eye"></i> View
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle fa-2x mb-2"></i>
                        <h5>No logs found</h5>
                        <p>There are no conveyance reports available at this time.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
