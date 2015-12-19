<?php

namespace app\models;

use Yii;

class AppActiveRecord extends \yii\db\ActiveRecord
{
    /**
     * Redefine standard toArray method for avoiding showing atributes with null value
     * null attributes needed for exended view of many-to-many relation
     * @param array $fields
     * @param array $expand
     * @param bool|true $recursive
     * @return array
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        return array_filter(parent::toArray($fields,$expand,$recursive), function($val){
            return is_null($val) ? false : true;
        });
    }
}