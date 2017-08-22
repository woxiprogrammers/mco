<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('permissions')->insert([
            [
                'name' => 'create-category',
                'module_id' => DB::table('modules')->where('slug','category')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-category',
                'module_id' => DB::table('modules')->where('slug','category')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-category',
                'module_id' => DB::table('modules')->where('slug','category')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'approve-category',
                'module_id' => DB::table('modules')->where('slug','category')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','approve')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-material',
                'module_id' => DB::table('modules')->where('slug','material')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-material',
                'module_id' => DB::table('modules')->where('slug','material')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-material',
                'module_id' => DB::table('modules')->where('slug','material')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'approve-material',
                'module_id' => DB::table('modules')->where('slug','material')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','approve')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-profit-margin',
                'module_id' => DB::table('modules')->where('slug','profit-margin')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-profit-margin',
                'module_id' => DB::table('modules')->where('slug','profit-margin')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-profit-margin',
                'module_id' => DB::table('modules')->where('slug','profit-margin')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'approve-profit-margin',
                'module_id' => DB::table('modules')->where('slug','profit-margin')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','approve')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-units',
                'module_id' => DB::table('modules')->where('slug','units')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-units',
                'module_id' => DB::table('modules')->where('slug','units')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-units',
                'module_id' => DB::table('modules')->where('slug','units')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'approve-units',
                'module_id' => DB::table('modules')->where('slug','units')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','approve')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-summary',
                'module_id' => DB::table('modules')->where('slug','summary')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-summary',
                'module_id' => DB::table('modules')->where('slug','summary')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-summary',
                'module_id' => DB::table('modules')->where('slug','summary')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'approve-summary',
                'module_id' => DB::table('modules')->where('slug','summary')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','approve')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-tax',
                'module_id' => DB::table('modules')->where('slug','tax')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-tax',
                'module_id' => DB::table('modules')->where('slug','tax')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-tax',
                'module_id' => DB::table('modules')->where('slug','tax')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'approve-tax',
                'module_id' => DB::table('modules')->where('slug','tax')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','approve')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-product',
                'module_id' => DB::table('modules')->where('slug','product')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-product',
                'module_id' => DB::table('modules')->where('slug','product')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-product',
                'module_id' => DB::table('modules')->where('slug','product')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'approve-product',
                'module_id' => DB::table('modules')->where('slug','product')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','approve')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-manage-extra-items',
                'module_id' => DB::table('modules')->where('slug','manage-extra-items')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-manage-extra-items',
                'module_id' => DB::table('modules')->where('slug','manage-extra-items')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-manage-extra-items',
                'module_id' => DB::table('modules')->where('slug','manage-extra-items')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'approve-manage-extra-items',
                'module_id' => DB::table('modules')->where('slug','manage-extra-items')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','approve')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-manage-user',
                'module_id' => DB::table('modules')->where('slug','manage-user')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-manage-user',
                'module_id' => DB::table('modules')->where('slug','manage-user')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-manage-user',
                'module_id' => DB::table('modules')->where('slug','manage-user')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'approve-manage-user',
                'module_id' => DB::table('modules')->where('slug','manage-user')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','approve')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-manage-client',
                'module_id' => DB::table('modules')->where('slug','manage-client')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-manage-client',
                'module_id' => DB::table('modules')->where('slug','manage-client')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-manage-client',
                'module_id' => DB::table('modules')->where('slug','manage-client')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'approve-manage-client',
                'module_id' => DB::table('modules')->where('slug','manage-client')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','approve')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-manage-sites',
                'module_id' => DB::table('modules')->where('slug','manage-sites')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-manage-sites',
                'module_id' => DB::table('modules')->where('slug','manage-sites')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-manage-sites',
                'module_id' => DB::table('modules')->where('slug','manage-sites')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'approve-manage-sites',
                'module_id' => DB::table('modules')->where('slug','manage-sites')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','approve')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-quotation',
                'module_id' => DB::table('modules')->where('slug','quotation')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-quotation',
                'module_id' => DB::table('modules')->where('slug','quotation')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-quotation',
                'module_id' => DB::table('modules')->where('slug','quotation')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'approve-quotation',
                'module_id' => DB::table('modules')->where('slug','quotation')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','approve')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-product-management-after-quotation-approve',
                'module_id' => DB::table('modules')->where('slug','product-management-after-quotation-approve')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-product-management-after-quotation-approve',
                'module_id' => DB::table('modules')->where('slug','product-management-after-quotation-approve')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-product-management-after-quotation-approve',
                'module_id' => DB::table('modules')->where('slug','product-management-after-quotation-approve')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'remove-product-management-after-quotation-approve',
                'module_id' => DB::table('modules')->where('slug','product-management-after-quotation-approve')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','remove')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-extra-item-calculation-in-quotation',
                'module_id' => DB::table('modules')->where('slug','extra-item-calculation-in-quotation')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-extra-item-calculation-in-quotation',
                'module_id' => DB::table('modules')->where('slug','extra-item-calculation-in-quotation')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-extra-item-calculation-in-quotation',
                'module_id' => DB::table('modules')->where('slug','extra-item-calculation-in-quotation')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-billing',
                'module_id' => DB::table('modules')->where('slug','billing')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-billing',
                'module_id' => DB::table('modules')->where('slug','billing')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-billing',
                'module_id' => DB::table('modules')->where('slug','billing')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'approve-billing',
                'module_id' => DB::table('modules')->where('slug','billing')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','approve')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-billing-transaction',
                'module_id' => DB::table('modules')->where('slug','billing-transaction')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-billing-transaction',
                'module_id' => DB::table('modules')->where('slug','billing-transaction')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-billing-transaction',
                'module_id' => DB::table('modules')->where('slug','billing-transaction')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'approve-billing-transaction',
                'module_id' => DB::table('modules')->where('slug','billing-transaction')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','approve')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-purchase-request',
                'module_id' => DB::table('modules')->where('slug','purchase-request')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-purchase-request',
                'module_id' => DB::table('modules')->where('slug','purchase-request')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-purchase-request',
                'module_id' => DB::table('modules')->where('slug','purchase-request')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'approve-purchase-request',
                'module_id' => DB::table('modules')->where('slug','purchase-request')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','approve')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'remove-purchase-request',
                'module_id' => DB::table('modules')->where('slug','purchase-request')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','remove')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-material-request',
                'module_id' => DB::table('modules')->where('slug','material-request')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-material-request',
                'module_id' => DB::table('modules')->where('slug','material-request')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-material-request',
                'module_id' => DB::table('modules')->where('slug','material-request')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'approve-material-request',
                'module_id' => DB::table('modules')->where('slug','material-request')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','approve')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'remove-material-request',
                'module_id' => DB::table('modules')->where('slug','material-request')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','remove')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-vendor-assignment',
                'module_id' => DB::table('modules')->where('slug','vendor-assignment')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-vendor-assignment',
                'module_id' => DB::table('modules')->where('slug','vendor-assignment')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-vendor-assignment',
                'module_id' => DB::table('modules')->where('slug','vendor-assignment')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-purchase-order',
                'module_id' => DB::table('modules')->where('slug','purchase-order')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-purchase-order',
                'module_id' => DB::table('modules')->where('slug','purchase-order')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-purchase-order',
                'module_id' => DB::table('modules')->where('slug','purchase-order')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'approve-purchase-order',
                'module_id' => DB::table('modules')->where('slug','purchase-order')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','approve')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-purchase-bill',
                'module_id' => DB::table('modules')->where('slug','purchase-bill')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-purchase-bill',
                'module_id' => DB::table('modules')->where('slug','purchase-bill')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-purchase-bill',
                'module_id' => DB::table('modules')->where('slug','purchase-bill')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-purchase-history',
                'module_id' => DB::table('modules')->where('slug','purchase-bill')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => false,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'view-purchase-history',
                'module_id' => DB::table('modules')->where('slug','purchase-bill')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-manage-amendment',
                'module_id' => DB::table('modules')->where('slug','manage-amendment')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-manage-amendment',
                'module_id' => DB::table('modules')->where('slug','manage-amendment')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-manage-amendment',
                'module_id' => DB::table('modules')->where('slug','manage-amendment')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'approve-manage-amendment',
                'module_id' => DB::table('modules')->where('slug','manage-amendment')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','approve')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-inventory-in-out-transfer',
                'module_id' => DB::table('modules')->where('slug','inventory-in-out-transfer')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-inventory-in-out-transfer',
                'module_id' => DB::table('modules')->where('slug','inventory-in-out-transfer')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-inventory-in-out-transfer',
                'module_id' => DB::table('modules')->where('slug','inventory-in-out-transfer')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-asset-reading',
                'module_id' => DB::table('modules')->where('slug','asset-reading')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-asset-reading',
                'module_id' => DB::table('modules')->where('slug','asset-reading')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-asset-reading',
                'module_id' => DB::table('modules')->where('slug','asset-reading')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-asset-maintainance',
                'module_id' => DB::table('modules')->where('slug','asset-maintainance')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-asset-maintainance',
                'module_id' => DB::table('modules')->where('slug','asset-maintainance')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-asset-maintainance',
                'module_id' => DB::table('modules')->where('slug','asset-maintainance')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-asset-management',
                'module_id' => DB::table('modules')->where('slug','asset-management')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-asset-management',
                'module_id' => DB::table('modules')->where('slug','asset-management')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-asset-management',
                'module_id' => DB::table('modules')->where('slug','asset-management')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-inventory-history',
                'module_id' => DB::table('modules')->where('slug','inventory-history')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => false,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'view-inventory-history',
                'module_id' => DB::table('modules')->where('slug','inventory-history')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ]
        ]);
    }
}
