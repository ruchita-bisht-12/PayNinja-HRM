<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Leave Balances Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .title {
            text-align: center;
            margin-bottom: 20px;
        }
        .date {
            text-align: right;
            margin-bottom: 20px;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="title">
        <h2>Leave Balances Report</h2>
    </div>
    <div class="date">
        Generated on: {{ date('Y-m-d H:i:s') }}
    </div>
    <table>
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    @foreach($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
