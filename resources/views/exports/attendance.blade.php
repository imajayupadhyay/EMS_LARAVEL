<table border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr style="background-color: #f97316; color: white;">
            <th>Employee</th>
            <th>Date</th>
            <th>Total Hours</th>
        </tr>
    </thead>
    <tbody>
        @foreach($attendance as $record)
            <tr>
                <td>{{ $record['user'] }}</td>
                <td>{{ $record['date'] }}</td>
                <td>{{ $record['hours'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<p style="margin-top: 20px; font-weight: bold;">
    Total Working Days this Month: {{ $totalWorkingDays }}
</p>
