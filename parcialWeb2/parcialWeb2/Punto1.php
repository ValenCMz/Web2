<?php

class VueloController{
        private $vueloModel;
        private $view;
        private $authHelper;
        private $pasajeModel;

        public function __construct(){
            $this->vuelonoModel = new VueloModel();
            $this->view = new View();
            $this->helper = new authHelper();
            $this->pasajeModel = new PasajeModel();
        }

        public function borrarVuelosInternacionales(){
            if ($this->authHelper->isLogged()){
                $this->vueloModel->borrarVuelosInternacionales();
                $this->view->Mostrarmensaje("vuelos Borrados");}
            else{
                $this->view->Mostrarmensaje("No estas logueado");
            }
        }
}
class VueloModel{
    private $db;

    public function __construct(){
        $this->db = new PDO('mysql:host=localhost;'.'dbname=agenciaDeVuelos;charset=utf8', 'root', '');
    }
    public function borrarVuelosInternacionales(){
        $sentencia = $this->db->prepare("DELETE FROM VUELO WHERE internacional= ?");
        $sentencia->execute(array(true)); //asumo que la tabla pasajes que aputa a vuelos con una FK
                                        // se elimina en cascada por la estructura de la BBDD
    }
}

class authHelper{
    public function isLogged(){
        session_start();
        if(isset($_SESSION['logged'])){
            session_abort();
            return true;
        }
        else {
            session_abort();
            return false;
        }
    }
}