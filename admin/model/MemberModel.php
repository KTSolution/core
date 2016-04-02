<?php
use Illuminate\Database\Eloquent\Model as Eloquent;

class Member {
    public $first_name="";
    public $last_name="";
    public $type=-1;
    public $enable=1;
    public $login_name="";
    public $password="";
}

class MemberModel extends Eloquent {

    protected $table        = TABLE_MEMBER;

    
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