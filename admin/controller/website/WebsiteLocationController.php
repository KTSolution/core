<?php

/**
 * Created by PhpStorm.
 * User: Nam Dinh
 * Date: 3/13/2016
 * Time: 5:32 PM
 */
class WebsiteLocationController extends Controller
{

    public function index(){
        try{
            $request = $this->request->request;
            $session = $this->session->data;
            $language = $this->language;
            $data = $setting = $paging = $sidebar = $filter = $test = $header = $list = array();
            $user = new stdClass(); $user->type = 1;
            $global = __global($user->type);


            $sidebar = $this->load->json("common/SideBar");


            $google_map = $this->config->get('google_map');
            $map_info = ($google_map);

            $map_type   = isset($map_info['marker_type']) ? $map_info['marker_type'] : MAP_GOOGLE;
            $map_layout = isset($map_info['map_layout']) ? $map_info['map_layout'] : MAP_GOOGLE_LAYOUT_MAP;
            $map_zoom   = isset($map_info['map_zoom']) ? $map_info['map_zoom'] : 17;
//            $map_title   = isset($map_info['hotel_name']) ? $map_info['hotel_name'] : "";

            $mIndex = 0;
            $mTitle = isset($map_info['hotel_name']) ? $map_info['hotel_name'] : "";
            $mTitleOrg = "";
            $mLat = isset($map_info['map_lat']) ? $map_info['map_lat'] : 0;
            $mLng = isset($map_info['map_lng']) ? $map_info['map_lng'] : 0;
            $mType = isset($map_info['marker_type']) ? $map_info['marker_type'] : MAP_GOOGLE;
            $mSaved = true;

            $markerList = array(
                array(
                    'mIndex' => $mIndex,
                    'mTitle' => $language->get_text($mTitle, $language->current ),
                    'mTitleOrg' => $mTitleOrg,
                    'mLat' => $mLat,
                    'mLng' => $mLng,
                    'mType' => $mType,
                    'mSaved' => $mSaved
                ),
            );
            // Add other markers
            if ( isset($map_info['marker']) && is_array($map_info['marker']) && count($map_info['marker']) ) {
                foreach ( $map_info['marker'] as $key => $obj ) {
                    $marker = get_object_vars($obj);
                    $mTitel = $language->get_text($marker['mTitle'], $language->current );

                    array_push($markerList, array(
                        'mIndex' => $key + 1,
                        'mTitle' => $mTitel,
                        'mTitleOrg' => $marker['mTitle'],
                        'mLat' => (float) $marker['mLat'],
                        'mLng' => (float) $marker['mLng'],
                        'mType' => (int) $marker['mType'],
                        'mSaved' => true
                    ));
                }
            }

            $typeList = array(
                MAP_GOOGLE_MARK_SHOPPING => "Shopping",
                MAP_GOOGLE_MARK_RESTAURANT => "Restaurant",
                MAP_GOOGLE_MARK_HOTEL => "Hotel",
            );
            $iconList = array(
                MAP_GOOGLE_MARK_SHOPPING => "Shopping",
                MAP_GOOGLE_MARK_RESTAURANT => "Restaurant",
                MAP_GOOGLE_MARK_HOTEL => "Hotel",
            );

            $setting['map_layout'] = $map_layout;
            $setting['map_type'] = $map_type;
            $setting['markerData'] = array(
                'mapLayout' => $map_layout,
                'list'      => $markerList,
                'typeList'  => $typeList,
                'iconList'  => $iconList,
                'zoomLevel' => $map_zoom
            );

            // render to js
            $data['test']               = $language;
            $data['header']             = $header;
            $data['filter']             = $filter;
            $data['list']               = $list;
            $data['global']             = $global;
            $data['sidebar']            = $sidebar;
            $data['paging']             = $paging;
            $data['setting']            = $setting;

            return $data;

        } catch (Exception $ex){
            throw $ex;
        }
    }

