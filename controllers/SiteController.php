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

class SiteController extends Controller
{
    //accion para crear en peliculas
    public function actionCrearcliente(){
        $mensaje = null;
        $model = new CrearCliente;
        if($model->load(Yii::$app->request->post())){
            // conectar al modelo
            $table = new Clientes;
            $table->id_c = $model->id_c;
            $table->nombre = $model->nombre;
            $table->apellido = $model->apellido;
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
