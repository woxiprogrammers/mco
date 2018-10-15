<?php
/**
 * Created by Ameya Joshi
 * Date: 23/5/17
 */

namespace App\Http\Controllers\CustomTraits;
use App\Category;
use App\Http\Requests\CategoryRequest;
use App\InventoryComponent;
use App\InventoryComponentTransfers;
use App\InventoryTransferTypes;
use App\Material;
use App\MaterialRequestComponentTypes;
use App\Month;
use App\ProjectSite;
use App\UnitConversion;
use App\Year;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait CategoryTrait{

    public function getCreateView(Request $request){
        try{
            $year = new Year();
            $month = new Month();
            $inventoryComponentTransfer = new InventoryComponentTransfers();
            $inventoryComponent = new InventoryComponent();
            $inventoryTransferType = new InventoryTransferTypes();
            $yearSlug = '2018';
                $thisYear = $year->where('slug',$yearSlug)->first();
                $months = $month/*->where('id',6)*/->orderBy('id','asc')->get();
            $projectSite = new ProjectSite();
            $projectSites = $projectSite->where('id',23)->get();
            $data = array();
            $inTransferTypeIds = $inventoryTransferType->where('type','IN')->pluck('id')->toArray();
            foreach($projectSites as $projectSite) {
                foreach ($months as $thisMonth) {
                    $totalRentForMonth = $noofDaysUsedForMonth = 0;
                    $monthFirstDay = date('Y-m-d H:i:s', mktime(0, 0, 0, $thisMonth['id'], 1, $thisYear['slug']));
                    $monthLastDay = date('Y-m-t H:i:s', mktime(23, 59, 59, $thisMonth['id'], 1, $thisYear['slug']));
                    $inventoryComponentTransfers = $inventoryComponent->join('inventory_component_transfers', 'inventory_component_transfers.inventory_component_id'
                        , '=', 'inventory_components.id')
                        ->where('inventory_components.is_material', false)
                        ->where('inventory_components.project_site_id', $projectSite['id'])
                        ->whereMonth('inventory_component_transfers.created_at', $thisMonth['id'])
                        ->whereYear('inventory_component_transfers.created_at', $thisYear['slug'])
                        ->where('inventory_component_transfers.inventory_component_id',953)
                        ->orderBy('inventory_component_transfers.created_at', 'asc')
                        ->get();
                    $inventoryComponentData = $inventoryComponentTransfers->groupBy(function ($inventoryComponentTransfer) {
                        return $inventoryComponentTransfer['inventory_component_id'];
                    });
                    foreach ($inventoryComponentData as $inventoryComponentId => $inventoryComponentTransferData) {
                        $highestRentForMonth = $inventoryComponentTransferData->max('rate_per_unit');
                        $carryForwardQuantity = 0;
                        $count = count($inventoryComponentTransferData);
                        for ($iterator = 0; $iterator < $count; $iterator++) {
                            //$carryForwardQuantity += $inventoryComponentTransferData[$iterator]['quantity'];
                            if ((strtotime($inventoryComponentTransferData[$iterator]['created_at']) != strtotime($monthFirstDay)) && $carryForwardQuantity != 0) {
                                Log::info('inside if');
                                $noOfDays = date_diff(Carbon::parse($monthFirstDay), $inventoryComponentTransferData[$iterator]['created_at'])->d;
                                $noofDaysUsedForMonth += $noOfDays;
                                if(in_array($inventoryComponentTransferData[$iterator]['transfer_type_id'],$inTransferTypeIds)){
                                    $carryForwardQuantity += $inventoryComponentTransferData[$iterator]['quantity'];
                                }else{
                                    $carryForwardQuantity = $carryForwardQuantity - $inventoryComponentTransferData[$iterator]['quantity'];
                                }
                                $totalRentForMonth += ($carryForwardQuantity * $highestRentForMonth * $noOfDays);
                            } elseif (($iterator + 1) < $count) {
                                Log::info('inside else if');
                                $noOfDays = date_diff($inventoryComponentTransferData[$iterator]['created_at'], $inventoryComponentTransferData[$iterator + 1]['created_at'])->d;
                                $noofDaysUsedForMonth += $noOfDays;
                                if(in_array($inventoryComponentTransferData[$iterator]['transfer_type_id'],$inTransferTypeIds)){
                                    $carryForwardQuantity += $inventoryComponentTransferData[$iterator]['quantity'];
                                }else{
                                    $carryForwardQuantity = $carryForwardQuantity - $inventoryComponentTransferData[$iterator]['quantity'];
                                }
                                $totalRentForMonth += ($carryForwardQuantity * $highestRentForMonth * $noOfDays);
                            } else {
                                Log::info('inside else');
                                $noOfDays = date_diff($inventoryComponentTransferData[$iterator]['created_at'], Carbon::parse($monthLastDay))->d;
                                $noofDaysUsedForMonth += $noOfDays;
                                $carryForwardQuantity += $inventoryComponentTransferData[$iterator]['quantity'];
                                if(in_array($inventoryComponentTransferData[$iterator]['transfer_type_id'],$inTransferTypeIds)){
                                    $carryForwardQuantity += $inventoryComponentTransferData[$iterator]['quantity'];
                                }else{
                                    $carryForwardQuantity = $carryForwardQuantity - $inventoryComponentTransferData[$iterator]['quantity'];
                                }
                                $totalRentForMonth += ($carryForwardQuantity * $highestRentForMonth * $noOfDays);
                            }
                        }
                        $data[$projectSite['name']][$thisMonth['name']][$inventoryComponentId]['rent_per_month'] = $highestRentForMonth;
                        $data[$projectSite['name']][$thisMonth['name']][$inventoryComponentId]['days_used'] = $noofDaysUsedForMonth;
                        $data[$projectSite['name']][$thisMonth['name']][$inventoryComponentId]['rent'] = $totalRentForMonth;
                        $data[$projectSite['name']][$thisMonth['name']][$inventoryComponentId]['carry_forward_quantity'] = $carryForwardQuantity;
                        //save $carryForwardQuantity , $highestRentForMonth, $rentPerMonth
                    }
                }
            }
            dd($data);
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

    public function createCategory(CategoryRequest $request){
        try{
            $data['name'] = ucwords(trim($request['name']));
            $data['is_active'] = false;
            $data['is_miscellaneous'] = $request['is_miscellaneous'];
            $category = Category::create($data);
            $request->session()->flash('success', 'Category Created successfully.');
            return redirect('/category/manage');
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

    public function editCategory(CategoryRequest $request, $category){
        try{
            $data['name'] = ucwords(trim($request['name']));
            $data['is_miscellaneous'] = $request['is_miscellaneous'];
            $query = Category::where('id',$request['id'])->update($data);
            $request->session()->flash('success', 'Category Edited successfully.');
            return redirect('/category/manage');
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
            $user = Auth::user();
            if($request->has('search_name')){
                $categoriesData = Category::where('name','ilike','%'.$request->search_name.'%')->orderBy('name','asc')->get()->toArray();
            }else{
                $categoriesData = Category::orderBy('name','asc')->get()->toArray();
            }
            $iTotalRecords = count($categoriesData);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($categoriesData) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($categoriesData); $iterator++,$pagination++ ){
                if($categoriesData[$pagination]['is_active'] == true){
                    $category_status = '<td><span class="label label-sm label-success"> Enabled </span></td>';
                    $status = 'Disable';
                }else{
                    $category_status = '<td><span class="label label-sm label-danger"> Disabled</span></td>';
                    $status = 'Enable';
                }
                if($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('approve-category')){
                    $records['data'][$iterator] = [
                        $categoriesData[$pagination]['name'],
                        $category_status,
                        date('d M Y',strtotime($categoriesData[$pagination]['created_at'])),
                        '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">
                                <li>
                                    <a href="/category/edit/'.$categoriesData[$pagination]['id'].'">
                                    <i class="icon-docs"></i> Edit </a>
                                </li>
                                <li>
                                    <a href="/category/change-status/'.$categoriesData[$pagination]['id'].'">
                                        <i class="icon-tag"></i> '.$status.' </a>
                                </li>
                            </ul>
                        </div>'
                    ];
                }else{
                    $records['data'][$iterator] = [
                        $categoriesData[$pagination]['name'],
                        $category_status,
                        date('d M Y',strtotime($categoriesData[$pagination]['created_at'])),
                        '<div class="btn-group">
                            <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                Actions
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-left" role="menu">
                                <li>
                                    <a href="/category/edit/'.$categoriesData[$pagination]['id'].'">
                                    <i class="icon-docs"></i> Edit </a>
                                </li>
                            </ul>
                        </div>'
                    ];
                }
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        }catch(\Exception $e){
            $records = array();
            $data = [
                'action' => 'Get Category Listing',
                'params' => $request->all(),
                'exception'=> $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($records);
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

    public function checkCategoryName(Request $request){
        try{
            $categoryName = $request->name;
            if($request->has('category_id')){
                $nameCount = Category::where('name','ilike',$categoryName)->where('id','!=',$request->category_id)->count();
            }else{
                $nameCount = Category::where('name','ilike',$categoryName)->count();
            }
            if($nameCount > 0){
                return 'false';
            }else{
                return 'true';
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Check Category name',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

}