<?php
/**
 * Created by Ameya Joshi
 * Date: 23/5/17
 */

namespace App\Http\Controllers\CustomTraits;
use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait CategoryTrait{

    public function getCreateView(Request $request){
        try{
            return view('admin.category.create');
        }catch(\Exception $e){
            $data = [
                'action' => "Get category create view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getEditView(Request $request,$category){
        try{
            $category = $category->toArray();
            return view('admin.category.edit')->with(compact('category'));
        }catch(\Exception $e){
            $data = [
                'action' => "Get category edit view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getManageView(Request $request){
        try{
              return view('admin.category.manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Get Category manage view',
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createCategory(Request $request){
        try{
            $data = $request->only('name');
            $data['is_active'] = false;
            $category = Category::create($data);
            $request->session()->flash('success', 'Category Created successfully.');
            return redirect('/category/create');
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Category',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function editCategory(Request $request, $category){
        try{
            $category->update(['name' => $request->name]);
            $request->session()->flash('success', 'Category Edited successfully.');
            return redirect('/category/edit/'.$category->id);
        }catch(\Exception $e){
            $data = [
                'action' => 'Create Category',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function categoryListing(Request $request){
        try{
            $categoriesData = Category::orderBy('id','asc')->get()->toArray();
            $iTotalRecords = count($categoriesData);
            $records = array();
            $iterator = 0;
            foreach($categoriesData as $category){
                if($category['is_active'] == true){
                    $category_status = '<td><span class="label label-sm label-success"> Enabled </span></td>';
                    $status = 'Disable';
                }else{
                    $category_status = '<td><span class="label label-sm label-danger"> Disabled</span></td>';
                    $status = 'Enable';
                }
                $records['data'][$iterator] = [
                    $category['name'],
                    $category_status,
                    date('d M Y',strtotime($category['created_at'])),
                    '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="/category/edit/'.$category['id'].'">
                                    <i class="icon-docs"></i> Edit </a>
                            </li>
                            <li>
                                <a href="/category/change-status/'.$category['id'].'">
                                    <i class="icon-tag"></i> '.$status.' </a>
                            </li>
                        </ul>
                    </div>'
                ];
                $iterator++;
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch(\Exception $e){
            $records = array();
            $data = [
                'action' => 'Create Category',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }

        return response()->json($records,200);
    }

    public function changeCategoryStatus(Request $request, $category){
        try{
            $newStatus = (boolean)!$category->is_active;
            $category->update(['is_active' => $newStatus]);
            $request->session()->flash('success', 'Category Status changed successfully.');
            return redirect('/category/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Change category status',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

}