<table border="1" cellspacing="0" cellpadding="8" style="border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; font-size: 14px;">
    <thead>
        <tr style="background-color: #f97316; color: white;">
            <th style="text-align: left;">Employee</th>
            <th style="text-align: left;">Date</th>
            <th style="text-align: left;">Total Hours</th>
        </tr>
    </thead>
    <tbody>
        @foreach($attendance as $record)
            <tr>
                <td>{{ $record['employee'] }}</td>
                <td>{{ $record['date'] }}</td>
                <td>{{ $record['hours'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<p style="margin-top: 20px; font-weight: bold; font-size: 16px; color: #f97316;">
    Total Working Days this Month: {{ $totalWorkingDays }}
</p>
