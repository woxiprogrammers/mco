<?php

namespace App\Http\Controllers\Admin;

use App\Client;
use App\Http\Controllers\CustomTraits\ProjectTrait;
use App\Project;
use App\ProjectSite;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('custom.auth');
    }
    use ProjectTrait;
}
