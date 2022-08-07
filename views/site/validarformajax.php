<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<h1>Validar Formulario - AJAX con nuevo modelo action y vista y mpetodo de comprobacion en bdd
</h1>

<?php $form = ActiveForm::begin([
    "method" => "post",
    "id" => "formularioAJAX",
    "enableClientValidation" => false,
    "enableAjaxValidation" => true,
    
]);
?>

<div class="form-group">
    <?= $form->field($model, "nombre")->input("text") ?>
</div>

<div class="form-group">
    <?= $form->field($model, "email")->input("email") ?>
</div>


<?= Html::submitButton("Enviar",['class'=>'btn btn-lg btn-primary']) ?>

<?php
    if($model->validate()){
        echo 'si';
    }else{
        print_r( $model->validate());
    }?>
<?php $form->end() ?>

<?php if($model->validate()) { 
echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
  <strong>'?> $mensaje <?php '</strong> 
</div>

<script>
  $(".alert").alert();
</script> ';
 } ?>