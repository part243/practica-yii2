
<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<h1>Agregar Cliente a Peliculas</h1>

<h3>



    <?php
        if($mensaje != null){
            echo '
            <div class="alert alert-success alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <strong>'.$mensaje.'</strong> 
            </div>
            ';
        }
    ?>
</h3>

<?php 
    $form = ActiveForm::begin([
        "method" => "post",
        "enableClientValidation" => true
    ]);
?>

<div class="form-group">
  <?= $form->field($model,"id_c")->textInput(['class'=>'form-control',"aria-describedby"=>"helpId",'required'])->hint('Identificación')->label('ID del cliente'); ?>
 
</div>

<div class="form-group">
  <?= $form->field($model,"nombre_c")->textInput(['class'=>'form-control',"aria-describedby"=>"helpId",'required'])->hint('Ingrese el nombre del cliente')->label('Nombre del cliente'); ?>
  
</div>

<div class="form-group">
  <?= $form->field($model,"apellido_c")->textInput(['class'=>'form-control',"aria-describedby"=>"helpId",'required'])->hint('Ingrese el apellido del cliente')->label('Apellido del cliente'); ?>

</div>




<?= Html::submitButton("Agregar",["class"=>"btn btn-primary"]) ?>

<?php
    $form->end();
?>

<script>
  $(".alert").alert();
</script>
