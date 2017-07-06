<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\CategoryMaterialRelation;
use App\Quotation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use ConsoleTVs\Charts\Facades\Charts;

class DashboardController extends Controller
{
    public function index()
    {
        /*
         * Quotation Status Wise Chart
         */

        $quotationApprovedCount = Quotation::where('quotation_status_id', 2)->count();
        $quotationDraftCount = Quotation::where('quotation_status_id', 1)->count();
        $quotationDisapprovedCount = Quotation::where('quotation_status_id', 3)->count();
        $quotationStatus = Charts::multi('bar', 'material')
            // Setup the chart settings
            ->title("Projects Status")
            // A dimension of 0 means it will take 100% of the space
            ->dimensions(0, 400) // Width x Height
            // This defines a preset of colors already done:)
            ->template("material")
            // You could always set them manually
            // ->colors(['#2196F3', '#F44336', '#FFC107'])
            // Setup the diferent datasets (this is a multi chart)
            ->dataset('Approved', [$quotationApprovedCount])
            ->dataset('Disaproved', [$quotationDisapprovedCount])
            ->dataset('Draft', [$quotationDraftCount])
            // Setup what the values mean
            ->labels(['Projects']);

        /*
         * Category Wise Materials
         */
        $categoryData = Category::orderBy('id','asc')->get(['name','id'])->toArray();
        $categorymatData = CategoryMaterialRelation::get()->toArray();
        $category = array();
        $materialCounts = array();
        $colors = array();
        foreach ($categoryData as $cat) {
            $category[] = $cat['name'];
            $matCount = 0;
            foreach ($categorymatData as $catMat) {
                if($cat['id'] == $catMat['category_id']) {
                    $matCount++;
                }
            }
            $materialCounts[] = $matCount;
            $colors[] = $this->generateRandomString(6);
        }
        $totalCategory = count($category);
        $totalMaterials = count($categorymatData);

        $categorywiseMaterialCount = Charts::create('line', 'highcharts')
            ->title('Categorywise Material Count')
            ->labels($category)
            ->values($materialCounts)
            ->colors($colors)
            ->dimensions(0,400)
            ->type('pie');

        return view('admin.dashboard', [
            'quotationStatus' => $quotationStatus,
            'categorywiseMaterialCount' => $categorywiseMaterialCount,
            'totalCategory' => $totalCategory,
            'totalMaterials' => $totalMaterials,
            ]);
    }

    function generateRandomString($length = 6) {
        return "#".substr(str_shuffle(str_repeat($x='0123456789abcdefABCDEF', ceil($length/strlen($x)) )),1,$length);
    }
}
