<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\CustomTraits\RoleTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    use RoleTrait;

    public function __construct()
    {
        $this->middleware('custom.auth')->except('roleListing','autoSuggest');
    }
}
