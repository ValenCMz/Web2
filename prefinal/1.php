<?php

// Implemente el siguiente requerimiento siguiendo el patrón MVC.
//  No es necesario realizar las vistas ni helpers, solo controlador(es),
//  modelo(s) y las invocaciones a la vista.

// Listar los pokemones de una determinada especie y mayores a un nivel en una pokedex específica.

// - Informar los errores que pueden aparecer.

//  - No se espera que haya un usuario logeado.

//BASE DE DATOS
// POKEDEX(id: int, id_user: int, version: string)

// ESPECIEPOKEMON(id: int, nombre: string, tipo: string, debilidad: string)

// POKEMON(id: int, nivel: int, resistencia: int, apodo: string, id_especie: int, id_pokedex: int)


//CONTROLLER
class pokemonController{

    private $pokemonModel;
    private $especiePokemonModel;
    private $pokedexModel;
    private $view;

     function __construct(){
        $this->pokemonModel = new pokemonModel();
        $this->especiePokemonModel = new especiePokemonModel();
        $this->pokedexModel = new pokedexModel();
        $this->view = new View();
    }
  
    function listarPokemones(){
        //supongo que los datos llegan por $_POST
        $data = $_POST;
        if(empty($data['id_especie'])){
           return $this->view->error('No se ha ingresado una especie');
        }
        
        if(empty($data['nivel'])){
            return $this->view->error('No se ha ingresado un nivel');
        }

        if(empty($data['id_pokedex'])){
            return $this->view->error('No se ha ingresado una pokedex');
        }
        //Voy a buscar la especie por el id
        $especie = $this->especiePokemonModel->getEspecieById($data['id_especie']);

        if(empty($especie)){
            return $this->view->error('No se ha encontrado la especie');
        }
        //Voy a buscar la pokedex por el id
        $pokedex = $this->pokedexModel->getPokedexById($data['id_pokedex']);
        if(empty($pokedex)){
            return $this->view->error('No se ha encontrado la pokedex');
        }
        //Voy a buscar los pokemones por la especie y el nivel
        $pokemones = $this->pokemonModel->getPokemon($especie, $pokedex, $data['nivel']);

        if(empty($pokemones)){
            return $this->view->error('No se encontraron pokemones');
        }
        //Voy a renderizar la vista
        $this->view->listarPokemones($pokemon);
        $this->view->exito('Se encontraron pokemones');
}

//POKEMONMODEL
class pokemonModel{
    private $db;

    function __construct(){
        $this->db = new PDO('mysql:host=localhost;'.'dbname=pokemon;charset=utf8', 'root', '');
    }

    function getPokemon($especie, $pokedex, $nivel){
        //si el nivel del pokemon es mayor al que ingreso el user entonces lo traigo
        $query = $this->db->prepare("SELECT * FROM pokemon WHERE id_especie = ? AND id_pokedex = ? AND nivel > ?");
        $query->execute(array($especie, $pokedex, $data['nivel']));
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

}
//ESPECIEPOKEMONMODEL
class especiePokemonModel{
    private $db;

    function __construct(){
        $this->db = new PDO('mysql:host=localhost;'.'dbname=pokemon;charset=utf8', 'root', '');
    }

    function getEspecieById($id){
        $query = $this->db->prepare("SELECT * FROM ESPECIEPOKEMON WHERE id = ?");
        $query->execute(array($id));
        return $query->fetch(PDO::FETCH_OBJ);
    }
}

//POKEDEXMODEL
class pokedexModel{
    private $db;

    function __construct(){
        $this->db = new PDO('mysql:host=localhost;'.'dbname=pokemon;charset=utf8', 'root', '');
    }

    function getPokedexById($id){
        $query = $this->db->prepare("SELECT * FROM POKEDEX WHERE id = ?");
        $query->execute(array($id));
        return $query->fetch(PDO::FETCH_OBJ);
    }
}