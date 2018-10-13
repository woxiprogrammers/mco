<?php

namespace App\Console\Commands;

use App\InventoryComponent;
use App\InventoryComponentTransfers;
use App\Month;
use App\ProjectSite;
use App\Year;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AssetRentCalculations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    //php artisan custom:peticash-purchase-transaction-monthly-expense-calculation --month=all --year=all  => Executes Case 1

    protected $signature = 'custom:asset-rent-calculate {--year=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
            $year = new Year();
            $month = new Month();
            $inventoryComponentTransfer = new InventoryComponentTransfers();
            $inventoryComponent = new InventoryComponent();
            if($this->option('year') != 'null'){
                $yearSlug = $this->option('year');
                $thisYear = $year->where('slug',$yearSlug)->first();
                $months = $month->orderBy('id','asc')->get();
            }else{
                $months = $month->orderBy('id','asc')->get();
                $thisYear = $year->where('slug',date('Y', strtotime('last month')))->first();
            }
            $projectSite = new ProjectSite();
            $projectSites = $projectSite->where('id',13)->get();
            foreach($projectSites as $projectSite){
                foreach ($months as $thisMonth){
                    $inventoryComponentTransfers = $inventoryComponent->join('inventory_component_transfers','inventory_component_transfers.inventory_component_id'
                        ,'=','inventory_components.id')
                        ->where('inventory_components.project_site_id',$projectSite['id'])
                        ->whereMonth('inventory_component_transfers.created_at',$thisMonth['id'])
                        ->whereYear('inventory_component_transfers.created_at',$thisYear['slug'])
                        ->groupBy('inventory_components.id')
                        ->select('inventory_components.id','inventory_component_transfers.id');
                    dd($inventoryComponentTransfers);
            }
            }

        }catch(\Exception $e){
            $data = [
                'action' => 'Salary Distribution among sites',
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            $this->error($e->getMessage());
        }
    }
}
