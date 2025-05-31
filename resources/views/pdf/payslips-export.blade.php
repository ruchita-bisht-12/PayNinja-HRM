<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Employee Payslips Export</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 12px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
            font-size: 10px;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Employee Payslips</h1>
        <p>Generated on: {{ now()->format('d M, Y h:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Employee ID</th>
                <th>Employee Name</th>
                <th>Department</th>
                <th>Designation</th>
                <th class="text-right">Basic Salary</th>
                <th class="text-right">Gross Salary</th>
                <th class="text-right">Net Salary</th>
                <th>Effective From</th>
                <th>Effective To</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row['Employee ID'] ?? 'N/A' }}</td>
                    <td>{{ $row['Employee Name'] ?? 'N/A' }}</td>
                    <td>{{ $row['Department'] ?? 'N/A' }}</td>
                    <td>{{ $row['Designation'] ?? 'N/A' }}</td>
                    <td class="text-right">{{ $row['Basic Salary'] ?? '0.00' }}</td>
                    <td class="text-right">{{ $row['Gross Salary'] ?? '0.00' }}</td>
                    <td class="text-right">{{ $row['Net Salary'] ?? '0.00' }}</td>
                    <td>{{ $row['Effective From'] ?? 'N/A' }}</td>
                    <td>{{ $row['Effective To'] ?? 'Present' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">No data available</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Page {{ $pdf->getPage() }} of {!! $pdf->getNumPages() !!}</p>
    </div>
</body>
</html>
