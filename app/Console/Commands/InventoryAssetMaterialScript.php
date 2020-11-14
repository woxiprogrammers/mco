<?php

namespace App\Console\Commands;

use App\InventoryComponent;
use App\Year;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class InventoryAssetMaterialScript extends Command
{

    //php artisan custom:salary-distribution --month=8 --year=2018
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:inventory-asset-material';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'One time script for update inventory component name with Asset and material name';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        try{
            $InventoryComponent = new InventoryComponent();
            $icRetdata = $InventoryComponent->join('assets','inventory_components.reference_id','assets.id')
                    ->where('inventory_components.is_material',false)
                    ->where('inventory_components.name','!=','assets.name')
                    ->select('assets.name as asset_name','inventory_components.name as inv_name',
                    'inventory_components.id as inv_id','inventory_components.reference_id as ref_id')
                    ->get()->toArray();

            foreach($icRetdata as $icAsset) {
                if($icAsset['asset_name'] != $icAsset['inv_name']) {
                    var_dump($icAsset);
                    $icdata = array(
                        'name' => $icAsset['asset_name']
                    );
                    $ret = $InventoryComponent->where('id','=',$icAsset['inv_id'])
                    ->update($icdata);
                }
            }

            $icMatdata = $InventoryComponent->join('materials','inventory_components.reference_id','materials.id')
                    ->where('inventory_components.is_material',true)
                    ->where('inventory_components.name','!=','materials.name')
                    ->select('materials.name as mat_name','inventory_components.name as inv_name',
                    'inventory_components.id as inv_id','inventory_components.reference_id as ref_id')
                    ->get()->toArray();

            foreach($icMatdata as $icMat) {
                if($icMat['mat_name'] != $icMat['inv_name']) {
                    var_dump($icMat);
                    $icdata = array(
                        'name' => $icMat['mat_name']
                    );
                    $ret = $InventoryComponent->where('id','=',$icMat['inv_id'])
                    ->update($icdata);
                }
            }
        }catch(\Exception $e){
            $data = [
                'action' => 'One time script for update inventory component name with Asset and material name',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $this->error($e->getMessage());
        }
    }
}
