<tr>
    <th style="width: 25%"> Name </th>
    <th> Create </th>
    <th> View</th>
    <th> Edit </th>
    <th> Approve</th>
    <th> Remove </th>
</tr>
@foreach($moduleResponse as $data)
    <tr>
        <td> {{$data['module_name']}}</td>
        <td><input type="checkbox"></td>
        <td><input type="checkbox"></td>
        <td><input type="checkbox"></td>
        <td><input type="checkbox"></td>
        <td><input type="checkbox"></td>
    </tr>
    @endforeach
