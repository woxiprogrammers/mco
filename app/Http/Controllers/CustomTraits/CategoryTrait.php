<?php
/**
 * Created by Ameya Joshi
 * Date: 23/5/17
 */

namespace App\Http\Controllers\CustomTraits;
use App\AssetRentMonthlyExpenses;
use App\Category;
use App\Http\Requests\CategoryRequest;
use App\InventoryComponent;
use App\InventoryComponentTransfers;
use App\InventoryTransferTypes;
use App\Month;
use App\ProjectSite;
use App\Year;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait CategoryTrait{

    public function getData(Request $request){
        try{
            $year = new Year();
            $month = new Month();
            $projectSite = new ProjectSite();
            $inventoryComponentTransfer = new InventoryComponentTransfers();
            $inventoryComponent = new InventoryComponent();
            $inventoryTransferType = new InventoryTransferTypes();
            $assetRentMonthlyExpense = new AssetRentMonthlyExpenses();
                $yearSlug = '2018';
                $currentYear = date('Y');
                $thisYear = $year->where('slug',$yearSlug)->first();
                if($currentYear == $thisYear['slug']){
                    $months = $month->where('id','>',6)->orderBy('id','asc')->get();
                }else{
                    $months = $month->orderBy('id','asc')->get();
                }
                $totalMonths = $month->orderBy('id','asc')->get();
            $allProjectSiteIds = $projectSite->pluck('id');
            $inTransferTypeIds = $inventoryTransferType->where('type','IN')->pluck('id')->toArray();
            $outTransferTypeIds = $inventoryTransferType->where('type','OUT')->pluck('id')->toArray();
            foreach($allProjectSiteIds as $projectSiteId){
                $inventoryComponentData = $inventoryComponent->where('project_site_id',$projectSiteId)
                                            ->where('is_material',false)
                                            ->where('id',953)
                                            ->select('id','reference_id')->get();
                foreach ($inventoryComponentData as $thisInventoryComponent){
                    $assetId = $thisInventoryComponent['reference_id'];
                    $alreadyExistAssetRentMonthlyExpense = $assetRentMonthlyExpense
                                        ->where('year_id',$thisYear['id'])
                                        ->where('project_site_id',$projectSiteId)
                                        ->where('asset_id',$assetId)
                                        ->first();
                    //dd($months->toArray());
                    foreach ($months as $thisMonth){
                        $firstDayOfThisMonth = date('Y-m-d H:i:s', mktime(0, 0, 0, $thisMonth['id'], 1, $thisYear['slug']));
                        $lastDayOfThisMonth = date('Y-m-t H:i:s', mktime(23, 59, 59, $thisMonth['id'], 1, $thisYear['slug']));
                        $lastMonthData = array();
                        $thisMonthAssetRentMonthlyExpenseData = array();
                        if($thisMonth['slug'] == 'january'){
                            $lastYearAssetRentMonthlyExpenseData = $assetRentMonthlyExpense->where('project_site_id',$projectSite['id'])
                                                            ->where('year_id',$thisYear['slug']-1)
                                                            ->where('asset_id',$inventoryComponent['asset_id'])
                                                            ->first();
                            if($lastYearAssetRentMonthlyExpenseData != null){
                                $noOfDaysInJanuaryMonth = cal_days_in_month(CAL_GREGORIAN, 1, $thisYear['slug']);
                                $lastYearDecemberMonthData = json_decode($lastYearAssetRentMonthlyExpenseData['december']);
                                $lastMonthData['rent_per_day_per_quantity'] = $lastYearDecemberMonthData->rent_per_day_per_quantity;
                                $lastMonthData['days_used'] = ($lastYearDecemberMonthData->carry_forward_quantity == 0) ? 0 : $noOfDaysInJanuaryMonth;
                                $lastMonthData['quantity_used'] = $lastYearDecemberMonthData->carry_forward_quantity;
                                $lastMonthData['rent_for_month'] = ($lastMonthData['rent_per_day_per_quantity'] * $lastMonthData['days_used'] * $lastMonthData['quantity_used']);
                                $lastMonthData['carry_forward_quantity'] = $lastYearDecemberMonthData->carry_forward_quantity;
                            }else{
                                $lastMonthData['rent_per_day_per_quantity'] = 0;
                                $lastMonthData['quantity_used'] = 0;
                                $lastMonthData['days_used'] = 0;
                                $lastMonthData['rent_for_month'] = 0;
                                $lastMonthData['carry_forward_quantity'] = 0;
                            }
                        }else{
                            if($alreadyExistAssetRentMonthlyExpense != null){
                                $lastMonthId = $thisMonth['id']-1;
                                $lastMonthName = $totalMonths->where('id',$lastMonthId)->pluck('slug')->first();
                                $noOfDaysInThisMonth = cal_days_in_month(CAL_GREGORIAN, $thisMonth['id'], $thisYear['slug']);
                                $lastMonthData = json_decode($alreadyExistAssetRentMonthlyExpense[$lastMonthName]);
                                $lastMonthData['rent_per_day_per_quantity'] = $lastMonthData->rent_per_day_per_quantity;
                                $lastMonthData['days_used'] = ($lastMonthData->carry_forward_quantity == 0) ? 0 : $noOfDaysInThisMonth;
                                $lastMonthData['quantity_used'] = $lastMonthData->carry_forward_quantity;
                                $lastMonthData['rent_for_month'] = ($lastMonthData['rent_per_day_per_quantity'] * $lastMonthData['days_used'] * $lastMonthData['quantity_used']);
                                $lastMonthData['carry_forward_quantity'] = $lastMonthData->carry_forward_quantity;
                            }else{
                                $lastMonthData['rent_per_day_per_quantity'] = 0;
                                $lastMonthData['quantity_used'] = 0;
                                $lastMonthData['days_used'] = 0;
                                $lastMonthData['rent_for_month'] = 0;
                                $lastMonthData['carry_forward_quantity'] = 0;
                            }
                        }
                        $inventoryComponentTransfers = $inventoryComponentTransfer
                            ->where('inventory_component_id',$thisInventoryComponent['id'])
                            //->whereMonth('created_at', 9)
                            ->whereMonth('created_at', 7)
                            //->whereMonth('created_at', $thisMonth['id'])
                            ->whereYear('created_at', $thisYear['slug'])
                            ->orderBy('created_at','asc')
                            ->get();
                        $inventoryComponentTransferGroupByDateData = $inventoryComponentTransfers->sortBy('created_at')->groupBy(function($transactionsData) {
                            return Carbon::parse($transactionsData->created_at)->format('Y-m-d');
                        });
                      //  $inventoryComponentTransferGroupByDateData = $inventoryComponentTransferGroupByDateData->toArray();
                        $thisMonthAssetRentMonthlyExpenseData['rent_per_day_per_quantity'] = $thisMonthAssetRentMonthlyExpenseData['quantity_used'] = 0;
                        $thisMonthAssetRentMonthlyExpenseData['days_used'] = 0;
                        $thisMonthAssetRentMonthlyExpenseData['rent_for_month'] = 0;
                        $thisMonthAssetRentMonthlyExpenseData['carry_forward_quantity'] = 0;
                        //$thisMonthAssetRentMonthlyExpenseData['carry_forward_quantity'] = $lastMonthData['carry_forward_quantity'];
                        if(count($inventoryComponentTransferGroupByDateData) > 0){
                            $iterator = 0;
                            $carryForwardQuantity = $lastMonthData['carry_forward_quantity'];
                            $highestRentForMonth = $inventoryComponentTransfers->max('rate_per_unit');
                            $thisMonthAssetRentMonthlyExpenseData['rent_per_day_per_quantity'] = $highestRentForMonth;
                            foreach ($inventoryComponentTransferGroupByDateData as $date => $thisTransfer){
                                dd(next($inventoryComponentTransferGroupByDateData));
                                $parsedData = Carbon::parse($date);
                               // $noOfDays = ceil(abs(strtotime($lastDayOfThisMonth) - strtotime($inventoryComponentTransfers[$iterator+1]['created_at']))/86400);
                                if((date('d-m-y',strtotime($firstDayOfThisMonth)) != date('d-m-y',strtotime($parsedData))) && $lastMonthData['carry_forward_quantity'] != 0){
                                    $noOfDays = ceil(abs(strtotime($firstDayOfThisMonth) - strtotime($parsedData->subDay()))/86400);
                                    $thisMonthAssetRentMonthlyExpenseData['quantity_used'] += $carryForwardQuantity;
                                    $thisMonthAssetRentMonthlyExpenseData['days_used'] += $noOfDays;
                                    $thisMonthAssetRentMonthlyExpenseData['rent_for_month'] += $carryForwardQuantity * $highestRentForMonth * $noOfDays;
                                    $thisMonthAssetRentMonthlyExpenseData['carry_forward_quantity'] += $carryForwardQuantity;
                                }
                                if(($iterator + 1) < count($inventoryComponentTransferGroupByDateData)){
                                    $noOfDays = ceil(abs(strtotime($inventoryComponentTransfers[$iterator+1]['created_at']->subDay()) - strtotime($inventoryComponentTransfers[$iterator]['created_at']))/86400);
                                    $inQuantities = $thisTransfer->whereIn('transfer_type_id',$inTransferTypeIds)->sum('quantity');
                                    $outQuantities = $thisTransfer->whereIn('transfer_type_id',$outTransferTypeIds)->sum('quantity');
                                    $carryForwardQuantity = $carryForwardQuantity + $inQuantities - $outQuantities;
                                    $thisMonthAssetRentMonthlyExpenseData['quantity_used'] += $carryForwardQuantity;
                                    $thisMonthAssetRentMonthlyExpenseData['days_used'] += $noOfDays;
                                    $thisMonthAssetRentMonthlyExpenseData['rent_for_month'] += $carryForwardQuantity * $highestRentForMonth * $noOfDays;
                                    $thisMonthAssetRentMonthlyExpenseData['carry_forward_quantity'] += $carryForwardQuantity;
                                    $iterator++;
                                }

                            } // Transfer loop end
                        }else{
                            $thisMonthAssetRentMonthlyExpenseData = $lastMonthData;

                        }
                        //create or update
                    } //Month for loop end
                } // Asset Loop end
            } //Project Site Loop End
        }catch(\Exception $e){
            $data = [
                'action' => "Get asset Rent Data",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

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