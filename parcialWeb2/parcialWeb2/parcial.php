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

        public function agregarVuelo($origen,$destino,$fecha,$estado,$capadidad,$internacional,$aerolinea){
            if ($this->authHelper->isLogged()){
            $vuelo = $this->vueloModel->traerVuelo($origen,$destino,$fecha);
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

        public function vuelosInternacionalesXFecha($fecha){
            $vuelos = $this->vueloModel->InternacionalesXFecha($fecha);
            foreach ($vuelos as $vuelo){
                if ($this->pasajeModel->getCantPasajes($vuelo->id) == $vuelo->capacidad){
                    $vuelo->estadoCapacidad = "lleno";
                }
                else{
                    $vuelo->estadoCapacidad = "Hay asientos disponibles" ;
                }
            }
            $this->view->MostrarVuelos($vuelos);
            //asumo que la view mostrara de manera correcta los elementos
            //haciendo uso de todas sus propiedades.
            //Por ejemplo el mostrar el vuelo junto a la aerolinea y su estado de capacidad
        }
}

class VueloModel{
    private $db;

    public function __construct(){
        $this->db = new PDO('mysql:host=localhost;'.'dbname=agenciaDeVuelos;charset=utf8', 'root', '');
    }

    public function borrarVuelosInternacionales(){
        $sentencia = $this->db->prepare("DELETE FROM VUELO WHERE internacional= ?");
        $sentencia->execute(array(true));
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
    public function InternacionalesXFecha($fecha){
        $sentencia = $this->db->prepare("SELECT * FROM VUELO INNER JOIN aerolinea ON vuelo.id_aerolinea=aerolinea.id WHERE fecha=? AND internacional=?");
        $sentencia->execute(array($fecha,true));
        return $sentencia->fetchAll(PDO::FETCH_OBJ);
    }
    public function getVuelo($id){
        $sentencia = $this->db->prepare("SELECT * FROM VUELO WHERE id=?");
        $sentencia->execute(array($id));
        return $sentencia->fetch(PDO::FETCH_OBJ);
    }
    public function traerVuelos(){
        $sentencia = $this->db->prepare("SELECT * FROM VUELO");
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_OBJ);
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

class ApiView{


    public function response($data, $status) {
        header("Content-Type: application/json");
        header("HTTP/1.1 " . $status . " " . $this->_requestStatus($status));
        echo json_encode($data);
    }

    private function _requestStatus($code){
        $status = array(
            200 => "OK",
            404 => "Not found",
            500 => "Internal Server Error"
          );
          return (isset($status[$code]))? $status[$code] : $status[500];
    }
    

}

class apiVuelosController{
    private $vueloModel;
    private $apiView;

    public function __construct(){
        $this->vueloModel = new VueloModel();
        $this->apiView = new ApiView();
    }

    public function getVuelos($params = null){
        if (isset($params[':ID'])){
            $vuelo= $this->vueloModel->getVuelo($params[':ID']);
            if ($vuelo){
                $this->apiView->response($vuelo,200);
            }
            else{
                $this->apiView->response("No se encontro el vuelo",204);
            }
        }
        else{
            $vuelos = $this->model->traerVuelos();
            if ($vuelos){
                $this->apiView->response($vuelos,200);
            }
            else{
                $this->apiView->response("No hay vuelos",204);
            }
        }
    }
}