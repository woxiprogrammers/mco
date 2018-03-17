<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/material/check-name','/material/listing','/category/listing','/product/listing','/profit-margin/listing','/units/listing','/units/conversion/listing',
        '/summary/listing','/tax/listing','/quotation/get-materials','/quotation/get-profit-margins','/quotation/listing/*','bill/create','/project/listing','/quotation/get-product-calculations',
        '/quotation/image-upload/*','/quotation/display-images/*','/quotation/delete-temp-product-image','bill/approve','/bill/image-upload/*','/bill/display-images/*','/bill/delete-temp-product-image','/bill/edit/*',
        '/bill/product_description/create','/bill/product_description/update','/bill/calculate-tax-amounts','/bill/transaction/listing/*',
         '/quotation/check-product-remove','/extra-item/listing','/vendors/listing','/bank/listing','/checklist/category-management/listing','purchase/material-request/get-materials','/purchase/material-request/get-units',
        'purchase/material-request/create','/checkList/listing','/checklist/category-management/listing','role/listing',
        '/user/project-site/auto-suggest/*','/asset/listing','/asset/display-images','/asset/delete-temp-product-image','purchase/purchase-order/get-details','purchase/purchase-order/create-transaction','purchase/purchase-order/add-payment',
        'labour/listing','/peticash/master-peticash-account/listing','/peticash/sitewise-peticash-account/listing', '/peticash/peticash-approval-request/manage-purchase-list-ajax','/peticash/peticash-approval-request/manage-salary-list-ajax',
        '/purchase/purchase-order/change-status','/purchase/purchase-order/get-bill-details','/purchase/purchase-order/close-purchase-order','/subcontractor/listing',
        '/subcontractor/subcontractor-structure/listing','/awareness/file-management/file-upload','/awareness/file-management/get-files','/awareness/category-management/main-category-listing','/awareness/file-management/get-subcategories',
        '/awareness/file-management/get-subcategories-details','/checklist/category-management/change-status/*','/drawing/images/get-projects/','/drawing/images/get-project-sites/','/drawing/images/get-sub-categories/','/drawing/images/display-images/*','/drawing/images/delete-temp-product-image/'
        ,'/drawing/images/get-data/','/drawing/images/get-versions/','/awareness/file-management/get-subcategories-details','/checklist/category-management/change-status/*','/drawing/images/get-projects/','/drawing/images/get-project-sites/','/drawing/images/get-sub-categories/','/drawing/images/image-upload/*',
        '/drawing/images/display-images/*','/drawing/images/delete-temp-product-image/','/drawing/images/get-data/','/user/get-permission','/peticash/salary-request/get-labours','/purchase/purchase-order/reopen','/change-project-site','/asset/maintenance/request/display-images','/asset/maintenance/request/delete-temp-product-image',
        '/asset/maintenance/request/listing','/asset/maintenance/request/approval/listing','/inventory/transfer/upload-pre-grn-images'

    ];
}
