<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\CustomTraits\UnitsTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UnitsController extends Controller
{
    use UnitsTrait;

    public function __construct()
    {
        $this->middleware('custom.auth');
    }
}
