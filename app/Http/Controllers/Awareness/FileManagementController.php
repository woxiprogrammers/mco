<?php

namespace App\Http\Controllers\Awareness;

use App\AwarenessFiles;
use App\AwarenessMainCategory;
use App\AwarenessSubCategory;
use App\User;
use Dompdf\Exception;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class FileManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('custom.auth')->except('categoryManagementListing');
    }
    public function getManageView(Request $request){
        try{
            $main_categories = AwarenessMainCategory::select('id','name')->get();
            return view('awareness.file-management.manage')->with(compact('main_categories'));
        }catch(\Exception $e){
            $data = [
                'action' => 'manage view',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
    public function getCategoryCreateView(Request $request){
        try{
            $main_categories = AwarenessMainCategory::where('is_active', true)->select('id','name')->get();
            return view('awareness.file-management.create')->with(compact('main_categories'));
        }catch(\Exception $e){
            $data = [
                'action' => 'create view',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
    public function getMainCategories(Request $request,$id){
        try{
            $sub_categories = AwarenessSubCategory::where('awareness_main_category_id',$id)->where('is_active', true)->select('id','name')->get()->toArray();
            $sub_category_dropdown = array();
            $iterator = 0;
            foreach($sub_categories as $sub_category){
                $sub_category_dropdown[$iterator] = '<option value='.$sub_category['id'].'>' . $sub_category['name'].'</option';
                $iterator++;
            }
            return response()->json($sub_category_dropdown);
        }catch(\Exception $e){
            $data = [
                'action' => 'create view',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);

        }
    }
    public function uploadFiles(Request $request){
        try{
            $user_id = Auth::user();
            $tempUploadPath = public_path().env('AWARENESS_TEMP_FILE_UPLOAD').DIRECTORY_SEPARATOR.sha1($user_id['id']);
            $tempImageUploadPath = $tempUploadPath;
            /* Create Upload Directory If Not Exists */
            if (!file_exists($tempImageUploadPath)) {
                File::makeDirectory($tempImageUploadPath, $mode = 0777, true, true);
            }
            $extension = $request->file('file')->getClientOriginalExtension();
            $filename = explode(".",$request->name)[0].'#'.sha1(time()).".{$extension}";
            $request->file('file')->move($tempImageUploadPath,$filename);
            $path = env('AWARENESS_TEMP_FILE_UPLOAD').DIRECTORY_SEPARATOR.sha1($user_id['id']).DIRECTORY_SEPARATOR.urlencode($filename);
            $response = [
                'jsonrpc' => '2.0',
                'result' => 'OK',
                'path' => $path
            ];
        }catch(\Exception $e){
            $response = [
                'jsonrpc' => '2.0',
                'error' => [
                    'code' => 101,
                    'message' => $e->getMessage(),
                ],
                'id' => 'id'
            ];
        }
        return response()->json($response);
    }
    public function displayFiles(Request $request){
        try{
            $path = $request->path;
            $count = $request->count;
            $random = mt_rand(1,10000000000);
        }catch (\Exception $e){
            $path = null;
            $count = null;
        }
        return view('partials.awareness.awareness-files')->with(compact('path','count','random'));
    }
    public function create(Request $request){
        try{
            $user_id = Auth::user();
            $directoryName = sha1($request->main_category_id).DIRECTORY_SEPARATOR.sha1($request->sub_category_sub);
            $tempImageUploadPath = public_path().env('AWARENESS_TEMP_FILE_UPLOAD').DIRECTORY_SEPARATOR.sha1($user_id['id']);
            $imageUploadPath = public_path().env('AWARENESS_FILE_UPLOAD').DIRECTORY_SEPARATOR.$directoryName;
            $workOrderImagesData = array();
            if($request->has('awareness_files')){
                $files = $request->awareness_files;
                foreach($files as $image){
                    $imageName = urldecode(basename($image['image_name']));
                    $newTempImageUploadPath = $tempImageUploadPath.'/'.$imageName;
                    $workOrderImagesData['file_name'] = $imageName;
                    $workOrderImagesData['awareness_main_category_id']=$request->main_category_id;
                    $workOrderImagesData['awareness_sub_category_id']=$request->sub_category_sub;
                    AwarenessFiles::create($workOrderImagesData);
                    if (!file_exists($imageUploadPath)) {
                        File::makeDirectory($imageUploadPath, $mode = 0777, true, true);
                    }
                    if(File::exists($newTempImageUploadPath)){
                        $imageUploadNewPath = $imageUploadPath.DIRECTORY_SEPARATOR.$imageName;
                        File::move($newTempImageUploadPath,$imageUploadNewPath);
                    }
                }
                if(count(scandir($tempImageUploadPath)) <= 2){
                    rmdir($tempImageUploadPath);
                }
                $request->session()->flash('success','Data Saved successfully.');
            }else{
                $request->session()->flash('error','Please add atleast one file');
            }
            return redirect('/awareness/file-management/create');
        }catch(\Exception $e){
            $data = [
                'action' => 'create view',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);

        }
    }
    public function getSubCategories(Request $request){
        try{
            $sub_categories = AwarenessSubCategory::where('awareness_main_category_id',$request->id)->select('id','name')->get();
            $response = [
              "categories" => $sub_categories
            ];
            return Response()->json($response);
        }catch(\Exception $e){
            $data = [
                'action' => 'Edit view',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);

        }
    }
    public function getSubCategoriesDetails(Request $request){
        try{
            $main_category_id = AwarenessSubCategory::where('id',$request->id)->pluck('awareness_main_category_id')->first();
            $path = env('AWARENESS_FILE_UPLOAD').DIRECTORY_SEPARATOR.sha1($main_category_id).DIRECTORY_SEPARATOR.sha1($request->id);
            $files = AwarenessFiles::where('awareness_main_category_id',$main_category_id)->where('awareness_sub_category_id',$request->id)->select('id','file_name')->get();
            $awareness_files = array();
            $iterator = 0;
            foreach ($files as $file){
                $awareness_files[$iterator]['id'] = $file['id'];
                $awareness_files[$iterator]['path'] = $path.DIRECTORY_SEPARATOR.urlencode($file['file_name']);
                $iterator++;
            }
            return view('partials.awareness.awareness-edit')->with(compact('awareness_files'));
        }catch (Exception $e){
            $status = 500;
            $message = "Fail";
            $data = [
                'action' => 'Get Main Categories',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
        }
    }
    public function edit(Request $request){
        try{
            $user_id = Auth::user();
            $query1 = AwarenessFiles::where('awareness_main_category_id',$request->main_category_id)->where('awareness_sub_category_id',$request->sub_category_id)->delete();
            $directoryName = sha1($request->main_category_id).DIRECTORY_SEPARATOR.sha1($request->sub_category_id);
            $tempImageUploadPath = public_path().env('AWARENESS_TEMP_FILE_UPLOAD').DIRECTORY_SEPARATOR.sha1($user_id['id']);
            $imageUploadPath = public_path().env('AWARENESS_FILE_UPLOAD').DIRECTORY_SEPARATOR.$directoryName;
            $workOrderImagesData = array();
            $files = $request->awareness_files;
            foreach($files as $image){
                $imageName = urldecode(basename($image['image_name']));
                $newTempImageUploadPath = $tempImageUploadPath.'/'.$imageName;
                $workOrderImagesData['file_name'] = $imageName;
                $workOrderImagesData['awareness_main_category_id']=$request->main_category_id;
                $workOrderImagesData['awareness_sub_category_id']=$request->sub_category_id;
                AwarenessFiles::create($workOrderImagesData);
                if (!file_exists($imageUploadPath)) {
                    File::makeDirectory($imageUploadPath, $mode = 0777, true, true);
                }
                if(File::exists($newTempImageUploadPath)){
                    $imageUploadNewPath = $imageUploadPath.DIRECTORY_SEPARATOR.$imageName;
                    File::move($newTempImageUploadPath,$imageUploadNewPath);
                }
            }
            if(count(scandir($tempImageUploadPath)) <= 2){
                rmdir($tempImageUploadPath);
            }
            $request->session()->flash('success','Data Saved successfully.');
            return redirect('/awareness/file-management/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'create view',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}
