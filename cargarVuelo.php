<?php

class VueloController
{

    private $view;
    private $model;
    private $pasajeModel;


    public function __construct()
    {
        $this->view = new VueloView();
        $this->model = new VueloModel();
        $this->pasajeModel = new PasajeModel();
    }

    // VUELO(id: int, origen: string, destino: string, fecha: string, estado: string, capacidad: int, internacional: bool, id_aerolinea: int)
    // PASAJE(id: int, fecha_venta: string, clase: int, equipaje: int, id_vuelo: int, id_usuario: int) 


    function cargarVuelo($params = null)
    {
        // si no es admin no puede cargar vuelos
        if (!AuthHelper::checkIsAdmin())
            return $this->view->showError("No tienes permisos para realizar la operación.");

        // si no se pasan todos los datos, no se puede cargar el vuelo
        $vueloData = $_POST;
        if (!$this->isDataValid($vueloData))
            return $this->view->showError("Datos inválidos.");

        // si no existe un vuelo con las mismas caracteristicas se crea el vuelo
        $vuelo = $this->model->find($vueloData['origen'], $vueloData['destino'], $vueloData['fecha']);
        if ($vuelo == null)
            return $this->model->create($vueloData);

        // se calcula el porcentaje de ocupación en base a la capacidad del vuelo y la cantidad de pasajes vendidos para el mismo 
        $cantidadPasajesVendidos = $this->pasajeModel->getCantidadPasajesVendidos($vuelo['id']);
        $porcentajeOcupacion = $cantidadPasajesVendidos / $vuelo['capacidad'] * 100;

        // si el porcentaje de ocupación es mayor a 80% se crea un nuevo vuelo para que más gente pueda volar xq son más ingresos para la aerolinea papu
        // sino no se crea nada el vuelo, no da, ya hay mucho espacio
        return $porcentajeOcupacion > 80 ? $this->model->create($vueloData) : $this->view->showError("Ya existe uno vuelo con suficiente capacidad como para estar creando otro, no te parece?");
    }

    function isDataValid($vueloData)
    {
        return isset($vueloData['origen']) &&
            isset($vueloData['destino']) &&
            isset($vueloData['fecha']) &&
            isset($vueloData['capacidad']) &&
            isset($vueloData['internacional']) &&
            isset($vueloData['id_aerolinea']);
    }
}


class VueloModel
{
    private $db;

    function __construct()
    {
        $this->db = new PDO('mysql:host=mysql-tpeweb2-c;port=3306;dbname=db-tpe-web2', 'root', '');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // de existir al menos un vuelo con las caracteristicas recibidas por parámetro, devuelve el vuelo
    function find($origen, $destino, $fecha)
    {
        $query = $this->db->prepare("SELECT * FROM vuelo WHERE origen = ? AND destino = ? AND fecha = ?");
        $query->execute([$origen, $destino, $fecha]);
        return $query->fetch();
    }

    function create($vueloData)
    {
        $query = $this->db->prepare("INSERT INTO vuelo (id, origen, destino, fecha, estado, capacidad, internacional, id_aerolinea) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?)");
        $query->execute([$vueloData['origen'], $vueloData['destino'], $vueloData['fecha'], 'disponible', $vueloData['capacidad'], $vueloData['internacional'], $vueloData['id_aerolinea']]);
    }
}

class PasajeModel
{
    private $db;

    function __construct()
    {
        $this->db = new PDO('mysql:host=mysql-tpeweb2-c;port=3306;dbname=db-tpe-web2', 'root', '');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // devuelve la cantidad de pasajes vendidos para un id de vuelo dado
    function getCantidadPasajesVendidos($idVuelo)
    {
        $query = $this->db->prepare("SELECT COUNT(*) FROM pasaje WHERE id_vuelo = ?");
        $query->execute([$idVuelo]);
        return $query->fetch()[0];
    }
}
