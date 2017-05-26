<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\CustomTraits\MaterialTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MaterialController extends Controller
{
    use MaterialTrait;

    public function __construct()
    {
        $this->middleware('custom.auth');
    }
}
