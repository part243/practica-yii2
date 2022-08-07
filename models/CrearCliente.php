<?php 
namespace app\models;
use Yii;
use yii\base\model;

class CrearCliente extends model{
    public $id_c;
    public $nombre_c;
    public $apellido_c;

    public function rules(){
        return [
            ['id_c','integer','message' => 'Solo se aceptan números'],
            ['id_c','required','message' => 'Campo requerido'],
            ['nombre_c', 'required', 'message' => 'Campo requerido'],
            ['nombre_c', 'match', 'pattern' => '/^[a-záéíóúñ\s]+$/i', 'message' => 'Sólo se aceptan letras'],
            ['nombre_c', 'match', 'pattern' => '/^.{3,50}$/', 'message' => 'Mínimo 3 máximo 50 caracteres'],
            ['nombre_c','required','message' => 'El nombre es requerido'],
            ['apellido_c', 'required', 'message' => 'Campo requerido'],
            ['apellido_c', 'match', 'pattern' => '/^[a-záéíóúñ\s]+$/i', 'message' => 'Sólo se aceptan letras'],
            ['apellido_c', 'match', 'pattern' => '/^.{3,80}$/', 'message' => 'Mínimo 3 máximo 80 caracteres'],
        ];
    }

}