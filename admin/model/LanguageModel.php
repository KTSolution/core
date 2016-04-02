<?php
use Illuminate\Database\Eloquent\Model as Eloquent;

class LanguageModel extends Eloquent {

    protected $table        = TABLE_LANGUAGE;

    static function _make($attributes = array()) {
        $obj = new Member();
        foreach ($attributes as $key => $value) {
            $obj->$key = $value;
        }
        return $obj;
    }

    static function _filter($params){
        if ( count($params) > 0 ){
            foreach ( $params as $key => $value ) {
                $filter = self::where($key, '=', $value);
            }
            $result = $filter->get();
        } else {
            $result = self::get();
        }

        return $result;
    }

    static function _save($params, $id){
        $obj = ( $id > 0 ) ? self::find($id) : $obj = new self;

        if ( count($params) > 0 ){
            foreach ( $params as $key => $value ) {
                $obj->$key = $value;
            }
        }
        $obj->save();
    }
}