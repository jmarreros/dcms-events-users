<?php

namespace dcms\event\includes;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Class for the operations of plugin
class Export{

    public function __construct(){
        error_log(print_r('Ingreso al consturctor',true));
        add_action('admin_post_process_export_list_customers', [$this, 'process_export_list_data']);
        // add_action('admin_post_nopriv_process_export_list_customers', [$this, 'process_export_list_data']);
    }

    // Export data
    public function process_export_list_data(){
        $spreadsheet = new Spreadsheet();
        $writer = new Xlsx($spreadsheet);

        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $sheet->setCellValue('A1', 'Identificador');
        $sheet->setCellValue('B1', 'PIN');
        $sheet->setCellValue('C1', 'Correo');
        $sheet->setCellValue('D1', 'Fecha');

        $filename = 'list_customers.xlsx';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='. $filename);
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }

}