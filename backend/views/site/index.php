<?php

/* @var $this yii\web\View */

$this->title = 'My Organization Application';
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent">
        <h1 class="display-4">Employees List</h1>

    </div>

    <div class="body-content">
        <div class="row">
            <div class="col-lg-12">
            <table class="table table-hover">
                <thead>
                    <tr>
                    <th scope="col">#</th>
                    <th scope="col">Employee Code</th>
                    <th scope="col">Employee Name</th>
                    <th scope="col">Department</th>
                    <th scope="col">Age</th>                    
                    <th scope="col">Experience in the organization</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($employees)) : ?>
                        <?php $count = $pagination->getLimit() * $pagination->getPage();?>
                        <?php foreach($employees as $employee) :?>
                            <tr>
                            <th scope="row"><?= ++$count;?></th>                    
                            <td><?= $employee->employee_code?></td>
                            <td><?= $employee->employee_name?></td>
                            <td><?= $employee->department->department_name?></td>
                            <td><?= $employee->employee_age?>  Year</td>
                            <td><?= Yii::$app->generalFunctions->dateDifference($employee->joining_date);?> Days</td>
                            </tr>
                        <?php endforeach;?>   
                    
                    <?php endif;?>
                    <tr>                    
                </tbody>
            </table>
                
            </div>
            <div class="col-lg-12">
                <?php
                    echo \yii\widgets\LinkPager::widget([
                        'pagination' => $pagination,
                    ]);
                ?>
            </div>    
            
        </div>

    </div>
</div>
