<?php
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;
?>

<a name="view" id="view" class="btn btn-primary" href="<?= Url::toRoute("site/viewcliente") ?>" role="button">Ir a lista de cliente</a>

<h2>Editar Cliente con id:</h2><h1> <?= Html::encode($_GET["id_c"]) ?></h1>
<h3><?= $msg ?></h3>
<?php 
    $form = ActiveForm::begin([
        "method" => "post",
        "id" => "frmupdate",
        "enableClientValidation" => false,
        "enableAjaxValidation" => true,
    ]);
?>

<?= $form->field($model, "id_c")->input('hidden')->label(false) ?>
<!--
<div class="form-group">
   $form->field($model,"id_c",['inputOptions' => ['required'=>true]])->textInput(['class'=>'form-control',"aria-describedby"=>"helpId",'autofocus' => true])->hint('Identificación')->label('ID del cliente'); 
</div>
--> 
<div class="form-group">
  <?= $form->field($model,"nombre_c")->textInput(['class'=>'form-control',"aria-describedby"=>"helpId",'required'])->hint('Ingrese el nombre del cliente')->label('Nombre del cliente'); ?>
</div>

<div class="form-group">
  <?= $form->field($model,"apellido_c")->textInput(['class'=>'form-control',"aria-describedby"=>"helpId",'required'])->hint('Ingrese el apellido del cliente')->label('Apellido del cliente'); ?>
</div>


<?= Html::submitButton("Actualizar",["class"=>"btn btn-primary"]) ?>
<?php
    $form->end();
?>



<script>
  $(".alert").alert();
</script>