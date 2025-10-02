<h2 style="font-family: Arial, sans-serif; color: #f97316; margin-bottom: 15px;">
    Attendance Report - {{ $startDate }}
</h2>

<table border="1" cellspacing="0" cellpadding="6" style="border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size: 11px;">
    <thead>
        <tr style="background-color: #f97316; color: white;">
            <th style="text-align: left; min-width: 150px;">Employee Name</th>
            <th style="text-align: left; min-width: 100px;">Department</th>
            <th style="text-align: left; min-width: 100px;">Designation</th>
            @foreach($dates as $date)
                <th style="text-align: center; min-width: 40px;">{{ \Carbon\Carbon::parse($date)->format('d') }}</th>
            @endforeach
            <th style="text-align: center; min-width: 80px;">Total Days</th>
            <th style="text-align: center; min-width: 80px;">Present Days</th>
        </tr>
        <tr style="background-color: #fed7aa; font-size: 10px;">
            <th colspan="3" style="text-align: left;">Date</th>
            @foreach($dates as $date)
                <th style="text-align: center;">{{ \Carbon\Carbon::parse($date)->format('D') }}</th>
            @endforeach
            <th colspan="2" style="text-align: center;">Summary</th>
        </tr>
    </thead>
    <tbody>
        @foreach($attendanceMatrix as $row)
            <tr style="background-color: {{ $loop->iteration % 2 == 0 ? '#f9fafb' : 'white' }};">
                <td style="font-weight: 500;">{{ $row['employee_name'] }}</td>
                <td>{{ $row['department'] }}</td>
                <td>{{ $row['designation'] }}</td>
                @foreach($dates as $date)
                    @php
                        $status = $row['attendance'][$date];
                        $bgColor = '#fee2e2'; // Absent - red
                        $textColor = '#991b1b';
                        $displayText = 'A';

                        if ($status === 'Present') {
                            $bgColor = '#d1fae5'; // Present - green
                            $textColor = '#065f46';
                            $displayText = 'P';
                        }
                    @endphp
                    <td style="text-align: center; background-color: {{ $bgColor }}; color: {{ $textColor }}; font-weight: 600;">
                        {{ $displayText }}
                    </td>
                @endforeach
                <td style="text-align: center; font-weight: 600;">{{ $row['total_days'] }}</td>
                <td style="text-align: center; font-weight: 600; background-color: #d1fae5; color: #065f46;">{{ $row['present_days'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div style="margin-top: 20px; font-family: Arial, sans-serif; font-size: 12px;">
    <p style="margin: 5px 0;"><strong>Legend:</strong></p>
    <p style="margin: 5px 0;">
        <span style="background-color: #d1fae5; color: #065f46; padding: 3px 8px; border-radius: 3px; font-weight: 600;">P</span> = Present (4+ hours)
        &nbsp;&nbsp;
        <span style="background-color: #fee2e2; color: #991b1b; padding: 3px 8px; border-radius: 3px; font-weight: 600;">A</span> = Absent (&lt;4 hours)
    </p>
</div>
