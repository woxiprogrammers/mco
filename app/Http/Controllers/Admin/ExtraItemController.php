<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\CustomTraits\ExtraItemTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExtraItemController extends Controller
{
    use ExtraItemTrait;

    public function __construct()
    {
        $this->middleware('custom.auth')->except('extraItemListing');
    }
}
