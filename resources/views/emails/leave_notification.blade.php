<!DOCTYPE html>
<html>
<head>
    <title>Leave Application</title>
</head>
<body>
    <h2>New Leave Application Submitted</h2>
    <p><strong>Employee:</strong> {{ $leaveApplication->user->name }}</p>
    <p><strong>Leave Type:</strong> {{ $leaveApplication->leaveType->name }}</p>
    <p><strong>From:</strong> {{ $leaveApplication->start_date }}</p>
    <p><strong>To:</strong> {{ $leaveApplication->end_date }}</p>
    <p><strong>Reason:</strong> {{ $leaveApplication->reason }}</p>
</body>
</html>
