<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\CustomTraits\ProductTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    use ProductTrait;
    public function __construct()
    {
        $this->middleware('custom.auth');
    }
}
