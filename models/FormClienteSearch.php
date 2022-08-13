<?php
namespace app\models;
use yii;
use yii\base\model;

class FormClienteSearch extends Model{
    public $q;
    public function rules(){
        return [
            ["q","match","pattern"=>"/^[0-9a-záéíóúñ\s]+$/i","message"=>"solo"]
        ];
    }

 /*   public function attributeLabels(){
        return [
            ["q","Buscar:"]
        ];
    }*/
}