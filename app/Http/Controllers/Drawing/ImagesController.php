<?php

namespace App\Http\Controllers\Drawing;

use App\Client;
use App\DrawingCategory;
use App\DrawingCategorySiteRelation;
use App\DrawingImage;
use App\DrawingImageVersion;
use App\Project;
use App\ProjectSite;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ImagesController extends Controller
{
    public function __construct(){
        $this->middleware('custom.auth');
    }
    public function getManageView(Request $request){
        return view('drawing/images/manage');
    }
    public function getCreateView(Request $request){
        try{
             $categories = DrawingCategory::whereNull('drawing_category_id')->where('is_active',TRUE)->select('name','id')->get();
             $clients = Client::select('id','company')->get();
            return view('drawing/images/create')->with(compact('clients','categories'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
    public function getEditView(Request $request,$id,$site_id){
        try{
               $categories = DrawingCategory::whereNull('drawing_category_id')->where('is_active',TRUE)->select('name','id')->get();
               $clients = Client::select('id','company')->get();
               $main_category_id = DrawingCategory::where('id',$id)->pluck('drawing_category_id')->first();
               $drawing_category_site_relation_id = DrawingCategorySiteRelation::where('drawing_category_id',$id)
                                                    ->where('project_site_id',$site_id)
                                                    ->pluck('id')->toArray();
               $drawing_images_id = DrawingImage::whereIn('drawing_category_site_relation_id',$drawing_category_site_relation_id)->pluck('id')->toArray();
               $iterator = 0;
               $drawing_image_latest_version = array();
               $path = env('DRAWING_IMAGE_UPLOAD_PATH').DIRECTORY_SEPARATOR.sha1($site_id).DIRECTORY_SEPARATOR.sha1($id);
               foreach ($drawing_images_id as $value){
                   $drawing_image_latest_version[$iterator] =DrawingImageVersion:: where('drawing_image_id',$value)->orderBy('id','desc')->select('id','title','name')->first()->toArray();
                   $drawing_image_latest_version[$iterator]['encoded_name'] = $path.DIRECTORY_SEPARATOR.urlencode($drawing_image_latest_version[$iterator]['name']);
                   $iterator++;
               }
        }catch(\Exception $e){
            $data = [
                'action' => 'Edit',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return view('drawing/images/edit')->with(compact('project_site','path','drawing_image_latest_version','categories','clients'));
    }

    public function uploadTempDrawingImages(Request $request){
        try{
            $user_id = Auth::id();
            $drawingDirectoryName = sha1($user_id);
            $tempUploadPath = public_path().env('DRAWING_TEMP_IMAGE_UPLOAD');
            $tempImageUploadPath = $tempUploadPath.DIRECTORY_SEPARATOR.$drawingDirectoryName;
            /* Create Upload Directory If Not Exists */
            if (!file_exists($tempImageUploadPath)) {
                File::makeDirectory($tempImageUploadPath, $mode = 0777, true, true);
            }
            $extension = $request->file('file')->getClientOriginalExtension();
            $filename = explode(".",$request->name)[0].'#'.mt_rand(1,10000000000).sha1(time()).".{$extension}";
            $request->file('file')->move($tempImageUploadPath,$filename);
            $path = env('DRAWING_TEMP_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.$drawingDirectoryName.DIRECTORY_SEPARATOR.urlencode($filename);
            $response = [
                'jsonrpc' => '2.0',
                'result' => 'OK',
                'path' => $path
            ];
        }catch (\Exception $e){
            $response = [
                'jsonrpc' => '2.0',
                'error' => [
                    'code' => 101,
                    'message' => 'Failed to open input stream.',
                ],
                'id' => 'id'
            ];
        }
        return response()->json($response);
    }

    public function displayDrawingImages(Request $request){
        try{
            $path = $request->path;
            $count = $request->count;
            $random = mt_rand(1,10000000000);
        }catch (\Exception $e){
            $path = null;
            $count = null;
        }
        return view('partials.drawing.image')->with(compact('path','count','random'));
    }

    public function removeTempImage(Request $request){
        try{
            $sellerUploadPath = public_path().$request->path;
            File::delete($sellerUploadPath);
            return response(200);
        }catch(\Exception $e){
            return response(500);
        }
    }
    public function getProjects(Request $request){
        try{
            $projects = Project::where('client_id',$request->id)->select('id','name')->get();
            $response = [
                "projects" => $projects
            ];
            return Response()->json($response);
        }catch(\Exception $e){
            $data = [
                'action' => 'Listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
    public function getProjectSites(Request $request){
        try{
            $projects = ProjectSite::where('project_id',$request->id)->select('id','name')->get();
            $response = [
                "projects" => $projects
            ];
            return Response()->json($response);
        }catch(\Exception $e){
            $data = [
                'action' => 'Listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
    public function getSubCategories(Request $request){
        try{
            $projects = DrawingCategory::where('drawing_category_id',$request->id)->select('id','name')->get();
            $response = [
                "projects" => $projects
            ];
            return Response()->json($response);
        }catch(\Exception $e){
            $data = [
                'action' => 'Listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
    public function create(Request $request){
        try{
            $user_id = Auth::id();
            $directoryName = sha1($request->site_id).DIRECTORY_SEPARATOR.sha1($request->drawing_category_id);
            $tempImageUploadPath = public_path().env('DRAWING_TEMP_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.sha1($user_id);
            $imageUploadPath = public_path().env('DRAWING_IMAGE_UPLOAD_PATH').DIRECTORY_SEPARATOR.$directoryName;
            $workOrderImagesData = array();
            $files = $request->work_order_images;
            $workOrderImagesData['project_site_id']=$request->site_id;
            $workOrderImagesData['drawing_category_id']=$request->drawing_category_id;
            $drawing_categories_site_relation_id = DrawingCategorySiteRelation::insertGetId($workOrderImagesData);
            foreach($files as $image){
                $imageName = urldecode(basename($image['image_name']));
                $newTempImageUploadPath = $tempImageUploadPath.'/'.$imageName;
                $imageData['random_string'] = rand(10,100).sha1(time());
                $imageData['drawing_category_site_relation_id'] = $drawing_categories_site_relation_id;
                $drawing_image_id = DrawingImage::insertGetId($imageData);
                $imageVersionData['title'] = $image['title'];
                $imageVersionData['name'] = $imageName;
                $imageVersionData['drawing_image_id'] =$drawing_image_id;
                $drawing_image_version_id = DrawingImageVersion::insertGetId($imageVersionData);
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
            return redirect('/drawing/images/create');

        }catch(\Exception $e){
            $data = [
                'action' => 'create drawing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
    public function listing(Request $request){
        try{
            $subCategories = DrawingCategorySiteRelation::join('drawing_categories','drawing_categories.id','=','drawing_category_site_relations.drawing_category_id')
                ->select('drawing_categories.name','drawing_categories.id','drawing_categories.drawing_category_id','drawing_category_site_relations.project_site_id')->get();
            $iTotalRecords = count($subCategories);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($subCategories) : $request->length;
            $categories = array();
            for($iterator = 0,$pagination = $request->start; $iterator < $request->length && $iterator < count($subCategories); $iterator++,$pagination++ ){
                $records['data'][$iterator] = [
                    $subCategories[$pagination]['id'],
                    DrawingCategory::where('id',$subCategories[$pagination]['drawing_category_id'])->pluck('name')->first(),
                    $subCategories[$pagination]['name'],
                    '<div class="btn-group">
                            <a href="/drawing/images/edit/'.$subCategories[$pagination]['id'].DIRECTORY_SEPARATOR.$subCategories[$pagination]['project_site_id'].'"  >'
                               .'Edit
                            </a></div>'
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
            $responseStatus = 200;
            return response()->json($records);
        }catch(\Exception $e){
            $data = [
                'action' => 'drawing listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}
