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
                        <th style="width: 15%"> Work Area (Sq.ft.)</th>
                        <th style="width: 15%"> Total Amount </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subcontractorStructure->summaries as $subcontractorStructureSummary)
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
                                {!! $subcontractorStructureSummary->total_work_area !!}
                            </td>
                            <td>
                                {!! $subcontractorStructureSummary->rate *  $subcontractorStructureSummary->total_work_area !!}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
