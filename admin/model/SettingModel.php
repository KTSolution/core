<?php
use Illuminate\Database\Eloquent\Model as Eloquent;

class SettingModel extends Eloquent {

    protected $table        = TABLE_SETTING;
    
    static function _save_map_info($input){
        $map_info = self::where('key', '=', 'google_map')->first();
        
        if( $map_info ) {
            $map_info->value = $input;
            $map_info->save();
        }
    }
}