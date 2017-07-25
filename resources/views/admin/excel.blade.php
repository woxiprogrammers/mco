<?php
    echo "<h1>bhgsftdghfyhfjhfhfjhfhfjh</h1>";

    $sheet->row(1, array('SRN','Product with description','Rate','BOQ','W.O.Amount'));
    $next_column = 'F';
    $row = 1;
    for($iterator = 0 ; $iterator < count($data['tillThisBill']); $iterator++,$next_column++){
        $current_column = $next_column++;
        $sheet->getCell($current_column.($row+1))->setValue('Quantity');
        $sheet->getCell(($next_column).($row+1))->setValue('Amount');
        $sheet->mergeCells($current_column.$row.':'.$next_column.$row);
        $sheet->getCell($current_column.$row)->setValue("RA Bill".($iterator+1));
    }
    $next_column_data = array('Total Quantity','Total Amount');
    for($iterator = 0 ; $iterator < count($next_column_data) ; $iterator++,$next_column++){
        $columnData = $next_column_data[$iterator];
        $sheet->cell($next_column.$row, function($cell) use($columnData) {
            $cell->setAlignment('center')->setValignment('center');
            $cell->setValue($columnData);
        });
    }

    $serialNumber = 1;
    $productRow = 4;
    foreach($productArray as $product){
// dd($product);
        $sheet->row($productRow, array($serialNumber,$product['name'],$product['discounted_rate'],$product['BOQ'],$product['WO_amount']));
        $next_column = 'F';
        $row = 1;
        foreach($product['description'] as $description){
// dd($description['description']);
            $productRow++;
            $sheet->cell('B'.$productRow, function($cell) use($description) {
                $cell->setAlignment('center')->setValignment('center');
                $cell->setValue($description['description']);
            });
            /* $sheet->getCell('B'.($productRow))->setValue($description['description']);
            $current_column = $next_column++;
            $sheet->getCell($current_column.($row+1))->setValue($product['bills'][$iterator]['quantity']);
            $sheet->getCell(($next_column).($row+1))->setValue($product['bills'][$iterator]['amount']);
            $sheet->mergeCells($current_column.$row.':'.$next_column.$row);
            $sheet->getCell($current_column.$row)->setValue("RA Bill".($iterator+1));
            $next_column++;*/
        }
        $productRow = $productRow + 1;
        /*$next_column_data = array('Total Quantity','Total Amount');
        for($iterator = 0 ; $iterator < count($next_column_data) ; $iterator++,$next_column++){
        $columnData = $next_column_data[$iterator];
        $sheet->cell($next_column.$row, function($cell) use($columnData) {
        $cell->setAlignment('center')->setValignment('center');
        $cell->setValue($columnData);
        });
        }*/
        $productRow++;
        $serialNumber++;
    }
?>
