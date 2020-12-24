<?php

namespace App\Http\Controllers\Report;

use App\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Inventory\InventoryManageController;
use App\InventoryComponent;
use App\InventoryComponentTransfers;
use App\InventoryComponentTransferStatus;
use App\ProjectSite;
use App\RentalInventoryComponent;
use App\RentalInventoryTransfer;
use App\Unit;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Support\Facades\App;
use InventoryCart;

class RentalReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('custom.auth');
    }

    public function getManageView(Request $request)
    {
        try {
            return view('report.rental.manage');
            return view('admin.bill.manage-bill')->with(compact('taxes', 'project_site', 'bill_statuses'));
        } catch (\Exception $e) {
            $data = [
                'action' => 'Get bill manage view',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }

    public function listing(Request $request)
    {
        try {
            $user = Auth::user();
            $search_name = null;
            if ($request->has('search_name')) {
                $search_name = $request->search_name;
            }

            $clientData = Client::where('company', 'ilike', '%' . $search_name . '%')
                ->orderBy('company', 'asc')->get()->toArray();
            $iTotalRecords = count($clientData);
            $records = array();
            $records['data'] = array();
            for ($iterator = 0, $pagination = $request->start; $iterator < $request->length && $pagination < count($clientData); $iterator++, $pagination++) {
                if ($clientData[$pagination]['is_active'] == true) {
                    $client_status = '<td><span class="label label-sm label-success"> Enabled </span></td>';
                    $status = 'Disable';
                } else {
                    $client_status = '<td><span class="label label-sm label-danger"> Disabled</span></td>';
                    $status = 'Enable';
                }
                if ($user->roles[0]->role->slug == 'admin' || $user->roles[0]->role->slug == 'superadmin' || $user->customHasPermission('approve-manage-client')) {
                    $button = '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="/reports/rental?type=xls">
                                    <i class="icon-docs"></i> XLSX Report </a>
                                <a href="/reports/rental?type=pdf">
                                    <i class="icon-docs"></i> PDF Report </a>
                            </li>
                        </ul>
                    </div>';
                } else {
                    $button = '<div class="btn-group">
                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                            Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-left" role="menu">
                            <li>
                                <a href="/client/edit/' . $clientData[$pagination]['id'] . '">
                                    <i class="icon-docs"></i> Edit </a>
                            </li>
                        </ul>
                    </div>';
                }
                $records['data'][$iterator] = [
                    ucwords($clientData[$pagination]['company']),
                    $clientData[$pagination]['email'],
                    $clientData[$pagination]['mobile'],
                    $client_status,
                    date('d M Y', strtotime($clientData[$pagination]['created_at'])),
                    $button
                ];
            }
            $records["draw"] = intval($request->draw);
            $records["recordsTotal"] = $iTotalRecords;
            $records["recordsFiltered"] = $iTotalRecords;
        } catch (\Exception $e) {
            $records = array();
            $data = [
                'action' => 'Rental report listing',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }

        return response()->json($records, 200);
    }

    public function exportReport(Request $request)
    {
        try {
            $thisMonth = date('m');
            $thisYear = date('y');
            $user = Auth::user();
            $projectSite = ProjectSite::first();
            $projectSiteRentTotal = $inventoryComponentIterator = 0;
            $unit = Unit::where('slug', 'nos')->select('id', 'name')->first();

            $companyHeader['company_name'] = env('COMPANY_NAME');
            $companyHeader['designation'] = env('DESIGNATION');
            $companyHeader['address'] = env('ADDRESS');
            $companyHeader['contact_no'] = env('CONTACT_NO');
            $companyHeader['gstin_number'] = env('GSTIN_NUMBER');


            $controller = new InventoryManageController;
            $rentalInventoryTransfers = RentalInventoryTransfer::join('inventory_component_transfers', 'inventory_component_transfers.id', '=', 'rental_inventory_transfers.inventory_component_transfer_id')
                ->join('inventory_transfer_types', 'inventory_transfer_types.id', '=', 'inventory_component_transfers.transfer_type_id')
                ->join('inventory_components', 'inventory_components.id', '=', 'inventory_component_transfers.inventory_component_id')
                ->where('project_site_id', $projectSite['id'])
                ->whereBetween('rental_inventory_transfers.rent_start_date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                ->select(
                    'rental_inventory_transfers.id',
                    'rental_inventory_transfers.inventory_component_transfer_id',
                    'rental_inventory_transfers.quantity',
                    'rental_inventory_transfers.rent_per_day',
                    'rental_inventory_transfers.rent_start_date',
                    'inventory_component_transfers.inventory_component_id',
                    'inventory_transfer_types.type as inventory_transfer_type'
                )->get();

            // Headers
            $rows = [
                ['make_bold' => true, 'Sr No', 'Name', 'Transfer Date', 'Days', 'Rent', 'Qty', 'Unit', 'Amount'],
            ];

            $inventoryComponents = $projectSite->inventoryComponents->where('is_material', false);
            foreach ($inventoryComponents as $inventoryComponent) {
                $inventoryComponentIterator++;
                $transactions = [];
                $openingStockForThisMonth = RentalInventoryComponent::where('inventory_component_id', $inventoryComponent['id'])->where('month', $thisMonth - 1)->where('year', $thisYear - 1)->pluck('closing_stock')->first();
                if (!$openingStockForThisMonth) {
                    $availableQuantity = $controller->checkInventoryAvailableQuantity(['inventoryComponentId'  => $inventoryComponent['id'], 'quantity' => 0, 'unitId' => $unit['id']]);
                    $openingStockForThisMonth = $availableQuantity['available_quantity'];
                }
                $rentalDataAlreadyExists = RentalInventoryComponent::where('inventory_component_id', $inventoryComponent['id'])->where('month', $thisMonth)->where('year', $thisYear)->first();
                if ($rentalDataAlreadyExists) {
                    $rentalDataAlreadyExists->update(['opening_stock' => $openingStockForThisMonth]);
                    $rentalData = $rentalDataAlreadyExists;
                } else {
                    $rentalData = RentalInventoryComponent::create([
                        'inventory_component_id'  => $inventoryComponent['id'],
                        'month' => $thisMonth,
                        'year'  => $thisYear,
                        'opening_stock' => $openingStockForThisMonth,
                        'closing_stock' => $openingStockForThisMonth     // Intially closing stock will be same as opening stock but eventually will get updated once trasactions are calculated
                    ]);
                }

                $ratePerUnit = $inventoryComponent->asset->rent_per_day;
                $noOfDays = Carbon::now()->endOfMonth()->diffInDays(Carbon::now()->startOfMonth()) + 1;

                // Opening stock row for a inventry component
                $transactions[] = [
                    'make_bold'    => false, $inventoryComponentIterator, $inventoryComponent['name'], 'Opening Stock', $noOfDays, $ratePerUnit, $openingStockForThisMonth, $unit['name'], $noOfDays * $ratePerUnit * $openingStockForThisMonth
                ];
                $inventoryTraansfers = $rentalInventoryTransfers->where('inventory_component_id', $inventoryComponent['id'])->sortBy('rent_start_date');
                foreach ($inventoryTraansfers as $inventoryTransfer) {
                    $noOfDays = Carbon::now()->endOfMonth()->diffInDays($inventoryTransfer['rent_start_date']) + 1;
                    $quantity = ($inventoryTransfer['inventory_transfer_type'] === 'IN') ? -1 * abs($inventoryTransfer['quantity']) : $inventoryTransfer['quantity'];
                    $rentPerDay = (float)$inventoryTransfer['rent_per_day'];
                    $total = $quantity * $rentPerDay * $noOfDays;

                    // trasaction rows for a inventory component
                    $transactions[] = ['make_bold' => false, '', '', date('d/m/Y', strtotime($inventoryTransfer['rent_start_date'])), $noOfDays, $rentPerDay, $quantity, $unit['name'], $total];
                }
                // Closing stock row
                $closingStock = array_sum(array_column($transactions, 5));
                $transactions[] = ['make_bold' => false, '', '', 'Closing Stock', '', '', $closingStock, '', ''];
                $rentalData->update(['closing_stock'    => $closingStock]);

                // total row for inventory Component
                $inventoryComponentRentTotal = array_sum(array_column($transactions, 7));
                $projectSiteRentTotal += $inventoryComponentRentTotal;
                $transactions[] = ['make_bold' => true, '', '', '', '', '', '', '', $inventoryComponentRentTotal];

                // Blank row after a inventory component rent details
                $transactions[] = ['make_bold' => false, '', '', '', '', '', '', '', ''];
                $rows = array_merge($rows, $transactions);
            }
            if ($request['type'] === 'pdf') {
                unset($rows[0]);
                $pdf = App::make('dompdf.wrapper');
                $data = [
                    'rows'  => $rows,
                    'projectSiteRentTotal'  => $projectSiteRentTotal,
                    'bill_month'    => $thisMonth . '/' . $thisYear,
                    'projectSite'   => $projectSite
                ];
                $pdf->loadHTML(view('report.rental.pdf', $data));
                return $pdf->stream();
            } else {
                Excel::create('test', function ($excel) use ($rows, $companyHeader, $projectSite, $thisMonth, $thisYear, $projectSiteRentTotal) {
                    $excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);
                    $excel->sheet('Sheet Name 1', function ($sheet) use ($rows, $companyHeader, $projectSite, $thisMonth, $thisYear, $projectSiteRentTotal) {
                        $objDrawing = new \PHPExcel_Worksheet_Drawing();
                        $objDrawing->setPath(public_path('/assets/global/img/logo.jpg')); //your image path
                        $objDrawing->setWidthAndHeight(110, 54);
                        $objDrawing->setResizeProportional(true);
                        $objDrawing->setCoordinates('A1');
                        $objDrawing->setWorksheet($sheet);

                        $sheet->mergeCells('A2:H2');
                        $sheet->cell('A2', function ($cell) use ($companyHeader) {
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('center')->setValignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setValue($companyHeader['company_name']);
                        });

                        $sheet->mergeCells('A3:H3');
                        $sheet->cell('A3', function ($cell) use ($companyHeader) {
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('center')->setValignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setValue($companyHeader['designation']);
                        });

                        $sheet->mergeCells('A4:H4');
                        $sheet->cell('A4', function ($cell) use ($companyHeader) {
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('center')->setValignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setValue($companyHeader['address']);
                        });

                        $sheet->mergeCells('A5:H5');
                        $sheet->cell('A5', function ($cell) use ($companyHeader) {
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('center')->setValignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setValue($companyHeader['contact_no']);
                        });

                        $sheet->mergeCells('A6:H6');
                        $sheet->cell('A6', function ($cell) use ($companyHeader) {
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('center')->setValignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setValue($companyHeader['gstin_number']);
                        });

                        $sheet->mergeCells('A7:H7');
                        $sheet->cell('A7', function ($cell) use ($companyHeader) {
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $sheet->mergeCells('A8:H8');
                        $sheet->getRowDimension(8)->setRowHeight(35);
                        $sheet->cell('A8', function ($cell) use ($projectSite) {
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('center')->setValignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setValue('Monthly Rent Bill');
                            $cell->setBackground('#81A1D1');
                        });

                        $sheet->mergeCells('A9:H9');
                        $sheet->cell('A9', function ($cell) use ($thisMonth, $thisYear) {
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('left')->setValignment('left');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setValue('Billing for the month - ' . $thisMonth . '/' . $thisYear);
                        });
                        $sheet->mergeCells('A10:H10');
                        $sheet->cell('A10', function ($cell) use ($projectSite) {
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('left')->setValignment('left');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setValue('Site Name - ' . $projectSite['name']);
                        });

                        $sheet->mergeCells('A11:H11');
                        $sheet->getRowDimension(11)->setRowHeight(30);
                        $sheet->cell('A11', function ($cell) use ($projectSite) {
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('left');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setValue('Site Address - ' . $projectSite['address']);
                        });
                        $sheet->mergeCells('A12:D12');
                        $sheet->cell('A12', function ($cell) {
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('left')->setValignment('left');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setValue('Invoice No - Rent/'); //TODO add Bill no
                        });

                        $sheet->mergeCells('E12:H12');
                        $sheet->cell('E12', function ($cell) {
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('left')->setValignment('left');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setValue('Date - '); //TODO add date
                        });
                        $sheet->mergeCells('A13:H13');
                        $sheet->cell('A13', function ($cell) {
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        $sheet->cells('A14:H14', function ($cells) {
                            $cells->setBackground('#81A1D1');
                        });
                        $row = 13;
                        foreach ($rows as $key1 => $arow) {
                            $next_column = 'A';
                            $row++;
                            $makeBold = $arow['make_bold'];
                            unset($arow['make_bold']);
                            foreach ($arow as $key2 => $cellData) {
                                $current_column = $next_column++;
                                $sheet->getRowDimension($row)->setRowHeight(20);
                                $sheet->cell($current_column . ($row), function ($cell) use ($cellData, $makeBold, $key2) {
                                    if ($makeBold) {
                                        $cell->setFontWeight('bold');
                                    }
                                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                                    switch ($key2) {
                                        case 3:
                                            $cell->setAlignment('right')->setValignment('right');
                                            break;
                                        case 4:
                                            $cell->setAlignment('right')->setValignment('right');
                                            break;
                                        case 5:
                                            $cell->setAlignment('right')->setValignment('right');
                                            break;
                                        case 7:
                                            $cell->setAlignment('right')->setValignment('right');
                                            break;
                                        case 6:
                                            $cell->setAlignment('left')->setValignment('left');
                                            break;
                                        default:
                                            $cell->setAlignment('center')->setValignment('center');
                                            break;
                                    }
                                    $cell->setValue($cellData);
                                });
                            }
                        }
                        $row++;
                        $sheet->mergeCells('A' . $row . ':G' . $row);
                        $sheet->cell('A' . $row, function ($cell) {
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('center')->setValignment('center');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setValue('Final Rent total');
                        });

                        $sheet->cell('H' . $row, function ($cell) use ($projectSiteRentTotal) {
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('right')->setValignment('right');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setValue($projectSiteRentTotal);
                        });
                    });
                })->export('xls');
            }
        } catch (\Exception $e) {

            $records = array();
            $data = [
                'action' => 'Rental report Excel export',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }

        return response()->json($records, 200);
    }
}
