<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\CustomTraits\BillTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BillController extends Controller
{
    use BillTrait;

    public function __construct()
    {
        $this->middleware('custom.auth')->except(['calculateTaxAmounts','billTransactionListing']);
    }
}
