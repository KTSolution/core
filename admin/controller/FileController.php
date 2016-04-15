<?php

/**
 * Created by PhpStorm.
 * User: Nam Dinh
 * Date: 3/13/2016
 * Time: 5:32 PM
 */
class FileController extends Controller
{

    public function upload(){
        try{
            $path = DIR_UPLOAD;
            $file_name = "home.png";
            $file['tmp_name'] = "home.png";
            $override  = true;
            $file_name = $this->convert_filename($file_name);
            if ( !$override ) {
                $file_name = $this->get_unique_file_name($path, $file_name);
            }
$data = array();
            $data['error'] = "sssss";
            $data['status'] = 0;
            return $data;
            //save file.
            if ( move_uploaded_file($file['tmp_name'], "{$path}/{$file_name}" ) === FALSE ) {
                return FALSE;
            }
            else {
                return $file_name;
            }

        } catch (Exception $ex){
            throw $ex;
        }
    }

    public function delete(){
        try{
            $data ="";

            return $data;

        } catch (Exception $ex){
            throw $ex;
        }
    }

    public function convert_filename($file_name) {
        $file_name  = strtolower(trim($file_name));
        $file_name  = preg_replace("/[^a-z0-9_.-]/i", "-", $file_name);
        $file_name  = str_replace("(", "-", $file_name);
        $file_name  = str_replace(")", "-", $file_name);
        return $file_name;
    }

    public function get_unique_file_name($path, $file_name) {
        $dest = "{$path}/{$file_name}";
        if ( !file_exists($dest) ) {
            return $file_name;
        }
        $file_name = str_replace('.', '-' . rand() . '.', $file_name);
        return $this->get_unique_file_name($path, $file_name);
    }
}