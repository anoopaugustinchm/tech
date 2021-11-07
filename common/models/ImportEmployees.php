<?php

namespace common\models;

use Yii;
class ImportEmployees extends \yii\db\ActiveRecord
{
    public $bulkfile;
    public $column_department_id;
    public $column_employee_age;
    public $column_employee_name;
    public $column_employee_code;
    public $column_date_of_birth;
    public $column_joining_date;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'employees';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'department_id', 'employee_age', 'employee_name', 'employee_code', 'date_of_birth', 'joining_date', 'created_at', 'updated_at', 'user_id'], 'required', 'on'=>'update_employees'],
            [['bulkfile', 'column_joining_date', 'column_date_of_birth', 'column_employee_code' ,'column_employee_name', 'column_employee_age', 'column_department_id'],'required','on'=>'employees_bulk'],
            [['bulkfile'], 'file','extensions' => 'csv'],
            [['department_id'], 'string'],
            [['user_id'], 'integer'],
            [['employee_age'], 'integer'],
            ['employee_name','match','pattern'=>'/^[a-zA-Z\s]*$/','message'=>'Invalid Employee Name'],
            ['employee_code','match','pattern'=>'/^[a-zA-Z0-9]*$/', 'message'=>'Invalid Employee Code'],
            [['joining_date'], 'date', 'format' => 'php:Y-m-d'],
            [['date_of_birth'], 'date', 'format' => 'php:Y-m-d'],
            [['column_joining_date', 'column_date_of_birth', 'column_employee_code' ,'column_employee_name', 'column_employee_age', 'column_department_id'],'integer'],
            [['employee_code'],'unique'],
            [['date_of_birth', 'joining_date'], 'compare', 'compareValue' => date("Y-m-d"),'operator' => '<='],
            [['department_id'], 'exist', 'skipOnError' => true, 'targetClass' => Department::className(), 'targetAttribute' => ['department_id' => 'department_slug']],
            ['department_id','match','pattern'=>'/^\S*$/', 'message'=>'Invalid Department Slug'],
            ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bulkfile' => 'Select CSV File', 
            'column_joining_date' => 'Enter CSV Column number of joining date',
            'column_date_of_birth' => 'Enter CSV Column number of date of birth', 
            'column_employee_code' => 'Enter CSV Column number of employee code',
            'column_employee_name' => 'Enter CSV Column number of employee name', 
            'column_employee_age' => 'Enter CSV Column number of employee age', 
            'column_department_id'=> 'Enter CSV Column number of department slug'
        ];
    }

    /**
     * function to get department data 
     */
    public function getDepartment()
    {
        return $this->hasOne(Department::className(), ['id' => 'department_id']);
    }
}
