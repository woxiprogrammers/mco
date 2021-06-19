<?php

namespace App;

use App\Helper\UnitHelper;
use Illuminate\Database\Eloquent\Model;

class InventoryComponent extends Model
{

    protected $table = 'inventory_components';

    protected $fillable = [
        'name', 'project_site_id', 'purchase_order_component_id', 'is_material',
        'reference_id', 'opening_stock'
    ];

    public function projectSite()
    {
        return $this->belongsTo('App\ProjectSite', 'project_site_id');
    }

    public function asset()
    {
        return $this->belongsTo('App\Asset', 'reference_id');
    }

    public function material()
    {
        return $this->belongsTo('App\Material', 'reference_id');
    }

    public function fuelAssetReading()
    {
        return $this->hasMany('App\FuelAssetReading', 'inventory_component_id');
    }

    public function purchaseOrderComponent()
    {
        return $this->belongsTo('App\PurchaseOrderComponent', 'purchase_order_component_id');
    }

    public function quotation()
    {
        return $this->hasOne('App\Quotation', 'project_site_id', 'project_site_id');
    }

    public function inventoryComponentTransfers()
    {
        return $this->hasMany('App\InventoryComponentTransfers', 'inventory_component_id');
    }

    public function getAvailableQuantity()
    {
        $inventoryComponent = InventoryComponent::where('id', $this['id'])->first();
        $opening_stock = $inventoryComponent->opening_stock ?? 0;
        if ($inventoryComponent->is_material == true) {
            $materialUnit = Material::where('id', $inventoryComponent['reference_id'])->pluck('unit_id')->first();
            $unitID = Unit::where('id', $materialUnit)->pluck('id')->first();
            $inTransferQuantities = InventoryComponentTransfers::join('inventory_transfer_types', 'inventory_transfer_types.id', '=', 'inventory_component_transfers.transfer_type_id')
                ->where('inventory_transfer_types.type', 'ilike', 'in')
                ->where('inventory_component_transfers.inventory_component_id', $inventoryComponent->id)
                ->where('inventory_component_transfers.inventory_component_transfer_status_id', InventoryComponentTransferStatus::where('slug', 'approved')->pluck('id')->first())
                ->select('inventory_component_transfers.quantity as quantity', 'inventory_component_transfers.unit_id as unit_id')
                ->get();
            $outTransferQuantities = InventoryComponentTransfers::join('inventory_transfer_types', 'inventory_transfer_types.id', '=', 'inventory_component_transfers.transfer_type_id')
                ->where('inventory_transfer_types.type', 'ilike', 'out')
                ->where('inventory_component_transfers.inventory_component_id', $inventoryComponent->id)
                ->where('inventory_component_transfers.inventory_component_transfer_status_id', InventoryComponentTransferStatus::where('slug', 'approved')->pluck('id')->first())
                ->select('inventory_component_transfers.quantity as quantity', 'inventory_component_transfers.unit_id as unit_id')
                ->get();
            $inQuantity = $outQuantity = 0;
            foreach ($inTransferQuantities as $inTransferQuantity) {
                $unitConversionQuantity = UnitHelper::unitQuantityConversion($inTransferQuantity['unit_id'], $materialUnit, $inTransferQuantity['quantity']);
                if (!is_array($unitConversionQuantity)) {
                    $inQuantity += $unitConversionQuantity;
                }
            }
            foreach ($outTransferQuantities as $outTransferQuantity) {
                $unitConversionQuantity = UnitHelper::unitQuantityConversion($outTransferQuantity['unit_id'], $materialUnit, $outTransferQuantity['quantity']);
                if (!is_array($unitConversionQuantity)) {
                    $outQuantity += $unitConversionQuantity;
                }
            }
        } else {
            $unitID = Unit::where('slug', 'nos')->pluck('id')->first();
            $inQuantity = InventoryComponentTransfers::join('inventory_transfer_types', 'inventory_transfer_types.id', '=', 'inventory_component_transfers.transfer_type_id')
                ->where('inventory_transfer_types.type', 'ilike', 'in')
                ->where('inventory_component_transfers.inventory_component_id', $inventoryComponent->id)
                ->where('inventory_component_transfers.inventory_component_transfer_status_id', InventoryComponentTransferStatus::where('slug', 'approved')->pluck('id')->first())
                ->sum('inventory_component_transfers.quantity');
            $outQuantity = InventoryComponentTransfers::join('inventory_transfer_types', 'inventory_transfer_types.id', '=', 'inventory_component_transfers.transfer_type_id')
                ->where('inventory_transfer_types.type', 'ilike', 'out')
                ->where('inventory_component_transfers.inventory_component_id', $inventoryComponent->id)
                ->where('inventory_component_transfers.inventory_component_transfer_status_id', InventoryComponentTransferStatus::where('slug', 'approved')->pluck('id')->first())
                ->sum('inventory_component_transfers.quantity');
        }
        $openQty = 0;
        if ($opening_stock != null) {
            $openQty = $opening_stock;
        }
        $inQuantity = $inQuantity;
        $openQty = $openQty;
        $outQuantity = $outQuantity;
        $availableQuantity = ($inQuantity + $openQty) - $outQuantity;
        return $availableQuantity;
    }
}
