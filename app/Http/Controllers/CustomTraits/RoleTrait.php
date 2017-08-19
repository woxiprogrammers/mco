<?php
/**
 * Created by Ameya Joshi
 * Date: 23/5/17
 */

namespace App\Http\Controllers\CustomTraits;
use App\Role;
use App\Http\Requests\RoleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait RoleTrait{

    public function getCreateView(Request $request){
        try{
            return view('admin.role.create');
        }catch(\Exception $e){
            $data = [
                'action' => "Get role create view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getEditView(Request $request,$role){
        try{
            $role = $role->toArray();
            return view('admin.role.edit')->with(compact('role'));
        }catch(\Exception $e){
            $data = [
                'action' => "Get role edit view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getManageView(Request $request){
        try{
            return view('admin.role.manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get role manage view',
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createRole(RoleRequest $request){
        try{
            $data = $request->only('name','type');
            $data['name'] = ucwords(trim($data['name']));
            $role = Role::create($data);
            $request->session()->flash('success', 'Role Created successfully.');
            return redirect('/role/create');
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Role',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function editRole(RoleRequest $request, $role){
        try{
            $role->update(['name' => ucwords(trim($request->name))]);
            $request->session()->flash('success', 'Role Edited successfully.');
            return redirect('/role/edit/'.$role->id);
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Role',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function roleListing(Request $request){
        try{
            if($request->has('search_name')){
                $rolesData = Role::where('name','ilike','%'.$request->search_name.'%')->orderBy('name','asc')->get()->toArray();
            }else{
                $rolesData = Role::orderBy('name','asc')->get()->toArray();
            }
            $iTotalRecords = count($rolesData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($rolesData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($rolesData); $iterator++,$pagination++ ){
                $records['data'][$iterator] = [
                    $rolesData[$pagination]['name'],
                    ucfirst($rolesData[$pagination]['type']),
                    date('d M Y',strtotime($rolesData[$pagination]['created_at'])),
                    '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="/role/edit/'.$rolesData[$pagination]['id'].'">
                                <i class="icon-docs"></i> Edit </a>
                            </li>
                        </ul>
                    </div>'
                ];
            }

            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch(\Exception $e){
            $records = array();
            $data = [
                'action' => 'Get Role Listing',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($records);
    }

    public function checkRoleName(Request $request){
        try{
            $roleName = $request->name;
            if($request->has('role_id')){
                $nameCount = Role::where('name','ilike',$roleName)->where('id','!=',$request->role_id)->count();
            }else{
                $nameCount = Role::where('name','ilike',$roleName)->count();
            }
            if($nameCount > 0){
                return 'false';
            }else{
                return 'true';
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Check Role name',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

}