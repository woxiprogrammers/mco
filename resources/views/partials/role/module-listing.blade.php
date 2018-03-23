<tr>
    <th style="width: 25%;text-align: center"> Name </th>
    @foreach($permissionTypes as $permissionType)
        <th style="text-align: center">{{$permissionType['name']}}</th>
    @endforeach
</tr>

<tr>
    <th style="width: 25%;text-align: center"></th>
    {{$create = 'createPerm' }}
    {{$view = 'viewPerm'}}
    {{$edit = 'editPerm'}}
    {{$approve = 'approvePerm'}}
    {{$remove = 'removePerm'}}
    {{$count = 1}}
    @foreach($permissionTypes as $permissionType)
    @if ($count == 1)
        {{$append = $create}}
    @elseif ($count == 2)
        {{$append = $view}}
    @elseif ($count == 3)
     {{$append = $edit}}
    @elseif ($count == 4)
        {{$append = $approve}}
    @else
        {{$append = $remove}}
    @endif
    <th style="text-align: center">
        <input type="button" onclick="selectAll('{{$append}}','w')" value="Select All">
        <input type="button" onclick="UnSelectAll('{{$append}}','w')" value="UnSelect All">
    </th>
    {{$count++}}
    @endforeach
</tr>
<tr>
    <th style="font-size:150%;" colspan="{!! count($permissionTypes) + 1!!}">WEB</th>
</tr>
            @foreach($webModuleResponse as $data)
                <tr>
                     <td colspan="{!! count($permissionTypes) + 1!!}">
                             {{$data['module_name']}}
                            <input type="button" onclick="selectAll('{{strtolower($data['module_name'])}}','w')" value="Select All">
                            <input type="button" onclick="UnSelectAll('{{strtolower($data['module_name'])}}','w')" value="Unselect All">
                     </td>
                </tr>
            @foreach($data['submodules'] as $subModule)
                 <tr>
                     <td>
                            {{$subModule['submodule_name']}}
                     </td>
                     {{$count = 1}}
                     @foreach($permissionTypes as $permissionType)
                         @if ($count == 1)
                            {{$append = $create}}
                         @elseif ($count == 2)
                            {{$append = $view}}
                         @elseif ($count == 3)
                             {{$append = $edit}}
                         @elseif ($count == 4)
                            {{$append = $approve}}
                         @else
                            {{$append = $remove}}
                         @endif
                         <td style="text-align: center">
                             @if($userRole != 'superadmin' &&
                                 (
                                     ($subModule['submodule_name'] == 'Purchase Order Request' && $permissionType['name'] == 'Approve') ||
                                     ($subModule['submodule_name'] == 'Component Transfer' && ($permissionType['name'] == 'View' || $permissionType['name'] == 'Approve'))
                                 )
                             )
                                 <span>-</span>
                             @elseif(array_key_exists($permissionType['id'],$subModule['permissions']))
                                 @if(in_array($subModule['permissions'][$permissionType['id']],$roleWebPermissions))
                                     <input type="checkbox" name="web_permissions[]" class="{{strtolower($data['module_name'])}}-w {{$append}}-w" value="{{$subModule['permissions'][$permissionType['id']]}}" checked>
                                 @else
                                     <input type="checkbox" name="web_permissions[]" class="{{strtolower($data['module_name'])}}-w {{$append}}-w" value="{{$subModule['permissions'][$permissionType['id']]}}">
                                 @endif
                            @else
                                <span>-</span>
                            @endif
                         </td>
                     {{$count++}}
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
                <input type="button" onclick="selectAll('{{strtolower($data['module_name'])}}','m')" value="Select All">
                <input type="button" onclick="UnSelectAll('{{strtolower($data['module_name'])}}','m')" value="Unselect All">
            </td>
        </tr>
        @foreach($data['submodules'] as $subModule)
            <tr>
                <td>
                    {{$subModule['submodule_name']}}
                </td>
                {{$count = 1}}
                @foreach($permissionTypes as $permissionType)
                    @if ($count == 1)
                        {{$append = $create}}
                    @elseif ($count == 2)
                        {{$append = $view}}
                    @elseif ($count == 3)
                        {{$append = $edit}}
                    @elseif ($count == 4)
                        {{$append = $approve}}
                    @else
                        {{$append = $remove}}
                    @endif
                    <td style="text-align: center">
                        @if(array_key_exists($permissionType['id'],$subModule['permissions']))
                            @if(in_array($subModule['permissions'][$permissionType['id']],$roleMobilePermissions))
                            <input type="checkbox" name="mobile_permissions[]" class="{{strtolower($data['module_name'])}}-m {{$append}}-w" value="{{$subModule['permissions'][$permissionType['id']]}}" checked>
                            @else
                                <input type="checkbox" name="mobile_permissions[]" class="{{strtolower($data['module_name'])}}-m {{$append}}-w" value="{{$subModule['permissions'][$permissionType['id']]}}">
                            @endif
                        @else
                            <span>-</span>
                        @endif
                    </td>
                {{$count++}}
                @endforeach
            </tr>
        @endforeach
    @endforeach
@endif

<script type="text/javascript">
    function selectAll(data,type){
        var items=document.getElementsByClassName(data+'-'+type);
        for(var i=0; i<items.length; i++){
            if(items[i].type=='checkbox')
                items[i].checked=true;
        }
    }

    function UnSelectAll(data,type){
        var items=document.getElementsByClassName(data+'-'+type);
        for(var i=0; i<items.length; i++){
            if(items[i].type=='checkbox')
                items[i].checked=false;
        }
    }
</script>