    public function save() {
        $post = $this->request->post;
        $language = $this->language;

        $markerList    = isset($post['MarkListSave']) ? $post['MarkListSave'] : '';
        $markerList    = str_replace('&quot;', '"', $markerList);

        $markerList    = json_decode(($markerList));

        $map_info = new stdClass();
        if ( count($markerList) ) {
            $marker = array_shift($markerList);
            /* Update lat,lng for hotel default marker */
            if ( $marker->mIndex == 0 ) {
                $map_info->map_lat     = (float) $marker->mLat;
                $map_info->map_lng     = (float) $marker->mLng;
                $map_info->marker_type = $marker->mType;
                $map_info->map_zoom    = empty($post['map_zoom_save']) ? 16 : (float) $post['map_zoom_save'];
                $map_info->hotel_name  = $language->set_text($marker->mTitleOrg, $marker->mTitle, $language->current);
                $map_info->map_layout  = isset($post['map_layout']) ? $post['map_layout'] : MAP_GOOGLE_LAYOUT_MAP;
            }
            /* Update data for other markers */
            $hotelMarkerList = array();
            if ( count($markerList) ) {
                foreach ($markerList as $marker) {
                    if (!is_null($marker) && ($marker->mIndex != 0) && ($marker->mIndex != -1)) {
                        $hotel_marker =  new stdClass();
                        $hotel_marker->mTitle = $language->set_text($marker->mTitleOrg, $marker->mTitle, $language->current);
                        $hotel_marker->mLat   = $marker->mLat;
                        $hotel_marker->mLng   = $marker->mLng;
                        $hotel_marker->mType  = $marker->mType;

                        $hotelMarkerList[]    = $hotel_marker;
                    }
                }
                $map_info->marker = $hotelMarkerList;
            }
        }

        $input = serialize($map_info);

        $mSetting = $this->model("Setting");
        $mSetting::_save_map_info($input);
        
        return $this->index();
    }

    public function save_map(){
        $post = $this->request->post;
        $language = $this->language;

        $markerList    = isset($post['MarkListSave']) ? $post['MarkListSave'] : '';
        $markerList    = str_replace('&quot;', '"', $markerList);

        $markerList    = json_decode(($markerList));

        $map_info = new stdClass();
        if ( count($markerList) ) {
            $marker = array_shift($markerList);
            /* Update lat,lng for hotel default marker */
            if ( $marker->mIndex == 0 ) {
                $map_info->map_lat     = (float) $marker->mLat;
                $map_info->map_lng     = (float) $marker->mLng;
                $map_info->marker_type = $marker->mType;
                $map_info->map_zoom    = empty($post['map_zoom_save']) ? 16 : (float) $post['map_zoom_save'];
                $map_info->hotel_name  = $language->set_text($marker->mTitleOrg, $marker->mTitle, $language->current);
                $map_info->map_layout  = isset($post['map_layout']) ? $post['map_layout'] : MAP_GOOGLE_LAYOUT_MAP;
            }
            /* Update data for other markers */
            $hotelMarkerList = array();
            if ( count($markerList) ) {
                foreach ($markerList as $marker) {
                    if (!is_null($marker) && ($marker->mIndex != 0) && ($marker->mIndex != -1)) {
                        $hotel_marker =  new stdClass();
                        $hotel_marker->mTitle = $language->set_text($marker->mTitleOrg, $marker->mTitle, $language->current);
                        $hotel_marker->mLat   = $marker->mLat;
                        $hotel_marker->mLng   = $marker->mLng;
                        $hotel_marker->mType  = $marker->mType;

                        $hotelMarkerList[]    = $hotel_marker;
                    }
                }
                $map_info->marker = $hotelMarkerList;
            }
        }

        $input = serialize($map_info);

        $mSetting = $this->model("Setting");
        $mSetting::_save_map_info($input);
    }
}