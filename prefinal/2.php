<?php

// Agregar un pokemon a una pokedex. 

// Se debe verificar que la pokedex no cuente actualmente con un pokemon de esa especie

// Chequear que el usuario esté logueado

// Informar los errores que pueden aparecer. 

// POKEDEX(id: int, id_user: int, version: string)

// ESPECIEPOKEMON(id: int, nombre: string, tipo: string, debilidad: string)

// POKEMON(id: int, nivel: int, resistencia: int, apodo: string, id_especie: int, id_pokedex: int)

//CONTROLLER
class pokemonController{

    private $pokemonModel;
    private $pokedexModel;
    private $especieModel;
    private $view;
    private $authHelper;

    function __construct(){
        $this->pokemonModel = new pokemonModel();
        $this->pokedexModel = new pokedexModel();
        $this->especieModel = new especieModel();
        $this->view = new View();
        $this->authHelper = new AuthHelper();
    }
    
    function agregarPokemonAPokedex(){
        if(!$this->authHelper->checkLogin()){
           return $this->view->error("No estás logueado");
        }
        //supongo que los datos llegan por $_POST
        $data = $_POST;

        if(empty($data)){
            return $this->view->error("No se recibió ningún dato");
        }
        //voy a buscar el id del user
        $id_user = $this->authHelper->getIdUser();

        if(empty($id_user)){
            return $this->view->error("No se pudo obtener el id del usuario");
        }
        //voy a buscar la pokedex por el id del user
        $pokedex = $this->pokedexModel->getPokedex($id_user);
        if(empty($pokedex)){
            return $this->view->error("No tienes ninguna pokedex");
        }
        //voy a buscar la especie del pokemon
        $especie = $this->especieModel->getEspecie($data['especie']);
        if(empty($especie)){
            return $this->view->error("No se pudo obtener la especie");
        }
        //voy a buscar el pokemon por el id de la especie
        $pokemon = $this->pokemonModel->getPokemon($especie->id, $pokedex->id);
        if(!empty($pokemon)){
            return $this->view->error("Ya tienes un pokemon de esa especie");
        }
        //voy a agregar el pokemon a la pokedex
        $pokemonAgregado = $this->pokemonModel->agregarPokemonAPokedex($data['nivel'], $data['resistencia'], $data['apodo'], $data['id_especie'], $pokedex['id']);
        if(empty($pokemonAgregado)){
            return $this->view->error("No se pudo agregar el pokemon");
        }
        //aviso que se agrego al pokemon
        return $this->view->exito("Pokemon agregado");
    }
}

//POKEDEXMODEL
class pokedexModel{
    private $db;

   function __construct(){
        $this->db = new PDO('mysql:host=localhost;'.'dbname=pokemon;charset=utf8', 'root', '');
    }

  function getPokedex($id_user){
        $query = $this->db->prepare("SELECT * FROM pokedex WHERE id_user = ?");
        $query->execute(array($id_user));
        return $query->fetch(PDO::FETCH_OBJ);
    }
}

//ESPECIEMODEL
class especieModel{
    private $db;

   function __construct(){
        $this->db = new PDO('mysql:host=localhost;'.'dbname=pokemon;charset=utf8', 'root', '');
    }

    function getEspecie($id_especie){
            $query = $this->db->prepare("SELECT * FROM especie WHERE id = ?");
            $query->execute(array($id_especie));
            return $query->fetch(PDO::FETCH_OBJ);
        }
}

//POKEMONMODEL
class pokemonModel{
    private $db;

    function __construct(){
        $this->db = new PDO('mysql:host=localhost;'.'dbname=pokemon;charset=utf8', 'root', '');
    }

    function getPokemon($especie, $id_pokedex){
        $query = $this->db->prepare("SELECT * FROM pokemon WHERE id_especie = ? AND id_pokedex = ?");
        $query->execute(array($especie, $id_pokedex));
        return $query->fetch(PDO::FETCH_OBJ);
    }

    function agregarPokemonAPokedex($nivel, $resistencia, $apodo, $id_especie, $id_pokedex){
        $query = $this->db->prepare("INSERT INTO pokemon (nivel, resistencia, apodo, id_especie, id_pokedex) VALUES (?, ?, ?, ?, ?)");
        $query->execute(array($nivel, $resistencia, $apodo, $id_especie, $id_pokedex));
        return $this->db->lastInsertId();
    }

}
