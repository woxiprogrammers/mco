<table>
    <thead>
    <tr>
        <th>Name</th>
        <th>Email</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $user)
        <tr>
            <td>{{ $user->sr_no }}</td>
            <td>{{ $user->name }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
