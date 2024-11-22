<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Penalties</title>
    @include('partials.admin-link')
</head>

<body>

    @include('partials.admin-sidebar')
    @include('partials.admin-header')

    <div class="container">
        <div class="page-inner">
            <div class="page-header">
                <h3 class="fw-bold mb-3">List of Penalties</h3>
                <ul class="breadcrumbs mb-3">
                    <li class="nav-home">
                        <a href="{{ route('admin.admin-dashboard') }}">
                            <i class="icon-home"></i>
                        </a>
                    </li>
                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.motorcycles.manage-motorcycles') }}" class="fw-bold">Penalties</a>
                    </li>

                </ul>
            </div>
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="basic-datatables" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Driver Name</th>
                                        <th>Email</th>
                                        <th>Penalty Type</th>
                                        <th>Additional Payment</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($penalties as $penalty)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $penalty->driver->first_name ?? '' }}
                                                {{ $penalty->driver->last_name ?? '' }}</td>
                                            <td>{{ $penalty->driver->email ?? '' }}</td>
                                            <td>{{ $penalty->penalty_type }}</td>
                                            <td>{{ '₱' . number_format($penalty->additional_payment, 2) }}</td>
                                            <td>{{ $penalty->description }}</td>
                                            <td>
                                                @if ($penalty->status == 'Unpaid')
                                                    <div class="dropdown">
                                                        <button class="badge badge-primary dropdown-toggle"
                                                            type="button" id="statusDropdown" data-bs-toggle="dropdown"
                                                            aria-expanded="false">
                                                            {{ $penalty->status }}
                                                        </button>
                                                        <ul class="dropdown-menu" aria-labelledby="statusDropdown">
                                                            <li>
                                                                <form
                                                                    action="{{ route('penalties.updateStatus', $penalty->penalty_id) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <button class="dropdown-item" type="submit"
                                                                        name="status" value="Paid">
                                                                        Paid
                                                                    </button>
                                                                </form>
                                                            </li>
                                                            <li>
                                                                <form
                                                                    action="{{ route('penalties.updateStatus', $penalty->penalty_id) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <button class="dropdown-item" type="submit"
                                                                        name="status" value="Not Paid">
                                                                        Not Paid
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                @else
                                                    <span
                                                        class="badge @if ($penalty->status == 'Paid') badge-success @else badge-danger @endif">
                                                        {{ $penalty->status }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @include('partials.admin-footer')
</body>

</html>
<script>
    $(document).ready(function() {
        $("#basic-datatables").DataTable();
    });
</script>
