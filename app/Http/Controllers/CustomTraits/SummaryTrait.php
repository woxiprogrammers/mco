<?php
namespace App\Http\Controllers\CustomTraits;
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

    public function createSummary(Request $request){
        try{
            $data = ucwords($request->name);
            $summary = Summary::create(['name'=>$data]);
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

    public function editSummary(Request $request, $summary){
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

}