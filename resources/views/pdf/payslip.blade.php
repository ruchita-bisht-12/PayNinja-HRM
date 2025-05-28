<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Salary Slip - {{ $salary->employee->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .payslip-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .payslip-title {
            font-size: 18px;
            margin: 15px 0;
            text-transform: uppercase;
        }
        .employee-info, .company-info {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table th, .info-table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .info-table th {
            background-color: #f5f5f5;
            width: 30%;
        }
        .earnings-deductions {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .earnings-deductions th, .earnings-deductions td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: right;
        }
        .earnings-deductions th:first-child, .earnings-deductions td:first-child {
            text-align: left;
        }
        .earnings-deductions th {
            background-color: #f5f5f5;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .net-salary {
            font-size: 16px;
            color: #2c3e50;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 11px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .signature {
            margin-top: 50px;
            text-align: right;
        }
        .signature-line {
            border-top: 1px solid #333;
            display: inline-block;
            width: 200px;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="payslip-container">
        <div class="header">
            <div class="company-name">{{ 'PayNinja Technology Ltd' }}</div>
            <div>Flat No. 1003, 10th Floor, Nirmal Tower 26, Barakhamba Road, New Delhi – 110001</div>
            <div>Phone: +91 9999092616 | +91 9654540842 | Email: info@payninjahr.com</div>
        </div>

        <div class="payslip-title">Salary Slip for {{ date('F Y', strtotime($monthYear)) }}</div>
        
        <table class="info-table">
            <tr>
                <th>Employee Name</th>
                <td>{{ $salary->employee->name }}</td>
                <th>Employee ID</th>
                <td>{{ $salary->employee->employee_id ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Department</th>
                <td>{{ $salary->employee->department->name ?? 'N/A' }}</td>
                <th>Designation</th>
                <td>{{ $salary->employee->designation->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Joining Date</th>
                <td>{{ $salary->employee->joining_date ? \Carbon\Carbon::parse($salary->employee->joining_date)->format('d M, Y') : 'N/A' }}</td>
                <th>Payment Date</th>
                <td>{{ $generatedDate }}</td>
            </tr>
            <tr>
                <th>PAN Number</th>
                <td>{{ $salary->employee->pan_number ?? 'N/A' }}</td>
                <th>Bank Account</th>
                <td>{{ $salary->employee->bank_account_number ?? 'N/A' }}</td>
            </tr>
        </table>

        <table class="earnings-deductions">
            <tr>
                <th style="width: 50%;">Earnings</th>
                <th style="width: 25%;">Amount (₹)</th>
                <th style="width: 25%;">Deductions</th>
                <th style="width: 25%;">Amount (₹)</th>
            </tr>
            <tr>
                <td>Basic Salary</td>
                <td>{{ number_format($salary->basic_salary, 2) }}</td>
                <td>PF</td>
                <td>{{ number_format($salary->pf_deduction, 2) }}</td>
            </tr>
            <tr>
                <td>HRA</td>
                <td>{{ number_format($salary->hra, 2) }}</td>
                <td>ESI</td>
                <td>{{ number_format($salary->esi_deduction, 2) }}</td>
            </tr>
            <tr>
                <td>DA</td>
                <td>{{ number_format($salary->da, 2) }}</td>
                <td>Professional Tax</td>
                <td>{{ number_format($salary->professional_tax, 2) }}</td>
            </tr>
            <tr>
                <td>Other Allowances</td>
                <td>{{ number_format($salary->other_allowances, 2) }}</td>
                <td>Loan Deductions</td>
                <td>{{ number_format($salary->loan_deductions, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td><strong>Total Earnings</strong></td>
                <td><strong>{{ number_format($salary->gross_salary, 2) }}</strong></td>
                <td><strong>Total Deductions</strong></td>
                <td><strong>{{ number_format($salary->total_deductions, 2) }}</strong></td>
            </tr>
            <tr class="net-salary">
                <td colspan="3" style="text-align: right;"><strong>Net Salary Payable</strong></td>
                <td><strong>₹{{ number_format($salary->net_salary, 2) }}</strong></td>
            </tr>
        </table>

        <div class="footer">
            <p>This is a system generated payslip and does not require a signature.</p>
            <p>For any discrepancies, please contact the HR department within 7 days.</p>
        </div>
        
        <div class="signature">
            <div class="signature-line"></div>
            <div>Authorized Signatory</div>
        </div>
    </div>
</body>
</html>
