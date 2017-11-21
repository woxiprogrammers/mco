<?php

namespace App\Http\Controllers\CheckList;

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
            return view('checklist.structure.create');
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


