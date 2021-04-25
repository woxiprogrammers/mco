<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Inventory\InventoryManageController;
use App\ProjectSite;
use App\RentalInventoryComponent;
use App\RentalInventoryTransfer;
use App\RentBill;
use App\Unit;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class RentalReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('custom.auth');
    }

    /**
     * Rent Bill manage view
     */
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

    /**
     * Rent Bill Listing
     */
    public function listing(Request $request)
    {
        try {
            $rentBill = new RentBill();
            if ($request->has('project_name')) {
                $projectSiteIds = ProjectSite::join('projects', 'projects.id', 'project_sites.project_id')
                    ->where('projects.name', 'ilike', '%' . $request['project_name'] . '%')->pluck('project_sites.id')->toArray();
                $rentBill = $rentBill->whereIn('project_site_id', $projectSiteIds);
            }
            if ($request->has('month') && $request['month'] != 0) {
                $rentBill = $rentBill->where('month', $request['month']);
            }
            if ($request->has('year') && $request['year'] != 0) {
                $rentBill = $rentBill->where('year', $request['year']);
            }
            if ($request->has('bill_number')) {
                $rentBill = $rentBill->where(DB::raw('id::VARCHAR'), 'ilike', '%' .  $request['bill_number'] . '%');
            }
            $officeProjectSiteId = ProjectSite::where('name', env('OFFICE_PROJECT_SITE_NAME'))->pluck('id')->first();
            $rentBill = $rentBill->whereNotIn('project_site_id', [$officeProjectSiteId])->orderBy('id', 'asc')->get();
            $iTotalRecords = count($rentBill->toArray());
            $records =  array();
            $records['data'] = array();

            if ($request->has('get_total')) {
                $total = $rentBill->sum('total');
                $records['total'] = (float)$total;
            } else {
                $records["recordsFiltered"] = $records["recordsTotal"] = $iTotalRecords;
                for ($iterator = 0, $pagination = $request->start; $iterator < $request->length && $pagination < count($rentBill); $iterator++, $pagination++) {
                    $button = '<button class="btn btn-xs blue">
                                    <a href="/reports/rental/bill/' . $rentBill[$pagination]->id . '?type=xls" style="color: white">
                                    <i class="fa fa-file-excel-o"></i> XLSX </a>
                                    <input type="hidden" name="_token">
                                </button>
                                <button class="btn btn-xs blue">
                                <a href="/reports/rental/bill/' . $rentBill[$pagination]->id . '?type=pdf" style="color: white">
                                <i class="fa fa-file-pdf-o"></i> PDF </a>
                                    <input type="hidden" name="_token">
                                </button>
				<button class="btn btn-xs blue">
                                <a href="/reports/rental/summary/' . $rentBill[$pagination]->id . '?type=xls" style="color: white">
                                <i class="fa fa-file-excel-o"></i> Summary-XLSX </a>
                                <input type="hidden" name="_token">
                            </button>
			    ';
                    $records['data'][$iterator] = [
                        $rentBill[$pagination]->projectSite->project->name,
                        $rentBill[$pagination]['month'],
                        $rentBill[$pagination]['year'],
                        $rentBill[$pagination]['id'],
                        'Manisha Construction',
                        $rentBill[$pagination]['total'],
                        $button
                    ];
                }
            }
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

    /**
     * Excel / PDF rental report export
     */
    public function exportReport(Request $request, $rentBillId)
    {
        try {
            $rentBill = RentBill::find($rentBillId);
            $thisMonth = $rentBill->month;
            $thisYear = $rentBill->year;
            $projectSite = ProjectSite::find($rentBill->project_site_id);
            $startOfTheMonth = Carbon::create($rentBill->year, $rentBill->month, 01, 00, 00, 00);
            $endOfTheMonth = Carbon::create($rentBill->year, $rentBill->month, 01, 00, 00, 00)->endOfMonth();

            $projectSiteRentTotal = $inventoryComponentIterator = 0;
            $unit = Unit::where('slug', 'nos')->select('id', 'name')->first();

            $companyHeader['company_name'] = env('COMPANY_NAME');
            $companyHeader['designation'] = env('DESIGNATION');
            $companyHeader['address'] = env('ADDRESS');
            $companyHeader['contact_no'] = env('CONTACT_NO');
            $companyHeader['gstin_number'] = env('GSTIN_NUMBER');

            $noOfDaysInMonth = $endOfTheMonth->diffInDays($startOfTheMonth) + 1;
            $rentalInventoryTransfers = RentalInventoryTransfer::join('inventory_component_transfers', 'inventory_component_transfers.id', '=', 'rental_inventory_transfers.inventory_component_transfer_id')
                ->join('inventory_transfer_types', 'inventory_transfer_types.id', '=', 'inventory_component_transfers.transfer_type_id')
                ->join('inventory_components', 'inventory_components.id', '=', 'inventory_component_transfers.inventory_component_id')
                ->where('project_site_id', $projectSite['id'])
                ->whereBetween('rental_inventory_transfers.rent_start_date', [$startOfTheMonth, $endOfTheMonth])
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

                $rentalData = RentalInventoryComponent::where('inventory_component_id', $inventoryComponent['id'])->where('month', $thisMonth)->where('year', $thisYear)->first();
                $openingStockForThisMonth = $rentalData['opening_stock'];
                $ratePerUnit = $inventoryComponent->asset->rent_per_day;

                // Opening stock row for a inventry component
                $transactions[] = [
                    'make_bold'    => false, $inventoryComponentIterator, $inventoryComponent['name'], 'Opening Stock', $noOfDaysInMonth, $ratePerUnit, $openingStockForThisMonth, $unit['name'], $noOfDaysInMonth * $ratePerUnit * $openingStockForThisMonth
                ];
                $inventoryTraansfers = $rentalInventoryTransfers->where('inventory_component_id', $inventoryComponent['id'])->sortBy('rent_start_date');
                foreach ($inventoryTraansfers as $inventoryTransfer) {
                    $noOfDays = $endOfTheMonth->diffInDays($inventoryTransfer['rent_start_date']) + 1;
                    $quantity = ($inventoryTransfer['inventory_transfer_type'] === 'OUT') ? -1 * abs($inventoryTransfer['quantity']) : $inventoryTransfer['quantity'];
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
                    'projectSite'   => $projectSite,
                    'billNumber'    => $rentBill->id
                ];
                $pdf->loadHTML(view('report.rental.pdf', $data));
                return $pdf->stream();
            } else {
                Excel::create('test', function ($excel) use ($rows, $companyHeader, $projectSite, $thisMonth, $thisYear, $projectSiteRentTotal, $rentBill) {
                    $excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);
                    $excel->sheet('Sheet Name 1', function ($sheet) use ($rows, $companyHeader, $projectSite, $thisMonth, $thisYear, $projectSiteRentTotal, $rentBill) {
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
                        $sheet->cell('A12', function ($cell) use ($rentBill) {
                            $cell->setFontWeight('bold');
                            $cell->setAlignment('left')->setValignment('left');
                            $cell->setBorder('thin', 'thin', 'thin', 'thin');
                            $cell->setValue('Invoice No - Rent/' . $rentBill['id']);
                        });

                        // $sheet->mergeCells('E12:H12');
                        // $sheet->cell('E12', function ($cell) {
                        //     $cell->setFontWeight('bold');
                        //     $cell->setAlignment('left')->setValignment('left');
                        //     $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        //     $cell->setValue('Date - '); //TODO add date
                        // });
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

    /**
     * Excel summary rental report export
     */
    public function exportSummaryReport(Request $request, $rentBillId)
    {
        try {
            $rentBill = RentBill::find($rentBillId);
            $thisMonth = $rentBill->month;
            $thisYear = $rentBill->year;
            $projectSite = ProjectSite::find($rentBill->project_site_id);
            $startOfTheMonth = Carbon::create($rentBill->year, $rentBill->month, 01, 00, 00, 00);
            $endOfTheMonth = Carbon::create($rentBill->year, $rentBill->month, 01, 00, 00, 00)->endOfMonth();

            $projectSiteRentTotal = $inventoryComponentIterator = 0;
            $unit = Unit::where('slug', 'nos')->select('id', 'name')->first();

            $companyHeader['company_name'] = env('COMPANY_NAME');
            $companyHeader['designation'] = env('DESIGNATION');
            $companyHeader['address'] = env('ADDRESS');
            $companyHeader['contact_no'] = env('CONTACT_NO');
            $companyHeader['gstin_number'] = env('GSTIN_NUMBER');

            $noOfDaysInMonth = $endOfTheMonth->diffInDays($startOfTheMonth) + 1;
            $rentalInventoryTransfers = RentalInventoryTransfer::join('inventory_component_transfers', 'inventory_component_transfers.id', '=', 'rental_inventory_transfers.inventory_component_transfer_id')
                ->join('inventory_transfer_types', 'inventory_transfer_types.id', '=', 'inventory_component_transfers.transfer_type_id')
                ->join('inventory_components', 'inventory_components.id', '=', 'inventory_component_transfers.inventory_component_id')
                ->where('project_site_id', $projectSite['id'])
                ->whereBetween('rental_inventory_transfers.rent_start_date', [$startOfTheMonth, $endOfTheMonth])
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
                ['make_bold' => true, 'Sr No', 'Name', 'Closing stock pre month', 'Closing stock current month', 'Amount'],
            ];
            $inventoryComponents = $projectSite->inventoryComponents->where('is_material', false);
            foreach ($inventoryComponents as $inventoryComponent) {
                $inventoryComponentIterator++;
                $transactions = [];

                $rentalData = RentalInventoryComponent::where('inventory_component_id', $inventoryComponent['id'])->where('month', $thisMonth)->where('year', $thisYear)->first();
                $openingStockForThisMonth = $rentalData['opening_stock'];
                $ratePerUnit = $inventoryComponent->asset->rent_per_day;

                // Opening stock row for a inventry component
                $transactions[] = [
                    'make_bold'    => false, $inventoryComponentIterator, $inventoryComponent['name'], 'Opening Stock', $noOfDaysInMonth, $ratePerUnit, $openingStockForThisMonth, $unit['name'], $noOfDaysInMonth * $ratePerUnit * $openingStockForThisMonth
                ];
                $inventoryTraansfers = $rentalInventoryTransfers->where('inventory_component_id', $inventoryComponent['id'])->sortBy('rent_start_date');
                foreach ($inventoryTraansfers as $inventoryTransfer) {
                    $noOfDays = $endOfTheMonth->diffInDays($inventoryTransfer['rent_start_date']) + 1;
                    $quantity = ($inventoryTransfer['inventory_transfer_type'] === 'OUT') ? -1 * abs($inventoryTransfer['quantity']) : $inventoryTransfer['quantity'];
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

                $rows[] = [
                    'make_bold'    => false, $inventoryComponentIterator, $inventoryComponent['name'], $openingStockForThisMonth, $closingStock, $inventoryComponentRentTotal
                ];
            }
            Excel::create('Summary Report', function ($excel) use ($rows, $companyHeader, $projectSite, $thisMonth, $thisYear, $projectSiteRentTotal, $rentBill) {
                $excel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);
                $excel->sheet('Sheet Name 1', function ($sheet) use ($rows, $companyHeader, $projectSite, $thisMonth, $thisYear, $projectSiteRentTotal, $rentBill) {
                    $objDrawing = new \PHPExcel_Worksheet_Drawing();
                    $objDrawing->setPath(public_path('/assets/global/img/logo.jpg')); //your image path
                    $objDrawing->setWidthAndHeight(110, 54);
                    $objDrawing->setResizeProportional(true);
                    $objDrawing->setCoordinates('A1');
                    $objDrawing->setWorksheet($sheet);

                    $sheet->mergeCells('A2:E2');
                    $sheet->cell('A2', function ($cell) use ($companyHeader) {
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center')->setValignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setValue($companyHeader['company_name']);
                    });

                    $sheet->mergeCells('A3:E3');
                    $sheet->cell('A3', function ($cell) use ($companyHeader) {
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center')->setValignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setValue($companyHeader['designation']);
                    });

                    $sheet->mergeCells('A4:E4');
                    $sheet->cell('A4', function ($cell) use ($companyHeader) {
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center')->setValignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setValue($companyHeader['address']);
                    });

                    $sheet->mergeCells('A5:E5');
                    $sheet->cell('A5', function ($cell) use ($companyHeader) {
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center')->setValignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setValue($companyHeader['contact_no']);
                    });

                    $sheet->mergeCells('A6:E6');
                    $sheet->cell('A6', function ($cell) use ($companyHeader) {
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center')->setValignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setValue($companyHeader['gstin_number']);
                    });

                    $sheet->mergeCells('A7:E7');
                    $sheet->cell('A7', function ($cell) use ($companyHeader) {
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    $sheet->mergeCells('A8:E8');
                    $sheet->getRowDimension(8)->setRowHeight(35);
                    $sheet->cell('A8', function ($cell) use ($projectSite) {
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center')->setValignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setValue('Monthly Rent Bill');
                        $cell->setBackground('#81A1D1');
                    });

                    $sheet->mergeCells('A9:E9');
                    $sheet->cell('A9', function ($cell) use ($thisMonth, $thisYear) {
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('left')->setValignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setValue('Billing for the month - ' . $thisMonth . '/' . $thisYear);
                    });
                    $sheet->mergeCells('A10:E10');
                    $sheet->cell('A10', function ($cell) use ($projectSite) {
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('left')->setValignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setValue('Site Name - ' . $projectSite['name']);
                    });

                    $sheet->mergeCells('A11:E11');
                    $sheet->getRowDimension(11)->setRowHeight(30);
                    $sheet->cell('A11', function ($cell) use ($projectSite) {
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setValue('Site Address - ' . $projectSite['address']);
                    });
                    $sheet->mergeCells('A12:D12');
                    $sheet->cell('A12', function ($cell) use ($rentBill) {
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('left')->setValignment('left');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setValue('Invoice No - Rent/' . $rentBill['id']);
                    });

                    // $sheet->mergeCells('E12:H12');
                    // $sheet->cell('E12', function ($cell) {
                    //     $cell->setFontWeight('bold');
                    //     $cell->setAlignment('left')->setValignment('left');
                    //     $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    //     $cell->setValue('Date - '); //TODO add date
                    // });
                    $sheet->mergeCells('A13:E13');
                    $sheet->cell('A13', function ($cell) {
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    $sheet->cells('A14:E14', function ($cells) {
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
                    $sheet->mergeCells('A' . $row . ':D' . $row);
                    $sheet->cell('A' . $row, function ($cell) {
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('center')->setValignment('center');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setValue('Final Rent total');
                    });

                    $sheet->cell('E' . $row, function ($cell) use ($projectSiteRentTotal) {
                        $cell->setFontWeight('bold');
                        $cell->setAlignment('right')->setValignment('right');
                        $cell->setBorder('thin', 'thin', 'thin', 'thin');
                        $cell->setValue($projectSiteRentTotal);
                    });
                });
            })->export('xls');
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


    /**
     * Rent calculation cron example.
     * ## TO BE DELETED
     */
    public function rentCalculationCron(Request $request)
    {
        try {
            $firstDayOfTheMonth = Carbon::now()->startOfMonth();
            $thisMonth = $firstDayOfTheMonth->format('m');
            $thisYear = $firstDayOfTheMonth->format('Y');
            $lastDayOfTheMonth = Carbon::now()->endOfMonth();
            dd(Carbon::create(2021, 2, 1, 0, 0, 0), $firstDayOfTheMonth);
            $projectSite = ProjectSite::first();
            //foreach ($projectSites as $projectSite) {
            $projectSiteRentTotal = $inventoryComponentIterator = 0;
            $unit = Unit::where('slug', 'nos')->select('id', 'name')->first();
            $controller = new InventoryManageController;
            $rentalInventoryTransfers = RentalInventoryTransfer::join('inventory_component_transfers', 'inventory_component_transfers.id', '=', 'rental_inventory_transfers.inventory_component_transfer_id')
                ->join('inventory_transfer_types', 'inventory_transfer_types.id', '=', 'inventory_component_transfers.transfer_type_id')
                ->join('inventory_components', 'inventory_components.id', '=', 'inventory_component_transfers.inventory_component_id')
                ->where('project_site_id', $projectSite['id'])
                ->whereBetween('rental_inventory_transfers.rent_start_date', [$firstDayOfTheMonth, $lastDayOfTheMonth])
                ->select(
                    'rental_inventory_transfers.id',
                    'rental_inventory_transfers.inventory_component_transfer_id',
                    'rental_inventory_transfers.quantity',
                    'rental_inventory_transfers.rent_per_day',
                    'rental_inventory_transfers.rent_start_date',
                    'inventory_component_transfers.inventory_component_id',
                    'inventory_transfer_types.type as inventory_transfer_type'
                )->get();

            $inventoryComponents = $projectSite->inventoryComponents->where('is_material', false);
            foreach ($inventoryComponents as $inventoryComponent) {
                $inventoryComponentIterator++;
                $transactionTotal = $transactionQuantity = 0;
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
                $noOfDays = $lastDayOfTheMonth->diffInDays($firstDayOfTheMonth) + 1;

                // Opening stock total for a inventry component
                $openingStockTotal = $noOfDays * $ratePerUnit * $openingStockForThisMonth;

                $inventoryTraansfers = $rentalInventoryTransfers->where('inventory_component_id', $inventoryComponent['id'])->sortBy('rent_start_date');
                foreach ($inventoryTraansfers as $inventoryTransfer) {
                    $noOfDays = $lastDayOfTheMonth->diffInDays($inventoryTransfer['rent_start_date']) + 1;
                    $quantity = ($inventoryTransfer['inventory_transfer_type'] === 'IN') ? -1 * abs($inventoryTransfer['quantity']) : $inventoryTransfer['quantity'];
                    $rentPerDay = (float)$inventoryTransfer['rent_per_day'];
                    $total = $quantity * $rentPerDay * $noOfDays;

                    $transactionTotal += $total;
                    $transactionQuantity +=  $quantity;
                }
                // Closing stock row
                $closingStock = $openingStockForThisMonth + $transactionQuantity;
                $rentalData->update(['closing_stock'    => $closingStock]);
                $projectSiteRentTotal += ($openingStockTotal  + $transactionTotal);
            }
            $rentBillRecord = RentBill::where('project_site_id', $projectSite['id'])->where('month', $thisMonth)->where('year', $thisYear)->first();
            if ($rentBillRecord) {
                $rentBillRecord->update(['total' => $projectSiteRentTotal]);
            } else {
                $rentBillRecord = RentBill::create([
                    'project_site_id'   => $projectSite['id'],
                    'month'             => $thisMonth,
                    'year'              => $thisYear,
                    // 'bill_number'       => 1,
                    'total'             => $projectSiteRentTotal
                ]);
            }
            // }
        } catch (Exception $e) {
            $data = [
                'action' => 'Rent Calculation Cron',
                'params' => $request->all(),
                'exception' => $e->getMessage()
            ];
            Log::critical(json_encode($data));
            abort(500);
        }
    }
}
