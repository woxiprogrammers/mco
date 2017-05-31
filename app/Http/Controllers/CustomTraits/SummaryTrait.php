<?php
namespace App\Http\Controllers\CustomTraits;
use App\Http\Requests\SummaryRequest;
use App\Summary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\DocBlock\Tags\Example;

trait SummaryTrait{
    public function getManageView(Request $request) {
        try{
            return view('admin.summary.manage');
        }catch(\Exception $e){
            $data = [
                'action' => "Get summary manage view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
    public function getCreateView(Request $request) {
        try{
            return view('admin.summary.create');
        }catch(\Exception $e){
            $data = [
                'action' => "Get summary create view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
    public function getEditView(Request $request,$summary) {
        try{
            $summary = $summary->toArray();
            return view('admin.summary.edit')->with(compact('summary'));
        }catch(\Exception $e){
            $data = [
                'action' => "Get summary edit view",
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function createSummary(SummaryRequest $request){
        try{
            $data['name'] = ucwords($request->name);
            $data['is_active'] = false;
            $summary = Summary::create($data);
            $request->session()->flash('success', 'Summary Created successfully.');
            return redirect('/summary/create');
        }catch(\Exception $e){
            $data = [
              'action' => 'Create New Summary',
              'params' => $request->all(),
              'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function editSummary(SummaryRequest $request, $summary){
        try{
            $summary->update(['name' => ucwords($request->name)]);
            $request->session()->flash('success', 'Summary Edited successfully.');
            return redirect('/summary/edit/'.$summary->id);
        }catch(\Exception $e){
            $data = [
                'action' => 'Edit existing Summary',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function summaryListing(Request $request){
        try{
            $summaryData = Summary::orderBy('id','asc')->get()->toArray();
            $iTotalRecords = count($summaryData);
            $records = array();
            $iterator = 0;
            foreach($summaryData as $summary){
                if($summary['is_active'] == true){
                    $summary_status = '<td><span class="label label-sm label-success"> Enabled </span></td>';
                    $status = 'Disable';
                }else{
                    $summary_status = '<td><span class="label label-sm label-danger"> Disabled</span></td>';
                    $status = 'Enable';
                }
                $records['data'][$iterator] = [
                    $summary['name'],
                    $summary_status,
                    date('d M Y',strtotime($summary['created_at'])),
                    '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="/summary/edit/'.$summary['id'].'">
                                    <i class="icon-docs"></i> Edit </a>
                            </li>
                            <li>
                                <a href="/summary/change-status/'.$summary['id'].'">
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
                'action' => 'Get Summary Listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
        return response()->json($records,200);
    }

    public function changeSummaryStatus(Request $request, $summary){
        try{
            $newStatus = (boolean)!$summary->is_active;
            $summary->update(['is_active' => $newStatus]);
            $request->session()->flash('success', 'Summary Status changed successfully.');
            return redirect('/summary/manage');
        }catch(\Exception $e){
            $data = [
                'action' => 'Change summary status',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function checkSummaryName(Request $request){
        try{
            $summaryName = ucwords($request->name);
            if($request->has('summary_id')){
                $nameCount = Summary::where('name','=',$summaryName)->where('id','!=',$request->$summary_id)->count();
            }else{
                $nameCount = Summary::where('name','=',$summaryName)->count();
            }
            if($nameCount > 0){
                return 'false';
            }else{
                return 'true';
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'Check Summary name',
                'param' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }

    }

}