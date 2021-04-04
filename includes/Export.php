<?php

namespace dcms\event\includes;

use dcms\event\helpers\Helper;
use dcms\event\includes\Database;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Class for the operations of plugin
class Export{

    public function __construct(){
        add_action('admin_post_process_export_list_customers', [$this, 'process_export_list_data']);
    }

    // Export data
    public function process_export_list_data(){
        $spreadsheet = new Spreadsheet();
        $writer = new Xlsx($spreadsheet);
        $sheet = $spreadsheet->getActiveSheet();

        // Get Id post
        $id_post  = intval($_GET['id_post']);

        // Get fields
        $fields = Helper::get_fields_export();

        // Fill headers
        $icol = 1;
        foreach ($fields as $value) {
            $sheet->setCellValueByColumnAndRow($icol, 1, $value);
            $icol++;
        }
        $styleArray = Helper::get_style_header_cells();
        $sheet->getStyle('A1:S1')->applyFromArray($styleArray);

        // Data
        $db = new Database();
        $items = $db->select_users_event_export($id_post);

        $rows = Helper::transform_columns_arr($items);
        Helper::order_array_column($rows); // Order by number

        // Fill excel body
        $irow = 2;
        foreach ($rows as $row) {
            $icol = 1;
            foreach ($fields as $key => $value) {
                if ( isset($row[$key]) ){
                    $sheet->setCellValueByColumnAndRow($icol, $irow, $row[$key]);
                }
                $icol++;
            }
            $irow++;
        }

        $filename = 'list_user_event.xlsx';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='. $filename);
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }

}