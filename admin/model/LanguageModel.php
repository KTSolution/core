<?php
use Illuminate\Database\Eloquent\Model as Eloquent;

class LanguageModel extends Eloquent {

    protected $table        = TABLE_LANGUAGE;

//    static function _make($attributes = array()) {
//        $obj = new Member();
//        foreach ($attributes as $key => $value) {
//            $obj->$key = $value;
//        }
//        return $obj;
//    }

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

    static function _saveLanguageActive($languages){
        self::where('enable', '=', 1)->update(array('enable' => 0));
        self::where('code', '=', LANG_DEFAULT)->update(array('enable' => 1));

        foreach (array_keys($languages) as $language) {
            self::where('code', '=', $language)->update(array('enable' => 1));
        }
    }

    static function _getLanguageList(){
        $return = array();
        $data = self::where('enable', '=', 1)->select('code', 'name')->get();
        foreach ($data as $item) {
            $return[] = array($item->code, $item->name);
        }

        return $return;
    }

    static function _getCurrentLanguageObj($code=LANG_DEFAULT){
        $result = self::where('enable', '=', 1)
            ->where('code', '=', $code)
            ->select('code', 'directory')
            ->first();

        return $result;
    }
}