<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\CustomTraits\CategoryTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    use CategoryTrait;

    public function __construct()
    {
        $this->middleware('custom.auth');
    }
}
