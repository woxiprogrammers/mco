<tr>
    <th style="width: 25%"> Name </th>
    @foreach($permissionTypes as $permissionType)
        <th>{{$permissionType['name']}}</th>
    @endforeach
</tr>
<tr>
    <th style="font-size:150%;" colspan="{!! count($permissionTypes) + 1!!}">WEB</th>
</tr>
            @foreach($webModuleResponse as $data)
                <tr>
                     <td colspan="{!! count($permissionTypes) + 1!!}">
                             {{$data['module_name']}}
                     </td>
                </tr>
            @foreach($data['submodules'] as $subModule)
                 <tr>
                     <td>
                            {{$subModule['submodule_name']}}
                     </td>
                     @foreach($permissionTypes as $permissionType)
                         <td style="text-align: center">
                            @if(array_key_exists($permissionType['id'],$subModule['permissions']))
                                    <input type="checkbox" name="web_permissions[]" value="{{$subModule['permissions'][$permissionType['id']]}}">
                            @else
                                <span>-</span>
                             @endif
                         </td>
                     @endforeach
                 </tr>
            @endforeach
        @endforeach
@if(count($mobileModuleResponse) > 0)
    <tr>
        <th style="font-size:150%;" colspan="{!! count($permissionTypes) + 1!!}">MOBILE</th>
    </tr>
    @foreach($mobileModuleResponse as $data)
        <tr>
            <td colspan="{!! count($permissionTypes) + 1!!}">
                {{$data['module_name']}}
            </td>
        </tr>
        @foreach($data['submodules'] as $subModule)
            <tr>
                <td>
                    {{$subModule['submodule_name']}}
                </td>
                @foreach($permissionTypes as $permissionType)
                    <td style="text-align: center">
                        @if(array_key_exists($permissionType['id'],$subModule['permissions']))
                            <input type="checkbox" name="mobile_permissions[]" value="{{$subModule['permissions'][$permissionType['id']]}}">
                        @else
                            <span>-</span>
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach
    @endforeach
@endif


