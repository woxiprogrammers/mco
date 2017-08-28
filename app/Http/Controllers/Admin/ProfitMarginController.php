<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\CustomTraits\ProfitMarginTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProfitMarginController extends Controller
{
    public function __construct(){
        $this->middleware('custom.auth');
    }
    use ProfitMarginTrait;
}
