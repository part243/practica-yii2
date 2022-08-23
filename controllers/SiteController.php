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

// registro de usuario
use app\models\Register;
use app\models\Users;

class SiteController extends Controller
{
    /**
     * Genera password aleatorios del nuevo registro
     */
    private function randKey($str='', $long=0)
    {
        $key = null;
        $str = str_split($str);
        $start = 0;
        $limit = count($str)-1;
        for($x=0; $x<$long; $x++)
        {
            $key .= $str[rand($start, $limit)];
        }
        return $key;
    }
    /** 
     * Confirmación de usuario luego de recibir el mensajey dar clic lo activa
     */
    public function actionConfirm()
 {
    $table = new Users;
    if (Yii::$app->request->get())
    {
   
        //Obtenemos el valor de los parámetros get
        $id = Html::encode($_GET["id"]);
        $authkey = $_GET["authkey"];
    
        if ((int) $id)
        {
            //Realizamos la consulta para obtener el registro
            $model = $table
            ->find()
            ->where("id=:id", [":id" => $id])
            ->andWhere("authkey=:authkey", [":authkey" => $authkey]);
 
            //Si el registro existe
            if ($model->count() == 1)
            {
                $activar = Users::findOne($id);
                $activar->activate = 1;
                if ($activar->update())
                {
                    echo "Enhorabuena registro llevado a cabo correctamente, redireccionando ...";
                    echo "<meta http-equiv='refresh' content='8; ".Url::toRoute("site/login")."'>";
                }
                else
                {
                    echo "Ha ocurrido un error al realizar el registro, redireccionando ...";
                    echo "<meta http-equiv='refresh' content='8; ".Url::toRoute("site/login")."'>";
                }
             }
            else //Si no existe redireccionamos a login
            {
                return $this->redirect(["site/login"]);
            }
        }
        else //Si id no es un número entero redireccionamos a login
        {
            return $this->redirect(["site/login"]);
        }
    }
 }
    /** 
     * Registra nuevos usuarios
     */
    public function actionRegister()
    {
     //Creamos la instancia con el model de validación
     $model = new Register;
      
     //Mostrará un mensaje en la vista cuando el usuario se haya registrado
     $msg = null;
      
     //Validación mediante ajax
     if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax)
           {
               Yii::$app->response->format = Response::FORMAT_JSON;
               return ActiveForm::validate($model);
           }
      
     //Validación cuando el formulario es enviado vía post
     //Esto sucede cuando la validación ajax se ha llevado a cabo correctamente
     //También previene por si el usuario tiene desactivado javascript y la
     //validación mediante ajax no puede ser llevada a cabo
     if ($model->load(Yii::$app->request->post()))
     {
      if($model->validate())
      {
        //Preparamos la consulta para guardar el usuario
       $table = new Users;
       $table->username = $model->username;
       $table->email = $model->email;
       //Encriptamos el password
       $table->password = crypt($model->password, Yii::$app->params["salt"]);
       //Creamos una cookie para autenticar al usuario cuando decida recordar la sesión, esta misma
       //clave será utilizada para activar el usuario
       $table->authkey = $this->randKey("abcdef0123456789", 200);
       //Creamos un token de acceso único para el usuario
       $table->accesstoken = $this->randKey("abcdef0123456789", 200);
        
       //Si el registro es guardado correctamente
       if ($table->insert())
       {
        //Nueva consulta para obtener el id del usuario
        //Para confirmar al usuario se requiere su id y su authkey
        $user = $table->find()->where(["email" => $model->email])->one();
        $id = urlencode($user->id);
        $authkey = urlencode($user->authkey);
         
        $subject = "Confirmar registro";
        $body = "<h1>Haga click en el siguiente enlace para finalizar tu registro</h1>";
        $body .= "<a href='http://localhost:8080/index.php?r=site/confirm&id=".$id."&authkey=".$authkey."'>Confirmar</a>";
         
        //Enviamos el correo
        Yii::$app->mailer->compose()
        ->setTo($user->email)
        ->setFrom([Yii::$app->params["adminEmail"] => Yii::$app->params["title"]])
        ->setSubject($subject)
        ->setHtmlBody($body)
        ->send();
        
        $model->username = null;
        $model->email = null;
        $model->password = null;
        $model->password_repeat = null;
        
        $msg = "Enhorabuena, ahora sólo falta que confirmes tu registro en tu cuenta de correo";
       }
       else
       {
        $msg = "Ha ocurrido un error al llevar a cabo tu registro";
       }
        
      }
      else
      {
       $model->getErrors();
      }
     }
     return $this->render("register", ["model" => $model, "msg" => $msg]);
    }
    /**
     * Actualizar un registro
     */
    public function actionUpdatecliente(){
        $model = new UpdateCliente;
        $msg= null;

        // validaión ajax
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
