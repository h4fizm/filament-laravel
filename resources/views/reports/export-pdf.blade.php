<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Evaluation</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; padding: 3rem; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        .header { text-align: center; font-size: 24px; font-weight: bold; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">Employee Performance Evaluation</div>

    <h2>Employee Details</h2>
    <table>
        <tr><th>Employee ID</th><td>{{ $review->employee->id }}</td></tr>
        <tr><th>Name</th><td>{{ $review->employee->name }}</td></tr>
        <tr><th>Email</th><td>{{ $review->employee->email }}</td></tr>
        <tr><th>Position</th><td>{{ ucfirst($review->employee->position) }}</td></tr>
        <tr><th>Contract Start Date</th><td>{{ $review->employee->contract->start_date ?? '-' }}</td></tr>
        <tr><th>Contract End Date</th><td>{{ $review->employee->contract->end_date ?? '-' }}</td></tr>
        <tr><th>Contract Status</th><td>{{ ucfirst($review->employee->contract->contract_status ?? '-') }}</td></tr>
    </table>

    <h2>Performance Metrics</h2>
    <table>
        <tr><th>Attendance</th><td>{{ $review->attendance }}</td></tr>
        <tr><th>Productivity</th><td>{{ $review->productivity }}</td></tr>
        <tr><th>Discipline</th><td>{{ $review->discipline }}</td></tr>
        <tr><th>Total Score</th><td>{{ $review->total_score }}</td></tr>
        <tr>
        <th>Evaluation Notes</th>
            <td>{!! html_entity_decode($review->evaluation) !!}</td>
        </tr>
    </table>
</body>
</html>
