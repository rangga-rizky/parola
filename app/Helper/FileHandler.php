<?php

namespace App\Helper;


class FileHandler
{
    public function readCSV($path,$ignore_first){
		$isFirst = true;
		$firstLine = null;
		$file = [];
		$file1 = fopen($path, 'r');
		while (($line = fgetcsv($file1)) !== FALSE) {
  			if($isFirst && $ignore_first == false){
  				$firstLine = $line;
  				$isFirst = false;
  				continue;
  			}
  			$file[] = $line;
  		
		}
		fclose($file1);
		return [
				"firstLine" => $firstLine,
				"file" => $file
				];
    }

    public function readKeyPairCSV($path){
        $file = [];
        try {          
          $file1 = fopen($path, 'r');
          while (($line = fgetcsv($file1)) !== FALSE) {
              $file[$line[0]] = $line[1];
            
          }
          fclose($file1);
        }catch (\Exception $e) {          
          return $file;
        }
        return $file;
    }



    public function writeCSV($path,$data,$firstLine){
    	$fp = fopen($path, 'w');
    	if(!is_null($firstLine)){
    		fputcsv($fp, $firstLine);
    	}
      for ($i=0; $i < sizeof($data) ; $i++) { 
        fputcsv($fp, $data[$i]);
      }

		}
		
		public static function upload($file,$path){
			try{            
	   				$name = time().'.'.$file->getClientOriginalExtension();
						 $destinationPath = public_path($path);
						 $file->move($destinationPath, $name);
					}catch(\Exception $e){
							return null;
					}     
	
			return $name;
		}	


}