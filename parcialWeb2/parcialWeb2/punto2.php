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


        public function agregarVuelo($origen,$destino,$fecha,$estado,$capadidad,$internacional,$aerolinea){
            if ($this->authHelper->isLogged()){
            $vuelo = $this->model->traerVuelo($origen,$destino,$fecha);
                if ($vuelo){
                    if ($this->pasajeModel->getCantPasajes($vuelo->id) > $vuelo->capadidad * 0.8){
                        $this->vueloModel->agregarVuelo($origen,$destino,$fecha,$estado,$capadidad,$internacional,$aerolinea);
                        $this->view->Mostrarmensaje("Vuelo Agregado");
                    }
                    else{
                        $this->view->Mostrarmensaje("Vuelo ya existente");
                    }

                }
                else{
                    $this->vueloModel->agregarVuelo($origen,$destino,$fecha,$estado,$capadidad,$internacional,$aerolinea);
                    $this->view->Mostrarmensaje("Vuelo Agregado");}
                }
            else{
                $this->view->Mostrarmensaje("No estas logueado");
            }
        }


}

class PasajeModel{
    private $db;

    public function __construct(){
        $this->db = new PDO('mysql:host=localhost;'.'dbname=agenciaDeVuelos;charset=utf8', 'root', '');
    }

    public function getCantPasajes($vuelo){
        $sentencia = $this->db->prepare("SELECT * FROM PASAJE WHERE id_vuelo=?");
        $sentencia->execute(array($vuelo));
        $pasajes= $sentencia->fetchAll(PDO::FETCH_OBJ);
        return count($pasajes);
    }
}
class VueloModel{
    private $db;

    public function __construct(){
        $this->db = new PDO('mysql:host=localhost;'.'dbname=agenciaDeVuelos;charset=utf8', 'root', '');
    }

    public function agregarVuelo($origen,$destino,$fecha,$estado,$capadidad,$internacional,$aerolinea){
        $sentencia = $this->db->prepare("INSERT INTO VUELO (origen,destino,fecha,estado,capadidad,internacional,id_aerolinea) VALUES (?,?,?,?,?,?,?)");
        $sentencia->execute(array($origen,$destino,$fecha,$estado,$capadidad,$internacional,$aerolinea));
    }
    public function traerVuelo($origen,$destino,$fecha){
        $sentencia = $this->db->prepare("SELECT * FROM VUELO WHERE origen=? AND destino=? AND fecha=?");
        $sentencia->execute(array($origen,$destino,$fecha));
        return $sentencia->fetch(PDO::FETCH_OBJ);
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