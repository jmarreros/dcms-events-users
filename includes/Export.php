<?php

namespace dcms\event\includes;

use dcms\event\backend\includes\sepa\Sepa;
use dcms\event\helpers\Helper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Class for the operations of plugin
class Export{

    public function __construct(){
        add_action('admin_post_process_export_list_customers', [$this, 'process_export_list_customers']);
	    add_action('admin_post_process_export_users_sepa', [$this, 'process_export_users_sepa']);
    }

    // Export data
    public function process_export_list_customers(){
	    // Get Id post
	    $id_post  = intval($_GET['id_post']);
	    // Only Joined?
        $only_joined = $_GET['only_joined']??0;
		// Only selected
	    $only_selected = $_GET['only_selected']??0;

	    // Get fields for header
		if ( $only_selected ){
			$fields = Helper::get_fields_selected_export();
		}  else {
			$fields = Helper::get_fields_inscribed_export();
		}

        $styleArray = Helper::get_style_header_cells();

        // Data
        $db = new Database();
        $rows = $db->select_users_event_export($id_post, $only_joined, $only_selected);

		if ( $only_selected ){
			$rows = $this->get_custom_order_selected_rows($rows);
		}

        $filename = 'list_user_event.xlsx';

		// Export
	    $this->build_export($fields, $rows, $filename,  $styleArray, 'A1:W1');
    }

	// Order by parent and children
	public function get_custom_order_selected_rows($rows): array {
		$parents = [];
		foreach($rows as $row){
			if ($row['identify'] === $row['parent'] || is_null($row['parent'])){
				$parents[] = $row;
			}
		}

		$result = [];
		foreach($parents as $parent){
			$result[] = $parent;
			if ( $parent['identify'] == $parent['parent'] ){
				$children = $this->get_children_parent($parent['identify'], $rows);
				foreach ($children as $child){
					$child['id_order'] = $parent['id_order'];
					$result[] = $child;
				}
			}
		}

		return $result;
	}

	// Auxiliar function for getting children
	private function get_children_parent($identify, $rows): array {
		$children = [];
		foreach($rows as $row){
			if ($row['children'] == 0 && $row['parent'] == $identify){
				$children[] = $row;
			}
		}
		return $children;
	}


	public function process_export_users_sepa(){
		$fields = [
			"identify" => "Identificativo",
			"first_name" => "Nombres",
			"last_name" => "Apellidos",
			"sepa_file_url" => "Archivo",
			"time" => "Fecha"
		];
		$styleArray = Helper::get_style_header_cells();
		$rows = (new Sepa())->get_users_with_sepa();
		$filename = 'list_users_sepa.xlsx';

		$this->build_export($fields, $rows, $filename,  $styleArray, 'A1:E1');
	}

	private function build_export($fields, $rows, $filename, $styleArray, $cellsStyle){
		$spreadsheet = new Spreadsheet();
		$writer = new Xlsx($spreadsheet);
		$sheet = $spreadsheet->getActiveSheet();

		// Fill headers
		$icol = 1;
		foreach ($fields as $value) {
			$sheet->setCellValueByColumnAndRow($icol, 1, $value);
			$icol++;
		}

		$sheet->getStyle($cellsStyle)->applyFromArray($styleArray);

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

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename='. $filename);
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
	}

}