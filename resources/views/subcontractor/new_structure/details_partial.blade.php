<?php
/**
 * Created by PhpStorm.
 * User: ameya
 * Date: 31/10/18
 * Time: 10:35 PM
 */
?>

<div class="form-body">
    <div class="row form-group">
        <div class="col-md-3" style="text-align: right">
            <label class="control-label">Project Site : </label>
        </div>
        <div class="col-md-3">
            <span> {!! $subcontractorStructure->projectSite->name !!}</span>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-md-3" style="text-align: right">
            <label class="control-label">Subcontractor : </label>
        </div>
        <div class="col-md-3">
            <span>{!! $subcontractorStructure->subcontractor->company_name !!}</span>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-md-3" style="text-align: right">
            <label class="control-label">Contract Type : </label>
        </div>
        <div class="col-md-3">
            <span> {!! $subcontractorStructure->contractType->name !!} </span>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered table-hover" id="summaryTable">
                <thead>
                    <tr>
                        <th style="width: 20%"> Summary </th>
                        <th style="width: 20%"> Description </th>
                        <th style="width: 15%"> Rate </th>
                        <th style="width: 15%"> Unit </th>
                        <th style="width: 15%"> Work Area (Sq.ft.)</th>
                        <th style="width: 15%"> Total Amount </th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalRate = 0;
                        $totalWorkArea = 0;
                        $totalAmount = 0;
                    @endphp
                    @foreach($subcontractorStructure->summaries as $subcontractorStructureSummary)
                        @php
                            $totalRate += $subcontractorStructureSummary->rate;
                            $totalWorkArea += $subcontractorStructureSummary->total_work_area;
                            $amount = $subcontractorStructureSummary->rate *  $subcontractorStructureSummary->total_work_area;
                            $totalAmount += $amount;
                        @endphp
                        <tr>
                            <td>
                                {!! $subcontractorStructureSummary->summary->name !!}
                            </td>
                            <td>
                                {!! $subcontractorStructureSummary->description !!}
                            </td>
                            <td>
                                {!! $subcontractorStructureSummary->rate !!}
                            </td>
                            <td>
                                @if($subcontractorStructureSummary->unit != null)
                                    {{$subcontractorStructureSummary->unit->name}}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                {!! $subcontractorStructureSummary->total_work_area !!}
                            </td>
                            <td>
                                {!! $amount !!}
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2">
                            <b>Total</b>
                        </td>
                        <td>
                            {!! $totalRate !!}
                        </td>
                        <td>
                            {!! $totalWorkArea !!}
                        </td>
                        <td>
                            {!! $totalAmount !!}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
