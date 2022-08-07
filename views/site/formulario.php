<?php
use yii\helpers\Url;
use yii\helpers\Html;
?>

<h1>Formulario</h1>

<?= Html::beginForm(
    Url::toRoute("site/request"), //action a donde quiere ir
    "get", //metodo
    ['class'=>'form-inline'] //opciones
);?>

<div class="form-group">
    <?= Html::label('Introduce tu nombre','nombre') ?>
    <?= Html::textInput('nombre',null,['class'=>'form-control mr-4 ml-4', 'required'=>true]) ?>
</div>

<?= Html::submitInput('Enviar nombre', ['class'=>'btn btn-primary']) ?>

<?= Html::endForm() ?>
<?php 
    if($mensaje){ echo '<div class="alert alert-success alert-dismissible fade show mt-5" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
    <strong> '.$mensaje.' </strong> 
  </div>';  } 


?>
<script>
  $(".alert").alert();
</script>