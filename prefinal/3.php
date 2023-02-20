<?php

// Un usuario debe poder eliminar un pokemon de una pokedex propia.

// Se debe verificar que la pokedex cuente con al menos 1 pokemon una vez eliminado.
//  En caso contrario, no permitir la eliminación

// Chequear que el usuario esté logueado

// Informar los errores que pueden aparecer. 

//BASE DE DATOS
// POKEDEX(id: int, id_user: int, version: string)

// ESPECIEPOKEMON(id: int, nombre: string, tipo: string, debilidad: string)

// POKEMON(id: int, nivel: int, resistencia: int, apodo: string, id_especie: int, id_pokedex: int)


//CONTROLLER
class pokemonController{
    
    private $pokemonModel;
    private $pokedexModel;
    private $view;
    private $authHelper;

    function __construct(){
        $this->pokemonModel = new pokemonModel();
        $this->pokedexModel = new pokedexModel();
        $this->view = new View();
        $this->authHelper = new AuthHelper();
    }

    function deletePokemon(){
        if(!$this->authHelper->checkLogin()){
            return $this->view->error("No estás logueado");
        }
        //supongo que los datos llegan por post
        $data = $_POST;
        if(empty($data)){
            return $this->view->error("No se recibió ningún dato");
        }
        $id_user = $this->authHelper->getIdUser();
        if(empty($id_user)){
            return $this->view->error("No se pudo obtener el id del usuario");
        }
        //traigo la pokedex del usuario
        $pokedex = $this->pokedexModel->getPokedexByUser($id_user);
        if(empty($pokedex)){
            return $this->view->error("No tienes ninguna pokedex");
        }
        //checkear que en la pokedex exista al menos un pokemon para poder eliminar
        $pokemon = $this->pokemonModel->getPokemonByPokedex($pokedex->id);
        if(count($pokemon) < 2){
            return $this->view->error("Debes tener al menos un pokemon para eliminar");
        }
        //traigo el numero de filas afectadas por la eliminacion
        $pokemonEliminado = $this->pokemonModel->deletePokemon($data, $pokedex->id);
        //si lo que me retorna esta vacio es porque no se elimino ningun pokemon
        if(empty($pokemonEliminado)){
            return $this->view->error("No se pudo eliminar el pokemon");
        }
        //aviso que se elimino con exito
        $this->view->exito("Pokemon eliminado");
    }
}

//POKEDEXMODEL
class pokedexModel{
    private $db;

    function __construct(){
        $this->db = new PDO('mysql:host=localhost;'.'dbname=pokemon;charset=utf8', 'root', '');
    }

    function getPokedexByUser($id_user){
        $query = $this->db->prepare("SELECT * FROM pokedex WHERE id_user = ?");
        $query->execute(array($id_user));
        return $query->fetch(PDO::FETCH_OBJ);
    }
}

//POKEMONMODEL
class pokemonModel{
    private $db;

    function __construct(){
        $this->db = new PDO('mysql:host=localhost;'.'dbname=pokemon;charset=utf8', 'root', '');
    
    }

    function getPokemonByPokedex($id_pokedex){
        $query = $this->db->prepare("SELECT * FROM pokemon WHERE id_pokedex = ?");
        $query->execute(array($id_pokedex));
        return $query->fetch(PDO::FETCH_OBJ);
    }

    function deletePokemon($data, $id_pokedex){
        //asumo que me deja eliminar el pokemon porque esta seteado con cascade la fk en la base de datos
        $query = $this->db->prepare("DELETE FROM pokemon WHERE id = ? AND id_pokedex = ?");
        $query->execute(array($data['id'], $id_pokedex));
        //Obtiene el número de filas afectadas en la última operación MySQL
        return mysqli_affected_rows();
    }
}