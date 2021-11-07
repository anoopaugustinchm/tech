<?php

namespace common\models;

use Yii;
class Department extends \yii\db\ActiveRecord
{    

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'department';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'department_name', 'department_slug'], 'required']
            
            ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'department_name' => 'Deapartment Name', 
            'department_slug' => 'Department Slug'    
        ];
    }
}
