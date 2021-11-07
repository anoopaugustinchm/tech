<?php

/* @var $this yii\web\View */
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

$this->title = 'Import Employees';
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent">
        <h1 class="display-4">Import Employees </h1>

    </div>

    <div class="body-content">

        <div class="row">
        
            <div class="col-lg-12">
            <div class="alert alert-info" role="alert">
               The first column number starts from 0
            </div>
            <?php if(!empty($errors)) : ?>
                <div class="alert alert-danger" role="alert">
                <ul>
                <?php foreach($errors as $error) : ?>
                    <li><?= $error[0];?></li>
                <?php endforeach; ?>    
                </ul>
                </div>
            <?php endif; ?>    
            
                <?php $form = ActiveForm::begin(['id' => 'import-form']); ?>
                <?= $form->field($model, 'column_department_id')->textInput(['autofocus' => true]) ?>
                <?= $form->field($model, 'column_employee_age')->textInput() ?>
                <?= $form->field($model, 'column_employee_name')->textInput() ?>
                <?= $form->field($model, 'column_employee_code')->textInput() ?>
                <?= $form->field($model, 'column_date_of_birth')->textInput() ?>
                <?= $form->field($model, 'column_joining_date')->textInput() ?>
                <?= $form->field($model, 'bulkfile')->fileInput() ?>

                <div class="form-group">
                    <?= Html::submitButton('Import', ['class' => 'btn btn-primary btn-block', 'name' => 'import-button']) ?>
                </div>
                <?php ActiveForm::end(); ?>

                
            </div>
        
                
        </div>

    </div>
</div>
