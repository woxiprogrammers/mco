<?php

namespace App\Console\Commands;

use App\Bill;
use App\Quotation;
use App\SubcontractorStructureType;
use Illuminate\Console\Command;

class BillModuleChanges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:bill';

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
        $quotation = new Quotation();
        $bill = new Bill();
        $subcontractorStructureType = new SubcontractorStructureType();
        $quotationIds = $bill->pluck('quotation_id')->toArray();
        $quotation->whereIn('id',array_unique($quotationIds))->update([
            'bill_type_id' => $subcontractorStructureType->where('slug','itemwise')->pluck('id')->first()
        ]);
    }
}
