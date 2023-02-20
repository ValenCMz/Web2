<?php

// a. Defina los endpoints necesarios para dar soporte por API REST a las tres tablas de la BD.
//  No es necesario implementarlos.


// b. Siguiendo el patrón MVC implemente la API REST solo para el siguiente requerimiento.
//  No implemente los MODELOS. Puede usar la Vista de API REST brindada por la cátedra 
//  (no es necesario copiarla).

// -Listar todas las especies de pokemons y listar una sola determinada por su ID.

// POKEDEX(id: int, id_user: int, version: string)

// ESPECIEPOKEMON(id: int, nombre: string, tipo: string, debilidad: string)

// POKEMON(id: int, nivel: int, resistencia: int, apodo: string, id_especie: int, id_pokedex: int)

//endpoints

// POKEDEX
listar todas las pokedex
http://www.cazapokemons.com/api/pokedex

listar pokedex por su id
http://www.cazapokemons.com/api/pokedex/ID

delete pokedex por su id
http://www.cazapokemons.com/api/pokedex/ID

PUT pokedex 
http://www.cazapokemons.com/api/pokedex/ID

POST pokedex
http://www.cazapokemons.com/api/pokedex

// ESPECIEPOKEMON
listar todas las especies de pokemons
http://www.cazapokemons.com/api/especiespokemon

listar especies de pokemons por su id
http://www.cazapokemons.com/api/especiespokemon/ID

delete especies de pokemons por su id
http://www.cazapokemons.com/api/especiespokemon/ID

PUT especies de pokemons
http://www.cazapokemons.com/api/especiespokemon/ID

POST especies de pokemons
http://www.cazapokemons.com/api/especiespokemon

//POKEMON

listar todos los pokemons
http://www.cazapokemons.com/api/pokemons

listar pokemon por su id
http://www.cazapokemons.com/api/pokemon/ID

delete pokemon por su id
http://www.cazapokemons.com/api/pokemon/ID

PUT pokemon
http://www.cazapokemons.com/api/pokemon/ID

POST pokemon
http://www.cazapokemons.com/api/pokemon


// -Listar todas las especies de pokemons y listar una sola determinada por su ID.

// POKEDEX(id: int, id_user: int, version: string)

// ESPECIEPOKEMON(id: int, nombre: string, tipo: string, debilidad: string)

// POKEMON(id: int, nivel: int, resistencia: int, apodo: string, id_especie: int, id_pokedex: int)

//CONTROLLER
class apiEspeciePokemonController{

    private $model;
    private $view;

    function getTodasLasEspeciesPokemon(){
        $especies = $this->model->getEspeciesPokemon();
        return $this->view->response($especies, 200);
    }

    function getEspeciesPokemonPorId($params = []){
        $especieId = $params[':ID'];
        $especies = $this->model->getEspeciesPokemonPorId($especieId);
        if($especies){
            return$this->view->response($especies, 200);
        }else{
            return $this->view->response("La especie pokemon con el id=$especieId no esta disponibles", 404);
        }
    }
}

//ROUTEAPI 
require_once 'libs/Router.php';
require_once 'Controller/apiEspeciePokemonController.php';


// crea el router
$router = new Router();

// define la tabla de ruteo
$router->addRoute('especiespokemon', 'GET', 'apiEspeciePokemonController', 'getTodasLasEspeciesPokemon');
$router->addRoute('especiespokemon/:ID', 'GET', 'apiEspeciePokemonController', 'getEspeciesPokemonPorId');

// rutea
$router->route($_GET['resource'], $_SERVER['REQUEST_METHOD']);


//VIEW
class apiCommentView
{
    public function response($data, $status)
    {
        header("Content-Type: application/json");
        header("HTTP/1.1 " . $status . " " . $this->_requestStatus($status));
        echo json_encode($data);
    }

    private function _requestStatus($code)
    {
        $status = array(
            200 => "OK",
            404 => "Not found",
            500 => "Internal Server Error",
            400 =>"Bad Request"
        );
        return (isset($status[$code])) ? $status[$code] : $status[500];
    }
}