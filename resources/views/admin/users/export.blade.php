@php
    $siteName = \App\Helpers\SettingsHelper::get('site_name', config('app.name', 'E-Commerce'));

    $statusLabels = [
        '' => 'All statuses',
        'verified' => 'Verified',
        'unverified' => 'Needs review',
    ];

    $roleLabels = [
        '' => 'All users',
        'customer' => 'Customers',
    ];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $siteName }} Users Export</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #0f172a;
            margin: 24px;
        }

        .sheet-title {
            font-size: 26px;
            font-weight: 700;
            color: #b91c1c;
            margin-bottom: 6px;
        }

        .sheet-subtitle {
            font-size: 13px;
            color: #475569;
            margin-bottom: 18px;
        }

        .summary-table,
        .users-table,
        .filters-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        .summary-table td,
        .filters-table td {
            border: 1px solid #dbe3ef;
            padding: 10px 12px;
            font-size: 12px;
        }

        .summary-label,
        .filter-label {
            background: #f8fafc;
            font-weight: 700;
            color: #334155;
            width: 160px;
        }

        .summary-value {
            font-weight: 700;
            color: #0f172a;
        }

        .users-table th {
            background: #111827;
            color: #ffffff;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 12px 10px;
            border: 1px solid #1f2937;
            text-align: left;
        }

        .users-table td {
            border: 1px solid #dbe3ef;
            padding: 10px;
            font-size: 12px;
            vertical-align: top;
        }

        .users-table tr:nth-child(even) td {
            background: #f8fafc;
        }

        .section-title {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            margin: 14px 0 8px;
        }

        .muted {
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="sheet-title">{{ $siteName }} Customer Report</div>
    <div class="sheet-subtitle">
        Generated on {{ $generatedAt->format('F j, Y \a\t g:i A') }}
    </div>

    <div class="section-title">Report Filters</div>
    <table class="filters-table">
        <tr>
            <td class="filter-label">Account Type</td>
            <td>{{ $roleLabels[$roleFilter] ?? ucfirst($roleFilter) }}</td>
            <td class="filter-label">Status</td>
            <td>{{ $statusLabels[$statusFilter] ?? ucfirst(str_replace('_', ' ', $statusFilter)) }}</td>
        </tr>
        <tr>
            <td class="filter-label">Search</td>
            <td colspan="3">{{ $search !== '' ? $search : 'No search applied' }}</td>
        </tr>
    </table>

    <div class="section-title">Summary</div>
    <table class="summary-table">
        <tr>
            <td class="summary-label">Total Records</td>
            <td class="summary-value">{{ $summary['total'] }}</td>
            <td class="summary-label">Customers</td>
            <td class="summary-value">{{ $summary['customers'] }}</td>
        </tr>

    </table>

    <div class="section-title">Contact Export</div>
    <table class="users-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Address</th>
                <th>Joined At</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone ?: 'N/A' }}</td>
                    <td>{{ ucfirst((string) $user->role) }}</td>
                    <td>{{ $user->address ?: 'N/A' }}</td>
                    <td>{{ optional($user->created_at)->format('Y-m-d H:i:s') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="muted">No records found for this export.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
