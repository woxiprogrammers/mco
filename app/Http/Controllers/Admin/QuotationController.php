<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\CustomTraits\QuotationTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class QuotationController extends Controller
{
    public function __construct(){
        $this->middleware('custom.auth')->except(['getMaterials','getProfitMargins']);
    }

    use QuotationTrait;
}
