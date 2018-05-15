<?php
    
namespace FederacionInformes\php;
use PDO;

/**
 * Clase encargada de realizar las consultas con la BD.
 * Realiza la conexión, la inserción de datos del fichero Excel en la base de datos
 * y las consultas necesarias para obtener los datos de los informes.
 * 
 * @author M. Elvira Rodríguez Luis
 */
class Consultas {

    /**
     * Objeto para realizar consultas a la bd
     *
     * @var PDO
     */
    private $pdo;

    /**
     * Constructor encargado de montar la base de la conexión
     */
    function __construct(){
        $this->conectar();
    }

    /**
     * Realiza la conexión con la base de datos.
     * 
     * @return void
     */
    private function conectar() : void
    {
        $host = 'localhost';
        $dbname = 'db_competiciones';
        $user = 'root';
        $pass = '';

        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
            $this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        }
        catch(PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    /**
     * Obtiene todos los géneros que existentes.
     * 
     * @return array
     */
    public function getGeneros() : array
    {
        $stmt = $this->pdo->prepare("SELECT DISTINCT sexo FROM competidores");
        $stmt->execute();

        return $stmt->fetchAll();  
    }

    /**
     * Obtiene todas las pruebas que existentes.
     * 
     * @return array
     */
    public function getPruebas() : array
    {
        $stmt = $this->pdo->prepare("SELECT DISTINCT prueba FROM competidores");
        $stmt->execute();

        return $stmt->fetchAll();  
    }

    /**
     * Obtiene todas las categorías que existentes.
     * 
     * @return array
     */
    public function getCategoria() : array
    {
        $stmt = $this->pdo->prepare("SELECT DISTINCT categoria FROM competidores");
        $stmt->execute();

        return $stmt->fetchAll();  
    }

    /**
     * Obtiene todas las temporadas que existen.
     *
     * @return array
     */
    public function getTemporadas() : array
    {
        $stmt = $this->pdo->prepare("SELECT DISTINCT fechaCompeticion FROM competidores");
        $stmt->execute();

        return $stmt->fetchAll();  
    }

    /**
     * Devuelve toda la información de los competidores.
     * 
     * @return array
     */
    public function getAllCompetidores() : array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM competidores');
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Obtiene todos los competidores correspondientes a los parámetros indicados.
     * 
     * @param string $categoria
     * @param string $sexo
     * @param string $prueba
     * @return array
     */
    public function getCompetidoresCategoria(string $categoria, string $sexo, string $prueba) : array
    {
        $stmt = $this->pdo->prepare("SELECT nombre, apellidos, anio, club, tipoPiscina FROM competidores WHERE categoria = :categoria AND sexo = :sexo AND prueba = :prueba");
        $stmt->bindParam(':categoria', $categoria, PDO::PARAM_STR, 20);
        $stmt->bindParam(':sexo', $sexo, PDO::PARAM_STR, 1);
        $stmt->bindParam(':prueba', $prueba, PDO::PARAM_STR, 60);
        $stmt->execute();

        $row = $stmt->fetchAll();
        $competidores = [];
        foreach($row as $fil) {
            $competidor = [
                'nombre' => $fil['nombre'], 
                'apellidos' => $fil['apellidos'], 
                'anio' => $fil['anio'], 
                'club' => $fil['club'], 
                'tipoPiscina' => $fil['tipoPiscina']
            ];
            array_push($competidores, $competidor);
        }
        
        return $competidores;
    }

    /**
     * Devuelve un array con la estructura necesaria para generar el informe por categorías.
     * 
     * @param string $categoria
     * @return array
     */
    public function informeCategoria(string $categoria) : array
    {
        $generos = $this->getGeneros();
        $pruebas = $this->getPruebas();
        
        $data = array(
            'categoria' => $categoria,
            'data' => []
        );

        foreach ($pruebas as $prueba) {
            $pru = array(
                'prueba' => $prueba['prueba'],
                'genero' => array()
            );    
            foreach ($generos as $genero) {
                array_push($pru['genero'], $this->getCompetidoresCategoria($categoria, $genero['sexo'], $prueba['prueba']));
            }
            array_push($data['data'], $pru);
        }

        return $data;
    }    

    /**
     * Realiza la inserción de los competidores a través de un array con sus datos que recibe como parámetro.
     * 
     * @param array $competidor
     * @return void
     */
    public function insertarCompetidor(array $competidor) : void
    {
        $date = $this->formatDate($competidor[7]);
        
        //Elimina algunos caracteres que recibe de forma errónea.
        $tiempo = str_replace ('=' , '' , str_replace ('"' , '' , $competidor[16]));
        //En caso de que no tenga tiempo ese competidor se coloca el campo a null.
        if($tiempo == "")
            $tiempo = null;
        $timeConvert = $this->formatTime($competidor[17]);   
    
        $stmt = $this->pdo->prepare(
            "INSERT INTO competidores (nombre, apellidos, anio, sexo, club, clubComunidad, competicion, fechaCompeticion, lugarCompeticion, comunidadCompeticion, tipoPiscina, prueba, agrupacion, categoria, tipoSerioe, ronda, tiempo, tiempoConvertido, posicion, exclusion, descalificado) 
            VALUES (:nombre, :apellidos, :anio, :sexo, :club, :clubComunidad, :competicion, :fechaCompeticion, :lugarCompeticion, :comunidadCompeticion, :tipoPiscina, :prueba, :agrupacion, :categoria, :tipoSerioe, :ronda, :tiempo, :tiempoConvertido, :posicion, :exclusion, :descalificado)");
        $stmt->bindParam(':nombre', $competidor[0], PDO::PARAM_STR, 20);
        $stmt->bindParam(':apellidos', $competidor[1], PDO::PARAM_STR, 50);
        $stmt->bindParam(':anio', $competidor[2], PDO::PARAM_INT);
        $stmt->bindParam(':sexo', $competidor[3], PDO::PARAM_STR, 1);
        $stmt->bindParam(':club', $competidor[4], PDO::PARAM_STR, 60);
        $stmt->bindParam(':clubComunidad', $competidor[5], PDO::PARAM_STR, 60);
        $stmt->bindParam(':competicion', $competidor[6], PDO::PARAM_STR, 60);
        $stmt->bindParam(':fechaCompeticion', $date);
        $stmt->bindParam(':lugarCompeticion', $competidor[8], PDO::PARAM_STR, 60);
        $stmt->bindParam(':comunidadCompeticion', $competidor[9], PDO::PARAM_STR, 60);
        $stmt->bindParam(':tipoPiscina', $competidor[10], PDO::PARAM_STR, 3);
        $stmt->bindParam(':prueba', $competidor[11], PDO::PARAM_STR, 60);
        $stmt->bindParam(':agrupacion', $competidor[12], PDO::PARAM_STR, 100);
        $stmt->bindParam(':categoria', $competidor[13], PDO::PARAM_STR, 20);
        $stmt->bindParam(':tipoSerioe', $competidor[14], PDO::PARAM_STR, 30);
        $stmt->bindParam(':ronda', $competidor[15], PDO::PARAM_INT);
        $stmt->bindParam(':tiempo', $tiempo, PDO::PARAM_STR, 8);
        $stmt->bindParam(':tiempoConvertido', $timeConvert);
        $stmt->bindParam(':posicion', $competidor[18], PDO::PARAM_INT);
        $stmt->bindParam(':exclusion', $competidor[19], PDO::PARAM_STR, 20);
        $stmt->bindParam(':descalificado', $competidor[20], PDO::PARAM_STR, 2);
        $stmt->execute();
    }

    /**
     * Convierte el campo fecha competición de string a tipo fecha para que pueda insertarse en la base de datos.
     * 
     * @param string $competidor Fecha competición.
     * @return string Devuelve la fecha convertida para insertarla.
     */
    private function formatDate($competidor) : string
    {  					
        $arrayF = explode("/", $competidor);
        $formatDate = $arrayF[1].'/'.$arrayF[0].'/'.$arrayF[2];
        $date = date("Y-m-d", strtotime($formatDate));
        return $date;
    }

    /**
     * Convierte el tiempo que es de tipo string a tipo date en formato time aceptado por la base de datos.
     * 
     * @param string $competidor Tiempo.
     * @return string|null Devuelve el tiempo convertido para insertarlo.
     */
    private function formatTime($competidor)
    {
        $tiempo = str_replace ('=' , '' , str_replace ('"' , '' , $competidor));
        $time = null;
        if($tiempo != "")
            $time = date('H:i:s', strtotime($tiempo));         
        return $time;
    }

    /**
     * Elimina todos los competidores de la tabla
     *
     * @return void
     */
    public function deleteAll(){
        $stmt = $this->pdo->prepare('TRUNCATE TABLE competidores');
        $stmt->execute();
    }

    /**
     * Cierra la conexión para que no sobre carge la BD
     *
     * @return void
     */
    public function closeConnection() : void
    {
        $this->pdo = null;
    }
}
    //$consulta = new Consultas();
    //header('Content-Type: application/json');
    //echo json_encode($consulta->informeCategoria('Infantil'));
    //echo json_encode($consulta->getCompetidoresCategoria('Infantil', 'F', '100 m. natación con obstáculos'));
?>