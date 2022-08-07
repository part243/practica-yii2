<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<h1>Validar Formulario - con Active Form del lado del cliente y Servidor - para guardar
    en BDD
</h1>

<?php $form = ActiveForm::begin([
    "method" => "post",
    "enableClientValidation" => true
]);
?>

<div class="form-group">
    <?= $form->field($model, "nombre")->input("text",['required' => true]) ?>
</div>

<div class="form-group">
    <?= $form->field($model, "email")->input("email") ?>
</div>

<?= Html::submitButton("Enviar",['class'=>'btn btn-primary']) ?>

<?php $form->end() ?>