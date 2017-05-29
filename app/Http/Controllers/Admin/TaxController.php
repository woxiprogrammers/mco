<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\CustomTraits\TaxTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TaxController extends Controller
{
    use TaxTrait;

    public function __construct()
    {
        $this->middleware('custom.auth');
    }
}
