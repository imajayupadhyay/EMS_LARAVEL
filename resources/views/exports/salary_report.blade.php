<table border="1" cellpadding="5" cellspacing="0">
  <thead style="background-color: #f97316; color: white;">
    <tr>
      <th>Name</th>
      <th>Department</th>
      <th>Designation</th>
      <th>Office Days</th>
      <th>Present Days</th>
      <th>Approved Leaves</th>
      <th>Assigned Leaves</th>
      <th>Balance</th>
    </tr>
  </thead>
  <tbody>
    @foreach($report as $item)
      <tr>
        <td>{{ $item['employee']->first_name }} {{ $item['employee']->last_name }}</td>
        <td>{{ $item['employee']->department->name ?? 'N/A' }}</td>
        <td>{{ $item['employee']->designation->name ?? 'N/A' }}</td>
        <td>{{ $officeDays }}</td>
        <td>{{ $item['present_days'] }}</td>
        <td>{{ $item['approved_leaves'] }}</td>
        <td>{{ $item['leave_assigned'] }}</td>
        <td>{{ $item['leave_balance'] }}</td>
      </tr>
    @endforeach
  </tbody>
</table>
