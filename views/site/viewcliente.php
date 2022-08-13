<?php
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;
use yii\bootstrap4\LinkPager;
use yii\bootstrap4\Modal;
?>
<!-- Crear usuarios -->
<a name="" id="" class="btn alert-info btn-primary" href="<?= Url::toRoute("site/crearcliente") ?>" role="button">Crear Cliente</a>
<!-- Buscar resultados -->
<?php $frm = ActiveForm::begin([
    "method" => "get",
    "action" => Url::toRoute("site/viewcliente"),
    "enableClientValidation" => true
]);

?>


<div class="container mt-5">
    <div class="row">
    <div class="col-8"><?= $frm->field($form, "q")->input("search")->label(false)?></div>
    <div class="col-4"><?= Html::submitButton("Buscar",["class"=>"btn btn-primary"])?></div>
    </div>
</div>
<?php $frm->end() ?>

<h3><?= $search ?></h3>
<!-- .----------------------------------------------------- -->
<h3>Lista de clientes</h3>

<table class="table table-striped table-inverse table-responsive table-hover">
    <thead class="thead-inverse">
        <tr>
            <th>Id</th>
            <th>Apellidos</th>
            <th>Nombres</th>
            <th colspan="2">Acciones</th>
        </tr>
        </thead>
        <tbody>
            <?php foreach($model as $key => $value) { ?>
            <tr>
                <td scope="row"><b><?=$value->id_c ?></b></td>
                <td><?= $value->apellido_c ?></td>
                <td><?= $value->nombre_c ?></td>
                <td> <a name="" id="" class="btn btn-primary" href="<?= Url::toRoute(['site/updatecliente',"id_c"=> $value->id_c]) ?>" role="button">Editar</a> </td>
                <td> 
              <!-- Formluario del modal -->
                <?php
                    Modal::begin([
                        'title' => 'Eliminar usuario',
                        'toggleButton' => ['label' => 'Eliminar', 'class'=>'btn btn-warning'],
                        'closeButton' => ['label'=> 'X','class'=>'btn btn-success'],
                        'footer' => Html::beginForm(Url::toRoute("site/deletecliente"),"POST").
                                        '<input type="hidden" name="id_c" value="'.$value->id_c.'"></input>
                                        <button type="submit" class="btn btn-danger ">Eliminar</button>'.
                                    Html::endForm(),
                    ]);
                ?>
                    <h3><?= $value->id_c ?></h3>
                    <h3><?= $value->apellido_c.' '.$value->nombre_c?></h3>
                <?php
                    Modal::end();
                ?>
                <!-- ------------------ -->
            </td>

            </tr>
            <?php } ?>
        </tbody>
</table>
<?= LinkPager::widget([
    'pagination' => $pages,
    'activePageCssClass' => 'active' ,
    'prevPageLabel' => 'primera',
    'lastPageLabel' => 'última',
    'hideOnSinglePage' => true,
]);
?>

