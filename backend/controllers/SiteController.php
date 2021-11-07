<?php

namespace backend\controllers;

use common\models\LoginForm;
use common\models\ImportEmployees;
use common\models\Department;
use yii\web\UploadedFile;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\data\Pagination; 

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'import'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $page_number = Yii::$app->getRequest()->getQueryParam('page');
        $per_page = Yii::$app->getRequest()->getQueryParam('per_page');

        $query = ImportEmployees::find()->orderBy("id DESC");

        // get the total number of articles (but do not fetch the article data yet)
        $count = $query->count();

        // create a pagination object with the total count
        $pagination = new Pagination(['totalCount' => $count, 'pageSize'=>5]);

        // limit the query using the pagination and retrieve the articles
        $employees = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        return $this->render('index',['employees' => $employees, 'pagination' => $pagination]);
    }

    /**
     * Login action.
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'blank';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            if(Yii::$app->user->identity->role == 'customer')
            {
                return $this->redirect(['customer/index']);
            }
            else if(Yii::$app->user->identity->role == 'shop')
            {
                return $this->redirect(['shop/index']); 
            }
            
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Function to import employees through csv.
     *
     * @return string
     */
    public function actionImport()
    {
        $errors = [];
        $error_occured = 0;
        $model = new ImportEmployees();
		$model->scenario = 'employees_bulk';
        if ($model->load(Yii::$app->request->post()) ){

            $model->bulkfile = UploadedFile::getInstance($model, 'bulkfile');
            $column_ids = [
                $model->column_department_id,
                $model->column_employee_age,
                $model->column_date_of_birth,
                $model->column_employee_name,
                $model->column_employee_code,   
                $model->column_joining_date    
            ];

            if(count($column_ids) != count(array_unique($column_ids)))
            {
                Yii::$app->session->setFlash('error', "Column number must be unique");
                $error_occured = 1;
            }

			if ($model->bulkfile) 
            {       
				
				$filename = uniqid().$model->bulkfile->name;
				$uploadPath = Yii::getAlias('@webroot')."/bulk-user/";
				if (!is_dir($uploadPath))
                {
					mkdir($uploadPath);
					chmod($uploadPath, 0775);
				}
				$file_main_path = $uploadPath.$filename;
				if($model->bulkfile->saveAs($file_main_path) )
                {
                    $fileHandler=fopen($file_main_path,'r');
                    if($fileHandler && $error_occured == 0)
                    {
                        $employee_array_for_batchinsert = [];
                        while($line=fgetcsv($fileHandler,1000)){

                            $model_employees = new ImportEmployees(['scenario' => 'update_employees']);                            
                            
                            $response_column_exists = Yii::$app->generalFunctions->columnExists($column_ids, $line);
                            if(!$response_column_exists)
                            {
                                Yii::$app->session->setFlash('error', "Invalid column id");
                                $error_occured = 1;
                                break;
                            }
                            $model_employees->department_id = $line[$model->column_department_id];
                            $model_employees->employee_age = $line[$model->column_employee_age];
                            $model_employees->date_of_birth = $line[$model->column_date_of_birth];
                            $model_employees->employee_name = $line[$model->column_employee_name];
                            $model_employees->employee_code = $line[$model->column_employee_code];
                            $model_employees->joining_date = $line[$model->column_joining_date];
                            $model_employees->created_at = date("Y-m-d H:i:s");
                            $model_employees->updated_at = date("Y-m-d H:i:s");
                            $model_employees->user_id = Yii::$app->user->id;

                            if($model_employees->validate())
                            {
                                $department_data = Department::find()->WHERE(['department_slug'=>$model_employees->department_id])->one();
                                $employee_array_for_batchinsert [] =[
                                    'department_id' => $department_data->id , 
                                    'employee_age' => $model_employees->employee_age, 
                                    'employee_name' => $model_employees->employee_name, 
                                    'employee_code' => $model_employees->employee_code, 
                                    'date_of_birth' => $model_employees->date_of_birth, 
                                    'joining_date' => $model_employees->joining_date, 
                                    'created_at' => $model_employees->created_at, 
                                    'updated_at' => $model_employees->updated_at, 
                                    'user_id' => $model_employees->user_id
                                ];
                                

                            }
                            else
                            {
                                Yii::$app->session->setFlash('error', "Some invalid content exists in csv file");
                                $error_occured = 1;
                                $errors = $model_employees->errors;
                                break;
                            }
                        }

                        if(count($employee_array_for_batchinsert) > 20)
                        {
                            Yii::$app->session->setFlash('error', "Maximum 20 rows allowed on csv file");
                            $error_occured = 1;
                        }

                        if($error_occured == 0)
                        {
                            $connection = Yii::$app->db;
                            $table_fields = ['department_id', 'employee_age', 'employee_name', 'employee_code', 'date_of_birth', 'joining_date', 'created_at', 'updated_at', 'user_id']; 
                            if($connection->createCommand()->batchInsert('employees', $table_fields, $employee_array_for_batchinsert)->execute())
                            {
                                Yii::$app->session->setFlash('success', "Employees imported success fully");
                                return $this->goHome();
                            }
                            else
                            {
                                Yii::$app->session->setFlash('error', "An error occured in saving database");
                            }
                        }
                        
                        //Yii::$app->generalFunctions->message();
                    }
					
				}
                else
                {

					Yii::$app->session->setFlash('error', "File was not successfully uploaded." );
					return $this->goHome();

				}
            }    
            
            
        }
        return $this->render('import',[
            'model' => $model, 'errors' => $errors
        ]);
    }

}
