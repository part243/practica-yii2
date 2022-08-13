<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\ValidarFormulario;
use app\models\ValidarFormAjax;
use yii\widgets\ActiveForm; //importante para trabajr con ajax
use yii\web\Response; // importante para trabajar con ajax

// incluir modelos apra interactuar con la bdd
use app\models\CrearCliente;
use app\models\Clientes;
use yii\data\Pagination;
use yii\data\pageSize;
use yii\data\page;
use app\models\FormClienteSearch;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\UpdateCliente;

class SiteController extends Controller
{
    /**
     * Actualizar un registro
     */
    public function actionUpdatecliente(){
        $model = new UpdateCliente;
        $msg= null;

        // alidaión ajax
        if($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON; // respuesta pasar a formato JSON
            return ActiveForm::validate($model); // enviamos a validar el formulario
            \Yii::$app->end();
        }
        // capturamos datos del formluario y validamos para guardar
        if($model->load(Yii::$app->request->post())){
            if($model->validate()){
                $table = Clientes::findOne($model->id_c);
                if($table){
                    $table->nombre_c = $model->nombre_c;
                    $table->apellido_c = $model->apellido_c;
                    if($table->save()){
                        $msg= 'Cliente actualizado';
                    }else{
                        $msg = 'Cliente no actualizado';
                    }
                }else{
                    $msg = "El cliente no ha sido encontrado";
                }
            }else{
                $model->getErrors();
            }
        }
        //obtenemos datos de la bdd y mostramos en el formulario
        if(Yii::$app->request->get("id_c")){
            $id_c = Html::encode($_GET['id_c']);
            if((int) $id_c){
                $table = Clientes::findOne($id_c);
                if($table){
                    $model->id_c = $table->id_c;
                    $model->apellido_c = $table->apellido_c;
                    $model->nombre_c = $table->nombre_c;
                }else{
                    return $this->redirect(["site/viewcliente"]);
                }
            }else{
                return $this->redirect(['site/viewcliente']);
            }
        }else{
            return $this->redirect(['site/viewcliente']);
        }
        if($msg != null){
            $msg = '<div class="alert alert-success alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <strong>'.$msg.'</strong> 
          </div>';
        }
        return $this->render('updatecliente',["model"=>$model,"msg"=>$msg]);
    }

    /** 
     * 
     * Eliminar Cliente
     */
    public function actionDeletecliente(){
        if(Yii::$app->request->post()){
            $idcliente = Html::encode($_POST["id_c"]);
            if((int) $idcliente){
                if(Clientes::deleteAll("id_c=:id_C",[":id_C"=> $idcliente])){
                    echo "Cliente eliminado exitosamente, redireccionando...";
                    echo "<meta http-equiv='refresh' content='3;".Url::toRoute("site/viewcliente");
                }else{
                    echo "Ha ocurrido un error al eliminar el cliente, redireccionando...";
                    echo "<meta http-equiv='refresh' content='3;".Url::toRoute("site/viewcliente");
                }
            }else{
                echo "Ha ocurrido un error al eliminar el cliente, redireccionando...";
                echo "<meta http-equiv='refresh' content='3;".Url::toRoute("site/viewcliente");
            }
        }
        
        return $this->redirect(["site/viewcliente"]);
    }
    /**
     * 
     * Muestra todos los clientes
     */
    public function actionViewcliente(){
        $mensaje = null;
        $form = new FormClienteSearch;
        $search = null;      
        
        if($form->load(Yii::$app->request->get())){
            if($form->validate()){
                $search = Html::encode($form->q); //validar inyeccion sql
                $table = Clientes::find()
                                    ->where(['like', "id_c",$search])
                                    ->orWhere(['like', "nombre_c",$search])
                                    ->orWhere(['like',"apellido_c",$search]);
                
               // $query = "select * from clientes where id_c like '%$search%' or ";
               // $query.= "apellido_c LIKE '%$search%' or ";
               // $query.= "nombre_c LIKE '%$search%';";
               $count = clone $table;
               $pages = new Pagination(["pageSize"=>3,"totalCount" =>$count->count()]);
               //$model = $table->findBySql($query)->all();
               $model = $table->offset($pages->offset)->limit($pages->limit)->all();
            }else{
                $form->getErrors();
            }
        }else{
            $table=Clientes::find();
            $count = clone $table;
            $pages = new Pagination(["pageSize"=>3,"totalCount" =>$count->count()]);
            $model = $table->offset($pages->offset)->limit($pages->limit)->all();
        }
       
      
        return $this->render('viewcliente',['mensaje' => $mensaje,'model'=>$model,"form"=>$form,"search"=>$search,"pages"=>$pages]);
    }

