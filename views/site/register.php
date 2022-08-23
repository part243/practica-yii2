<?php
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
?>

<h3><?= $msg ?></h3>

<h1>Register</h1>
<?php $form = ActiveForm::begin([
    'method' => 'post',
 'id' => 'formulario',
 'enableClientValidation' => true,
 'enableAjaxValidation' => true,
]);
?>
<div class="form-group">
 <?= $form->field($model, "username")->input("text") ?>   
</div>

<div class="form-group">
 <?= $form->field($model, "email")->input("email") ?>   
</div>

<div class="form-group">
 <?= $form->field($model, "password")->input("password") ?>   
</div>

<div class="form-group">
 <?= $form->field($model, "password_repeat")->input("password") ?>   
</div>

<?= Html::submitButton("Register", ["class" => "btn btn-primary"]) ?>

<?php $form->end() ?>


<script>
  $(".alert").alert();
</script>
