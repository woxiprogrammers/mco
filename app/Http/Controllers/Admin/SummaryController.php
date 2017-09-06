<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\CustomTraits\SummaryTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SummaryController extends Controller
{
    public function __construct(){
        $this->middleware('custom.auth');
    }
   use SummaryTrait;
}
