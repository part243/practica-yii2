<?php

namespace app\models;
use Yii;
use yii\base\Model;

class ValidarFormAjax extends model{
    public $nombre;
    public $email;

    public function rules(){

        return [
            ['nombre', 'required','message' =>'Campo requerido'],
            ['nombre','match','pattern'=>"/^.{3,50}$/",'message'=>'Mínimo 3 y max 4 caracteres'],
            ['nombre','match','pattern'=>"/^[0-9a-z]+$/i",'message'=> 'Solo se aceptan letras y numeros'],
            
            ['email', 'required','message'=>'Campo requerido'],
            ['email','match','pattern'=>"/^.{5,80}$/",'message'=>'Mínimo 5 y max 80 caracteres'],
            ['email','email','message'=>'Formato no válido'],
            ['email', 'email_exist'] //llamamos el método de comprobación
        ];
    }

    //Etiqueta LAbel para cada input
    public function attributeLabels(){
        return [
            'nombre' => 'Nombre:',
            'email' => 'Email:'
        ];
    }

    // método para validar si existe el email
    public function email_exist($attribute,$params){
        $emails = ["manuel@gmail.com","alejandro@gmail.com"];
        foreach ($emails as $key => $value) {
            if ($this->email == $value){
                $this->addError($attribute, 'Email ya está en uso');
                return true;
            }
        }
        return false;
      
        
    }
}