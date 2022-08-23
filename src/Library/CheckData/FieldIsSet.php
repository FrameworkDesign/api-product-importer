<?php

namespace Weareframework\ApiProductImporter\Library\CheckData;

trait FieldIsSet {

    /**
     * @param $item
     * @param $key
     * @return bool
     */
    public function fieldIsSet($item, $key)
    {
        if( isset($item[$key]) ){
            if( $item[$key] !== '' ){
                return true;
            }
        }

        return false;
    }

    /**
     * @param $item
     * @param $key
     * @return int
     */
    public function intIsSet($item, $key)
    {
        if( isset($item[$key]) ){
            return ( $item[$key] == 1 ) ? 1 : 0;
        }

        return 0;
    }

}
