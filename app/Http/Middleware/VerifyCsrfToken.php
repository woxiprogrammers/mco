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
        'labour/listing','/peticash/master-peticash-account/listing','/peticash/sitewise-peticash-account/listing'
    ];
}
