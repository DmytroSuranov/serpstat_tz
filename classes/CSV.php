<?php

class CSV {
	
	public function saveReport($url, $array_data){
		$filename = md5($url);
		
		header('Content-Type: application/excel');
		header('Content-Disposition: attachment; filename="csvs/'.$filename.'.csv"');
		$data = array(
				'This is report for URL : '.$url,
		);
		foreach($array_data as $key=>$value){
			$data[] = "\t ". $key+1 .". The link is ".$value['link'];
			$data[] = "\t The images are : ";
			foreach($value['images'] as $key=>$image){
				$data[] = "\t\t - ".$image;
			}
		}
		$fp = fopen('csvs/'.$filename.'.csv', 'w');
		foreach ( $data as $line ) {
			$val = explode(",", $line);
			fputcsv($fp, $val);
		}
		fclose($fp);
		echo "The csv file is available by this address : ".realpath('csvs/'.$filename.'.csv');
	}
	
	public function showReport($url){
		$filename = md5($url);
		$file = file_get_contents ('csvs/'.$filename.'.csv');
		echo $file;	
	}

}
?>