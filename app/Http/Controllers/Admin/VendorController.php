<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\CustomTraits\VendorTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VendorController extends Controller
{
    use VendorTrait;

    public function __construct()
    {
        $this->middleware('custom.auth')->except('vendorListing');
    }
}
