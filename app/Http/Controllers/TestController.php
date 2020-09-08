<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{

    public function __construct()
    {
    }

    public function generateTestPDF()
    {
        try {
            $data = [];
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML(view('test', $data));
            // $pdf->setPaper('A4', 'landscape');
            return $pdf->stream();
        } catch (\Exception $e) {
            $data = [
                'actions' => 'Generate Cumulative Bill',
                'params' => [],
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500, $e->getMessage());
        }
    }
}
