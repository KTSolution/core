<?php
use Illuminate\Database\Eloquent\Model as Eloquent;

class Page {
    public $code="";
    public $name="";
    public $title="";
    public $description="";
    public $keyword="";
    public $robot="";
    public $type=0;
    public $enable=1;
}

class PageModel extends Eloquent {

    protected $table        = TABLE_PAGE;

    static function _make($attributes = array()) {
        $obj = new Page();
        foreach ($attributes as $key => $value) {
            $obj->$key = $value;
        }
        return $obj;
    }

    static function _save($params, $id, $json = array(), $lang){
        $obj = ( $id > 0 ) ? self::find($id) : $obj = new self;
            
        if( is_object($params)){
            $params = get_object_vars($params);
        }

        if ( count($params) > 0 ){
            foreach ( $params as $key => $value ) {
                if ( in_array($key, $json)){
                    $value = $lang->set_text($obj->$key, $value, $lang->current);
                }
                $obj->$key = $value;
            }
        }
        $obj->save();
    }
}