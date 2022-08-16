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

    public function InternacionalesXFecha($fecha){
        $sentencia = $this->db->prepare("SELECT * FROM VUELO INNER JOIN aerolinea ON vuelo.id_aerolinea=aerolinea.id WHERE fecha=? AND internacional=?");
        $sentencia->execute(array($fecha,true));
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