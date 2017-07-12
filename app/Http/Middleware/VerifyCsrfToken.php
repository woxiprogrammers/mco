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
        '/quotation/image-upload/*','/quotation/display-images/*','/quotation/delete-temp-product-image','bill/approve','/bill/image-upload/*','/bill/display-images/*','/bill/delete-temp-product-image','/bill/edit/*'
    ];
}
