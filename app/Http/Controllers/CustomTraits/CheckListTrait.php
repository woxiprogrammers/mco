<?php
namespace App\Http\Controllers\CustomTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
trait CheckListTrait
{
    public function getManageView(Request $request)
    {
        try {
            return view('checklist.checkList.manage');
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
            return view('checklist.checkList.create');
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
}