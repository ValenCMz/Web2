Implemente el siguiente requerimiento siguiendo el patrón MVC. No es necesario realizar las vistas, solo controlador(es), modelo(s) y las invocaciones a la vista.
aerolinea : id , nombre

vuelo : id, origen, destino, fecha, estado, capacidad, internacional, id_aerolinea

pasaje : id, fecha_venta, clase, equipaje, id_vuelo, id_usuario

Cargar un vuelo
El agente de viajes podrá cargar un nuevo vuelo. Se deberá verificar que el agente está logueado. Se deberá verificar que no exista un vuelo con mismo origen, destino y fecha a menos que la cantidad de pasajes vendidos sea mayor al 80%. Utilice el patrón MVC.

Controller/VueloController.php
 <?php

use PasajeModel as GlobalPasajeModel;

require_once "Model/VueloModel.php";
require_once "View/VueloView.php";
require_once 'Helpers/LoginControll.php';
require_once "Model/PasajeModel";

 class VueloController{
    private $model;
    private $view;
    private $login;
    private $pasajeModel;

    public function__construct(){
        $this->$model = new VueloModel();
        $this->view = new VueloView(); 
        $this->login = new LoginControll();
        $this->pasajeModel = new PasajeModel();
    }

   
    function addVuelo($origen, $destino, $fecha, $estado, $capacidad, $internacional, $id_aerolinea){
        $this->login->checkLoggedIn();
        $coincideConOtroVuelo = $this->model->CoincidenConOtroVuelo($origen,$destino, $fecha);
        if(isset($coincideConOtroVuelo)){
            if($porcentajeVendido = getCapacidadVendida($capacidad,$pasajesDelVuelo)<80){
                $this->view->mostrarMensaje("Ya existe vuelo con orígen, destino y fecha indicados. No se puede cargar el vuelo.");
            }
            $this->model->insertVuelo($origen, $destino, $fecha, $estado, $capacidad, $internacional, $id_aerolinea);  
        }else{
            $this->model->insertVuelo($origen, $destino, $fecha, $estado, $capacidad, $internacional, $id_aerolinea);
        }
        $this->view->showVuelos();

    }

    function getCapacidadVendida($capacidad,$pasajesDelVuelo){
        $porcentajeVendido = ($pasajesDelVuelo/$capacidad)*100;
        return $porcentajeVendido;
    }
}

Model/VueloModel.php;
<?php

class VueloModel{
    private $db;

    public function __construct(){
        $this->db = new PDO('mysql:host=localhost;'.'dbname=parcial_2021;charset=utf8', 'root', '');
    }


    function insertVuelo($origen, $destino, $fecha, $estado, $capacidad, $internacional, $id_aerolinea){
        $query = $this->db->prepare("INSERT INTO VUELO(origen, destino, fecha, estado, capacidad, internacional, id_aerolinea) VALUES(?, ?, ?, ?, ?, ?, ?)");
        $query->execute(
            array($origen, $destino, $fecha, $estado, $capacidad, $internacional, $id_aerolinea)
        );
    }

    function CoincidenConOtroVuelo($origen, $destino, $fecha){
        $query = $this->db->prepare("Select (*) FROM vuelo WHERE origen = ?, destino =?, fecha =?");
        $query->execute(
            array($origen, $destino, $fecha)
        );
        $vueloCoinciden = $query->fetch(PDO::FETCH_OBJ);
        return $vueloCoinciden;
    }

    
}

Model/PasajeModel.php;
<?php
class PasajeModel{

    private $db;

    public function __construct(){
        $this->db = new PDO('mysql:host=localhost;'.'dbname=parcial_2021;charset=utf8', 'root', '');
    }

    function getPasajesVuelo($vuelo){
        $query = $this->db->prepare("SELECT id_pasaje FROM pasaje WHERE id_vuelo=?");
        $query->execute($vuelo->id_vuelo); 
        $pasajesDelVuelo = $query->fetchAll(PDO::FETCH_OBJ);
        return $pasajesDelVuelo;
    }
}