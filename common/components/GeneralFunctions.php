<?php
namespace common\components;

use Yii;
use yii\helpers\Url;
use yii\helpers\BaseFileHelper;
use yii\base\Component;

class GeneralFunctions extends Component{

    /**
     * Function to check the given colummn nunber exists
     * 
     *  @param array $columnIndex
     *  @param array  $csv_row
     * 
     *  @return boolean
     */
    public function columnExists($columnIndex, $csv_row)
    {
        foreach($columnIndex as $column){

            if(!isset($csv_row[$column]))
            {
                Yii::$app->session->setFlash('error', "Some invalid content exists in csv file");
                return false;
            } 
        }
        
        return true;
    }
    /**
     * 
     */
    function dateDifference($date_1 , $differenceFormat = '%a' )
    {
        $datetime1 = date_create($date_1);
        $datetime2 = date_create(date('Y-m-d'));

        $interval = date_diff($datetime1, $datetime2);

        return $interval->format($differenceFormat);

    }

    /**
     * Function to redirect user on role
     */
    function roleRedirection()
    {
        if(Yii::$app->user->identity->role == 'customer')
        {
            //return Yii::$app->getResponse()->redirect('site/import');
        }    
    }
}
