<?php

namespace App\Http\Controllers\Drawing;

use App\Client;
use App\DrawingCategory;
use App\DrawingCategorySiteRelation;
use App\DrawingImage;
use App\DrawingImageComment;
use App\DrawingImageVersion;
use App\Project;
use App\ProjectSite;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

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
            return view('drawing/images/create')->with(compact('categories'));
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
            $site = ProjectSite::where('id',$site_id)->select('id','project_id','name')->first()->toArray();
            $project = Project::where('id',$site['project_id'])->select('id','name','client_id')->first()->toArray();
            $client = Client::where('id',$project['client_id'])->select('id','company')->first()->toArray();
            $main_category_id = DrawingCategory::where('id',$id)->pluck('drawing_category_id')->first();
            $main_category = DrawingCategory::where('id',$main_category_id)->select('id','name')->first()->toArray();
            $sub_category = DrawingCategory::where('id',$id)->select('id','name')->first()->toArray();
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
                $drawing_image_latest_version[$iterator]['original_id'] = $value;
                $iterator++;
            }
            return view('drawing/images/edit')->with(compact('site','project','client','main_category','sub_category','project_site','path','drawing_image_latest_version','clients','site_id','id'));
        }catch(\Exception $e){
            $data = [
                'action' => 'Edit',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
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
            $tempFileName = str_replace('.'.$extension, '',$request->name);
            $tempFileName = str_replace(" ","",$tempFileName);
            $filename = $tempFileName.'#'.mt_rand(1,10000000000).sha1(time()).".{$extension}";
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
            Log::critical(json_encode([
                'action' => 'Upload drawing Temp Images',
                'exception' => $e->getMessage(),
            ]));
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
            if(Session::has('global_project_site')){
                if(count($request->work_order_images) > 0){
                    $siteId = Session::get('global_project_site');
                    $directoryName = sha1($siteId).DIRECTORY_SEPARATOR.sha1($request->drawing_category_id);
                    $tempImageUploadPath = public_path().env('DRAWING_TEMP_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.sha1($user_id);
                    $imageUploadPath = public_path().env('DRAWING_IMAGE_UPLOAD_PATH').DIRECTORY_SEPARATOR.$directoryName;
                    $workOrderImagesData = array();
                    $files = $request->work_order_images;
                    $workOrderImagesData['project_site_id'] = $siteId;
                    $workOrderImagesData['drawing_category_id'] = $request->drawing_category_id;
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
                }else{
                    $request->session()->flash('error','Please select Images.');
                }
            }else{
                $request->session()->flash('error','Global Site is not selected. Please refresh the page.');

            }

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
            if(Session::has('global_project_site')){
                $subCategories = DrawingCategorySiteRelation::join('drawing_categories','drawing_categories.id','=','drawing_category_site_relations.drawing_category_id')
                    ->where('drawing_category_site_relations.project_site_id', Session::get('global_project_site'))
                    ->select('drawing_categories.name','drawing_categories.id','drawing_categories.drawing_category_id','drawing_category_site_relations.project_site_id')
                    ->get();
            }else{
                $subCategories = DrawingCategorySiteRelation::join('drawing_categories','drawing_categories.id','=','drawing_category_site_relations.drawing_category_id')
                    ->select('drawing_categories.name','drawing_categories.id','drawing_categories.drawing_category_id','drawing_category_site_relations.project_site_id')
                    ->get();
            }

            $iTotalRecords = count($subCategories);
            $records = array();
            $records['data'] = array();
            $end = $request->length < 0 ? count($subCategories) : $request->length;
            $categories = array();
            for($iterator = 0,$pagination = $request->start; $iterator < $request->length && $iterator < count($subCategories); $iterator++,$pagination++ ){
                $records['data'][$iterator] = [
                    ProjectSite::where('id',$subCategories[$pagination]['project_site_id'])->pluck('name')->first(),
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

    public function createVersion(Request $request){
        try{
            $tempImageUploadPath = public_path().env('DRAWING_IMAGE_UPLOAD_PATH').DIRECTORY_SEPARATOR.sha1($request->site_id).DIRECTORY_SEPARATOR.sha1($request->sub_category_id);
            $extension = $request->file('file')->getClientOriginalExtension();
            $tempFileName = str_replace('.'.$extension, '',$request->file('file')->getClientOriginalName());
            $tempFileName = str_replace(" ","",$tempFileName);
            $filename = $tempFileName.'#'.mt_rand(1,10000000000).sha1(time()).".{$extension}";
            $request->file('file')->move($tempImageUploadPath,$filename);
            $data['drawing_image_id'] = $request->drawing_images_id;
            $data['title'] = $request->title;
            $data['name'] = $filename;
            $query = DrawingImageVersion::insert($data);
            return redirect('/drawing/images/edit/'.$request->sub_category_id.'/'.$request->site_id);
        }catch(\Exception $e){
            $data = [
                'action' => 'Version create',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function getManageDrawingsView(Request $request){
        try{
            $projectSiteId = Session::get('global_project_site');
            $categories = DrawingCategory::whereNull('drawing_category_id')->where('is_active',TRUE)->select('name','id')->get();
            return view('drawing/images/manage-drawings')->with(compact('projectSiteId','categories'));
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

    public function getData(Request $request){
        try{
            $drawing_category_site_relation_id = DrawingCategorySiteRelation::where('drawing_category_id',$request->sub_category_id)
                ->where('project_site_id',$request->project_site_id)
                ->pluck('id')->toArray();
            $drawing_images_id = DrawingImage::whereIn('drawing_category_site_relation_id',$drawing_category_site_relation_id)->pluck('id')->toArray();
            $iterator = 0;
            $drawing_image_latest_version = array();
            $path = env('DRAWING_IMAGE_UPLOAD_PATH').DIRECTORY_SEPARATOR.sha1($request->project_site_id).DIRECTORY_SEPARATOR.sha1($request->sub_category_id);
            foreach ($drawing_images_id as $value){
                $drawing_image_latest_version[$iterator] =DrawingImageVersion:: where('drawing_image_id',$value)->orderBy('id','desc')->select('id','title','name')->first()->toArray();
                $drawing_image_latest_version[$iterator]['encoded_name'] = $path.DIRECTORY_SEPARATOR.urlencode($drawing_image_latest_version[$iterator]['name']);
                $drawing_image_latest_version[$iterator]['iterator'] = $iterator;
                $iterator++;
            }
            return view('partials/drawing/images-table')->with(compact('drawing_image_latest_version'));
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

    public function getDetails(Request $request,$id){
        try{
            $file_name = DrawingImageVersion::where('id',$id)->pluck('name')->first();
            $comments = DrawingImageComment::where('drawing_image_version_id',$id)->select('comment')->get()->toArray();
            $images_id = DrawingImageVersion::where('id',$id)->pluck('drawing_image_id')->first();
            $project = DrawingImage::where('id',$images_id)->pluck('drawing_category_site_relation_id')->first();
            $project_details = DrawingCategorySiteRelation::where('id',$project)->select('project_site_id','drawing_category_id')->first()->toArray();
            $path = env('DRAWING_IMAGE_UPLOAD_PATH').DIRECTORY_SEPARATOR.sha1($project_details['project_site_id']).DIRECTORY_SEPARATOR.sha1($project_details['drawing_category_id']);
            $image_src = $path.DIRECTORY_SEPARATOR.urlencode($file_name);
            return view('drawing/images/image-details')->with(compact('comments','image_src','id'));
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

    public function getAllVersions(Request $request){
        try{
            $id = DrawingImageVersion::where('id',$request->id)->pluck('drawing_image_id')->first();
            $versions = DrawingImageVersion::where('drawing_image_id',$id)->select('id','title','name')->get();
            return response()->json($versions);
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

    public function addComment(Request $request){
        try{
            $imageData['comment'] = $request->comment;
            $imageData['drawing_image_version_id'] = $request->drawing_image_version_id;
            $query = DrawingImageComment::create($imageData);
            $request->session()->flash('success','Comment Added Successfully.');
            return redirect('/drawing/images/get-details/'.$request->drawing_image_version_id);
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

    public function getAllVersionImages(Request $request){
        try{
            $id = DrawingImageVersion::where('id',$request->image_version_id)->pluck('drawing_image_id')->first();
            $versions = DrawingImageVersion::where('drawing_image_id',$id)->get();
            $imageVersionData = array();
            foreach($versions as $version){
                $directoryName = sha1($version->drawingImage->drawingCategorySiteRelation->project_site_id).DIRECTORY_SEPARATOR.sha1($version->drawingImage->drawingCategorySiteRelation->drawing_category_id);
                $imagePath = env('DRAWING_IMAGE_UPLOAD_PATH').DIRECTORY_SEPARATOR.$directoryName.DIRECTORY_SEPARATOR.urlencode($version->name);
                $imageVersionData[] = [
                    'id' => $version->id,
                    'title' => $version->title,
                    'image_path' => $imagePath
                ];
            }
            return view('partials.dpr.image-version-listing')->with(compact('imageVersionData'));
        }catch (\Exception $e){
            $data = [
                'action' => 'Get drawing image versions',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            return response()->json(null, 500);
        }
    }

    public function edit(Request $request){
        try{
            $user = Auth::user();
            if(count($request->work_order_images) > 0){
                $siteId = $request->project_site_id;
                $directoryName = sha1($siteId).DIRECTORY_SEPARATOR.sha1($request->sub_category_id);
                $tempImageUploadPath = public_path().env('DRAWING_TEMP_IMAGE_UPLOAD').DIRECTORY_SEPARATOR.sha1($user->id);
                $imageUploadPath = public_path().env('DRAWING_IMAGE_UPLOAD_PATH').DIRECTORY_SEPARATOR.$directoryName;
                $workOrderImagesData = array();
                $files = $request->work_order_images;
                $workOrderImagesData['project_site_id'] = $siteId;
                $workOrderImagesData['drawing_category_id'] = $request->sub_category_id;
                $drawing_categories_site_relation = DrawingCategorySiteRelation::where($workOrderImagesData)->first();
                if($drawing_categories_site_relation == null){
                    $drawing_categories_site_relation = DrawingCategorySiteRelation::create($workOrderImagesData);
                }
                foreach($files as $image){
                    $imageName = urldecode(basename($image['image_name']));
                    $newTempImageUploadPath = $tempImageUploadPath.'/'.$imageName;
                    $imageData['random_string'] = rand(10,100).sha1(time());
                    $imageData['drawing_category_site_relation_id'] = $drawing_categories_site_relation->id;
                    $drawing_image = DrawingImage::create($imageData);
                    $imageVersionData['title'] = $image['title'];
                    $imageVersionData['name'] = $imageName;
                    $imageVersionData['drawing_image_id'] =$drawing_image->id;
                    $drawing_image_version = DrawingImageVersion::create($imageVersionData);
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
            }
            $request->session()->flash('success','Data Saved successfully.');
            return redirect('/drawing/images/edit/'.$request->sub_category_id.'/'.$request->project_site_id);
        }catch (\Exception $e){
            $data = [
                'action' => 'Edit Drawing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}
