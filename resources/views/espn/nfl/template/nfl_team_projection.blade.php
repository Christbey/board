<!DOCTYPE html>
<html>
<head>
    <title>NFL Team Projection</title>
</head>
<body>
<h1>NFL Team Projection</h1>
@if(!empty($projectionData))
<table border='1'>
    <thead>
    <tr>
        <th>Category</th>
        <th>Value</th>
    </tr>
    </thead>
    <tbody>
    @foreach($projectionData as $key => $value)
    <tr>
        <td>{{ ucfirst($key) }}</td>
        <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
    </tr>
    @endforeach
    </tbody>
</table>
@else
<p>No data available.</p>
@endif
</body>
</html>
