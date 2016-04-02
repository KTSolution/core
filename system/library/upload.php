<?php

class Upload {
    
    public function __construct()
    {
    }
    
    private function convert_filename($file_name){
        $file_name  = strtolower(trim($file_name));
        $file_name  = preg_replace("/[^a-z0-9_.-]/i", "-", $file_name);
        $file_name  = str_replace("(", "-", $file_name);
        $file_name  = str_replace(")", "-", $file_name);
        return $file_name;
    }

    private function get_unique_file_name($path, $file_name) {
        $dest = "{$path}/{$file_name}";
        if ( !file_exists($dest) ) {
            return $file_name;
        }
        $file_name = str_replace('.', '-' . rand() . '.', $file_name);
        return $this->get_unique_file_name($path, $file_name);
    }
    
    public function save($path, $file_name, $file, $overide=false){
        $file_name = $this->convert_filename($file_name);
        if ( !$overide ) {
            $file_name = $this->get_unique_file_name($path, $file_name);
        }

        //save file.
        if ( move_uploaded_file($file['tmp_name'], "{$path}/{$file_name}" ) === FALSE ) {
            return FALSE;
        }
        else {
            return $file_name;
        }
    }
}