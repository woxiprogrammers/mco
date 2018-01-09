    <table style="width:100%">
        <tr>
            <th >
                Component
            </th>
        <th>
            Vendor
        </th>
        </tr>
        @foreach($purchaseRequestComponents as $purchaseRequestComponent)
        <tr>
        <td >
            {{$purchaseRequestComponent['name']}}
        </td>
        <td >
            {{$purchaseRequestComponent['vendor']}}
        </td>
        </tr>
        @endforeach

    </table>
