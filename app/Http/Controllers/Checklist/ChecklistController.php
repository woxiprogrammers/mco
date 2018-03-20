<?php

namespace App\Http\Controllers\CheckList;

use App\Category;
use App\ChecklistCategory;
use App\ChecklistCheckpoint;
use App\ChecklistCheckpointImages;
use App\Http\Controllers\CustomTraits\CheckListTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class ChecklistController extends Controller
{
    public function __construct()
    {
        $this->middleware('custom.auth');
    }
    public function getManageView(Request $request)
    {
        try {
            return view('checklist.structure.manage');
        } catch (\Exception $e) {
            $data = [
                'action' => "Get Check List manage view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getCreateView(Request $request)
    {
        try {
            $mainCategories = ChecklistCategory::whereNull('category_id')->where('is_active', true)->select('id','name')->get();
            return view('checklist.structure.create')->with(compact('mainCategories'));
        } catch (\Exception $e) {
            $data = [
                'action' => "Get check list create view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getSubCategories(Request $request){
        try{
            $subCategories = ChecklistCategory::where('category_id',$request->category_id)->where('is_active', true)->select('id','name')->get();
            $response = '<option value="">-- Select Sub Category --</option>';
            foreach($subCategories as $subCategory){
                $response .= '<option value="'.$subCategory['id'].'">'.$subCategory['name'].'</option>';
            }
            $status = 200;
        }catch(\Exception $e){
            $data = [
                'action' => 'Get checklist subcategories',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_decode($data));
            $status = 500;
            $response = '';
        }
        return response()->json($response,$status);
    }

    public function createStructure(Request $request){
        try {
            $checkpointData = [
                'checklist_category_id' => $request->sub_category_id
            ];
            foreach($request->checkpoints as $checkpointInfo){
                $checkpointData['description'] = $checkpointInfo['description'];
                $checkpointData['is_remark_required'] = ($checkpointInfo['is_mandatory'] == 'true')? true : false;
                $checkpoint = ChecklistCheckpoint::create($checkpointData);
                if(array_key_exists('images',$checkpointInfo)){
                    $checkpointImageData = [
                        'checklist_checkpoint_id' => $checkpoint->id
                    ];
                    foreach ($checkpointInfo['images'] as $imageData){
                        $checkpointImageData['caption'] = $imageData['caption'];
                        $checkpointImageData['is_required'] = ($imageData['is_required'] == 'true')? true : false;;
                        ChecklistCheckpointImages::create($checkpointImageData);
                    }
                }
            }
            $request->session()->flash('success','Checkpoints created successfully.');
            return redirect('/checklist/structure/manage');
        } catch (\Exception $e) {
            $data = [
                'action' => "Create Checklist Structure",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getCheckpointPartialView(Request $request){
        try{
            $index = $request->number_of_checkpoints;
            return view('partials.checklist.structure.checkpoints')->with(compact('index'));
        }catch(\Exception $e){
            $data = [
                'action' => "Get checkpoint partial view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function getCheckpointImagePartialView(Request $request){
        try{
            $index = $request->index;
            $noOfImages = $request->number_of_images;
            return view('partials.checklist.structure.image-table')->with(compact('index','noOfImages'));
        }catch(\Exception $e){
            $data = [
                'action' => "Get checkpoint image partial view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }

    public function structureListing(Request $request){
        try{
            $status = 200;
            $checklistCategory = ChecklistCategory::where('is_active',true)->whereNotNull('category_id')->orderBy('id','desc')->get();
            $records = array();
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $records["recordsFiltered"] = count($checklistCategory);
            $records['data'] = array();
            $end = $request->length < 0 ? count($checklistCategory) : $request->length;
            for($iterator = 0,$pagination = $request->start; $iterator < $end && $pagination < count($checklistCategory); $iterator++,$pagination++ ){
            $mainCategoryName = ChecklistCategory::where('id',$checklistCategory[$pagination]['category_id'])->pluck('name')->first();
                $records['data'][] = [
                    ($iterator+1),
                    $mainCategoryName,
                    $checklistCategory[$pagination]->name,
                    ChecklistCheckpoint::where('checklist_category_id',$checklistCategory[$pagination]->id)->count(),
                    '<a href="/checklist/structure/edit/'.$checklistCategory[$pagination]->id.'" class="btn blue">Edit</a>'
                ];
            }
        }catch(\Exception $e){
            $data = [
                'action' => "Get Checklist structure listing",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $status = 500;
            $records = array();
        }
        return response()->json($records,$status);
    }

    public function getStructureEditView(Request $request,$checklistCategory){
        try{
            return view('checklist.structure.edit')->with(compact('checklistCategory'));
        }catch(\Exception $e){
            $data = [
                'action' => "Get Checklist structure Edit View",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}