    /**
     * Muestra todos los clientes
     */
    public function actionViewcliente1(){
        $mensaje = null;
        //$table = new Clientes;
        $sql = "select * from clientes";
       // $query = Clientes::findBySql($sql);
       // $countTotals = clone $query;
       // $pagination = new Pagination(['PageSize'=>3,'totalCount' => $countTotals->count()]);
       // $model = $query->offset($pagination->offset)->limit($pagination->limit)->all();
       $query = Clientes::find()->orderBy(['apellido_c' => SORT_ASC]);

       // get the total number of articles (but do not fetch the article data yet)
       
       
       // create a pagination object with the total count
       $pagination = new Pagination(['totalCount' => $query->count(),
                'pageSize' => 5,
                'pageParam' => 'p',
                'pageSizeParam' => 'pageSize'
                ]);
       
       // limit the query using the pagination and retrieve the articles
       $model = $query->offset($pagination->offset)
           ->limit($pagination->limit)
           ->all();
        return $this->render('viewcliente',['mensaje' => $mensaje,'model'=>$model,'pages'=>$pagination]);
    }

    //accion para crear en peliculas
    public function actionCrearcliente(){
        $mensaje = null;
        $model = new CrearCliente;

        if($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON; // respuesta pasar a formato JSON
            return ActiveForm::validate($model); // enviamos a validar el formulario
            \Yii::$app->end();
        }

        if($model->load(Yii::$app->request->post())){
            if( $model->validate()){
                    // conectar al modelo
                    $table = new Clientes;
                    $table->id_c = $model->id_c;
                    $table->nombre_c = $model->nombre_c;
                    $table->apellido_c = $model->apellido_c;
                    if($table->insert()){
                        $mensaje = "Registro agregado correctamente";
                        $model->id_c = null;
                        $model->nombre_c = null;
                        $model->apellido_c = null;
                    }else{
                        $mensaje = "Error al insertar registro";
                        $model->getErrors();
                    }
            }else{
                $message = "formulario NO válido";
                $model->getErrors();
            }
           
        }else{
            
            $model->getErrors();
        }

        return $this->render('crearcliente', ['model'=>$model,'mensaje'=>$mensaje]);
    }


    public function actionValidarformajax(){
        $model = new ValidarFormAjax;
        $message = null;
        // primero comprobar si el método es post
        // luego preguntamos si la petición es ajax
        if($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON; // respuesta pasar a formato JSON

            return ActiveForm::validate($model); // enviamos a validar el formulario
            \Yii::$app->end();
        }

        //verificando si la validación es correcta
        if($model->load(Yii::$app->request->post())){
            if( $model->validate()){
                //consultar a una bdd
                $message = "formulario validado correctamente";
                //limpiar campos
                $model->nombre = null;
                $model->email = null;
            }else{
                $message = "formulario NO válido";
                $model->getErrors();
            }
        }
        return $this->render('validarformajax', ['model'=>$model,'mensaje'=>$message]);
    }
    /**
     * Validar formulario de lado cliente y servidor
     */
    public function actionValidarformulario(){
        $model = new ValidarFormulario;
        if ($model->load(Yii::$app->request->post())){
            if($model->validate()){
                //realizar la accion que se requiera BDD etc..
                return $this->render('respuestavalidarform',['model'=>$model]);
                
            }else{
                $model->getErrors(); //obtener las validaciones y mostrar
            }
        }
        return $this->render('validarformulario',['model'=>$model]);
    }

    /**
     * Respuesta luego de validar fommlario 2 funciones anteriores
     */
    public function actionRespuestavalidarform($model){
        return $this->render('respuestavalidarform',['model'=>$model]);
    }
    /* nueva pagina en site
    */
    public function actionSaluda(){
        $mensaje = 'Hola mundo, soy la acción';
        $numeros = [1,2,3,4,5];
        
        return $this->render('saluda',["mensaje"=>$mensaje, "numeros" =>$numeros]);
    }

    public function actionFormulario($mensaje=null){
        if(Yii::$app->user->isGuest){
            echo 'User is not logged!';
           }
           else{
               
        
            return $this->render("formulario",["mensaje"=>$mensaje]);
           }
        
    }

    public function actionRequest(){
        $mensaje=null;
        if (isset($_REQUEST["nombre"]) ){
            $mensaje = "Nombre <b>". $_REQUEST["nombre"] . "</b> enviado correctamente";
            $_REQUEST["nombre"] = ""; 
        }
        
           
        
       // $this->redirect(["site/formulario","mensaje"=>$mensaje]); //parametro GET
        
        return $this->render('formulario',["mensaje" => $mensaje]);
    }
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
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
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
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
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
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
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
