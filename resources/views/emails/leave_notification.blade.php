<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leave Application Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            padding: 20px;
            color: #333;
        }
        .container {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            max-width: 600px;
            margin: auto;
        }
        h2 {
            color: #f97316;
        }
        p {
            margin: 8px 0;
        }
        strong {
            color: #444;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>New Leave Application Submitted</h2>
        <p><strong>Employee:</strong> {{ $leaveApplication->employee->first_name }} {{ $leaveApplication->employee->last_name }}</p>
        <p><strong>Leave Type:</strong> {{ $leaveApplication->leaveType->name }}</p>
        <p><strong>From:</strong> {{ $leaveApplication->start_date }}</p>
        <p><strong>To:</strong> {{ $leaveApplication->end_date }}</p>
        <p><strong>Reason:</strong> {{ $leaveApplication->reason }}</p>
    </div>
</body>
</html>
