<table>
    <thead>
        <tr>
            <th>User</th>
            <th>Date</th>
            <th>Hours Worked</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($attendance as $row)
            <tr>
                <td>{{ $row['user'] }}</td>
                <td>{{ $row['date'] }}</td>
                <td>{{ $row['hours'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
