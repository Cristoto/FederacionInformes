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
     * Name of file with the configuration to connect with the database
     */
    const FILE_NAME = 'configserver.json';

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
        $this->crearDB();
        $this->crearTablas();
        $this->conectar();
    }

    /**
     * Creación de la tabla encargada de gestionar los competidores
     *
     * @return void
     */
    private function crearTablas() : void 
    {
        $fileContent = $this->getConfiguration();
        try {
            if(\gettype($fileContent) === 'object') {
                $this->pdo = new PDO("mysql:host=" . $fileContent->host . ";dbname=" . $fileContent->dbname, $fileContent->user, $fileContent->password);
            } else {
                $this->pdo = new PDO("mysql:host=" . $fileContent['host'] . ";dbname=" . $fileContent["dbname"], $fileContent["user"], $fileContent["password"]);
            }

            $this->pdo->exec('CREATE TABLE IF NOT EXISTS competidores(
                id INT AUTO_INCREMENT NOT NULL,
                nombre VARCHAR(20),
                apellidos VARCHAR(50),
                anio SMALLINT,
                sexo CHAR(1),
                club VARCHAR(60),
                clubComunidad VARCHAR(60),
                competicion VARCHAR(60),
                fechaCompeticion DATE,
                lugarCompeticion VARCHAR(60),
                comunidadCompeticion VARCHAR(60),
                tipoPiscina CHAR(3),
                prueba VARCHAR(60),
                agrupacion VARCHAR(100),
                categoria VARCHAR(20),
                tipoSerioe VARCHAR(30),
                ronda TINYINT,
                tiempo VARCHAR(8),
                tiempoConvertido VARCHAR(8),
                posicion TINYINT,
                exclusion VARCHAR(20),
                descalificado CHAR(2),
                CONSTRAINT pk_competidores PRIMARY KEY (id)
            )ENGINE = InnoDb;');
        }
        catch(PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Creación de la base de datos
     *
     * @return void
     */
    private function crearDB() : void 
    {
        $fileContent = $this->getConfiguration();
        try {
            if(\gettype($fileContent) === 'object') {
                $this->pdo = new PDO("mysql:host=" . $fileContent->host, $fileContent->user, $fileContent->password);
            } else {
                $this->pdo = new PDO("mysql:host=" . $fileContent['host'], $fileContent["user"], $fileContent["password"]);
            }
            
            $this->pdo->exec('CREATE DATABASE IF NOT EXISTS db_competiciones CHARACTER SET utf8 COLLATE utf8_general_ci;');
        }
        catch(PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Realiza la conexión con la base de datos.
     * 
     * @return void
     */
    private function conectar() : void
    {
        $fileContent = $this->getConfiguration();
        try {
            if(\gettype($fileContent) === 'object') {
                $this->pdo = new PDO("mysql:host=" . $fileContent->host . ";dbname=" . $fileContent->dbname . 
                                     ";charset=utf8", $fileContent->user, $fileContent->password);
            } else {
                $this->pdo = new PDO("mysql:host=" . $fileContent['host'] . ";dbname=" . $fileContent["dbname"] . 
                                     ";charset=utf8", $fileContent["user"], $fileContent["password"]);
            }
            $this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        }
        catch(PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Recoge la información del fichero JSON
     * 
     * @return object
     */
    private function getConfiguration() : object
    {
        $contentString = file_get_contents(__DIR__ . '/..\/' . self::FILE_NAME);
        return json_decode($contentString);
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
     * Obtiene todas las competiciones que existen.
     *
     * @return array
     */
    public function getCompeticiones() : array
    {
        $stmt = $this->pdo->prepare("SELECT DISTINCT competicion FROM competidores");
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
     * Recibe los datos proporcionados por el usuario y los usa para calcular y asignar los puntos a los competidores
     * 
     * @param  string $bloqueo
     * @param  string $cantParticipantes
     * @param  string $puntInicial
     * @param  string $difPuntos
     * @param  string $temporada
     */
    public function asignarPuntos($bloqueo, $cantParticipantes, $puntInicial, $difPuntos, $temporada) : void
    {
        $puntos = [];
        array_push($puntos, $puntInicial);
        for ($i = 1; $i < $cantParticipantes ; $i++) { 
            array_push($puntos, $puntos[$i-1] + $difPuntos);
        }
        
        $generos = $this->getGeneros();
        $pruebas = $this->getPruebas();
        $categorias = $this->getCategoria();
        $competiciones  = $this->getCompeticiones();
        $compeditoresPuntos = [];

        foreach ($competiciones as $competicion) {
            foreach ($categorias as $categoria) {
                foreach ($pruebas as $prueba) {
                    foreach ($generos as $genero) {
                        $compeditor = $this->getPosiciones($prueba['prueba'], $genero['sexo'], $categoria['categoria'], $competicion['competicion'], $cantParticipantes, $puntInicial, $difPuntos, $bloqueo);
                        if(sizeof($compeditor) != 0){
                            if(sizeof($compeditor) > 1) {
                                foreach ($compeditor as $value) {
                                    $compeditoresPuntos[] = $value;
                                }
                            }
                            else
                                $compeditoresPuntos[] = $compeditor[0];
                        }
                    }
                }
            }
        }
        $this->insertarPuntosCompetidores($compeditoresPuntos);
    }

    public function getPosiciones($prueba, $sexo, $categoria, $competicion, $cantParticipantes, $puntInicial, $difPuntos, $bloqueo) : array
    {
        $stmt = $this->pdo->prepare('SELECT id, nombre, apellidos, anio, club, tipoPiscina, tiempo, tiempoConvertido, posicion, exclusion, descalificado FROM competidores WHERE prueba = :prueba AND sexo = :sexo AND categoria = :categoria AND competicion = :competicion ORDER BY posicion ASC');
        $stmt->bindParam(':prueba', $prueba, PDO::PARAM_STR, 60);
        $stmt->bindParam(':sexo', $sexo, PDO::PARAM_STR, 1);
        $stmt->bindParam(':categoria', $categoria, PDO::PARAM_STR, 20);
        $stmt->bindParam(':competicion', $competicion, PDO::PARAM_STR, 60);
        $stmt->execute();
        
        $row = $stmt->fetchAll();
        $competidores = [];
        $puntuados = 0;     
        $puntos = $puntInicial;

        foreach($row as $fil) {
            $descalificado = $fil['descalificado'];
            $club = $fil['club'];   
            
            $puntuadosClub = 1;
            foreach ($competidores as $value) {
                if($club == $value['club']){
                    $puntuadosClub++;
                }
            }     

            if($puntuados != 0) {
                if($puntuados < $cantParticipantes && $descalificado == 'No') {
                    if($puntuadosClub <= 3) {                             
                        $puntInicial = $puntInicial - $difPuntos;
                        $puntos = $puntInicial;
                    }
                    else {                        
                        if($bloqueo == 'S') {  
                            $puntInicial = $puntInicial - $difPuntos;
                        }
                        $puntos = 0;
                        $puntuados--;
                    }
                }
                else {
                    $puntos = 0;
                }
            } 
            else {
                if($descalificado != 'No')
                    $puntos = 0;
            }

            $competidor = [
                'id' => $fil['id'], 
                'nombre' => $fil['nombre'], 
                'apellidos' => $fil['apellidos'], 
                'anio' => $fil['anio'], 
                'club' => $club,
                'tipoPiscina' => $fil['tipoPiscina'],
                'tiempo' => $fil['tiempo'],
                'tiempoConvertido' => $fil['tiempoConvertido'],
                'posicion' => $fil['posicion'],
                'exclusion' => $fil['exclusion'],
                'descalificado' => $descalificado,
                'competicion' => $competicion,
                'categoria' => $categoria,
                'prueba' => $prueba,
                'genero' => $sexo,
                'puntos' => $puntos
            ];
            array_push($competidores, $competidor);            
            $puntuados++;
        }
        
        return $competidores;      
    }

    public function insertarPuntosCompetidores($compeditoresPuntos) 
    {
        $stmt = $this->pdo->exec('CREATE TABLE IF NOT EXISTS competidores_puntos (id INT NOT NULL, nombre VARCHAR(20), apellidos VARCHAR(50), anio SMALLINT, sexo CHAR(1), club VARCHAR(60), competicion VARCHAR(60), prueba VARCHAR(60), categoria VARCHAR(20), tiempo VARCHAR(8), tiempoConvertido VARCHAR(8), posicion TINYINT, exclusion VARCHAR(20), descalificado CHAR(2), tipoPiscina CHAR(3), puntos INT, CONSTRAINT pk_competidores_puntos PRIMARY KEY (id));');
        foreach ($compeditoresPuntos as $compeditor) {
            $stmt = $this->pdo->prepare('INSERT INTO competidores_puntos VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->bindParam(1, $compeditor['id']);
            $stmt->bindParam(2, $compeditor['nombre']); 
            $stmt->bindParam(3, $compeditor['apellidos']);
            $stmt->bindParam(4, $compeditor['anio']); 
            $stmt->bindParam(5, $compeditor['genero']);
            $stmt->bindParam(6, $compeditor['club']);
            $stmt->bindParam(7, $compeditor['competicion']);
            $stmt->bindParam(8, $compeditor['prueba']);
            $stmt->bindParam(9, $compeditor['categoria']); 
            $stmt->bindParam(10, $compeditor['tiempo']);
            $stmt->bindParam(11, $compeditor['tiempoConvertido']);
            $stmt->bindParam(12, $compeditor['posicion']);
            $stmt->bindParam(13, $compeditor['exclusion']);
            $stmt->bindParam(14, $compeditor['descalificado']);
            $stmt->bindParam(15, $compeditor['tipoPiscina']);
            $stmt->bindParam(16, $compeditor['puntos']); 
            $stmt->execute();      
        }        
    }

    public function informesCategorias() : array
    {
        $categorias = $this->getCategoria();
        $informeCategorias = [];
        foreach ($categorias as $categoria) {
            $informeCategorias[] = $this->informeCategoria($categoria['categoria']);
        }
        return $informeCategorias;
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
        
        $informe = [];
        foreach ($pruebas as $prueba) {
            foreach ($generos as $genero) {
                $competidor = [
                    'Categoria' => $categoria,
                    'Prueba' => $prueba['prueba'],
                    'Genero' => $genero['sexo'],
                    'Competidores' => $this->getCompetidoresCategoria($categoria, $genero['sexo'], $prueba['prueba'])
                ];
                if(!empty($competidor['Competidores']))
                    $informe[] = $competidor;
            }            
        }
        return $informe;
    }   

    /**
     * Obtiene todos los competidores correspondientes a los parámetros indicados.
     * 
     * @param string $categoria
     * @param string $sexo
     * @param string $prueba
     * @return array
     */
    public function getCompetidoresCategoria(string $categoria, string $sexo, string $prueba)
    {
        $competidores = [];
        $stmt = $this->pdo->prepare("SELECT nombre, apellidos, anio, club, tipoPiscina, tiempoConvertido, puntos FROM competidores_puntos WHERE categoria = ? AND sexo = ? AND prueba = ?");
        $stmt->bindParam(1, $categoria);
        $stmt->bindParam(2, $sexo);
        $stmt->bindParam(3, $prueba);
        $stmt->execute();

        $row = $stmt->fetchAll();
        foreach($row as $fil) {
            $competidor = [
                'nombre' => $fil['nombre'], 
                'apellidos' => $fil['apellidos'], 
                'anio' => $fil['anio'], 
                'club' => $fil['club'], 
                'tipoPiscina' => $fil['tipoPiscina'],
                'tiempoConvertido' => $fil['tiempoConvertido'],
                'puntos' => $fil['puntos']
            ];
            array_push($competidores, $competidor);
        }
        
        return $competidores;
    }

    public function informeCategoriaClub(string $categoria) : array
    {
        $generos = $this->getGeneros();
        $pruebas = $this->getPruebas();

        $informe = [];
        foreach ($pruebas as $prueba) {
            foreach ($generos as $genero) {
                $competidor = [
                    'Categoria' => $categoria,
                    'Prueba' => $prueba['prueba'],
                    'Genero' => $genero['sexo'],
                    'Competidores' => $this->getCompetidoresCategoriaClub($categoria, $genero['sexo'], $prueba['prueba'])
                ];
                if(!empty($competidor['Competidores']))
                    $informe[] = $competidor;
            }
        }
        return $informe;
    }

    public function getCompetidoresCategoriaClub(string $categoria, string $sexo, string $prueba)
    {        
        $competidores = [];
        $stmt = $this->pdo->prepare("SELECT club, tipoPiscina, tiempoConvertido, puntos, exclusion, descalificado FROM competidores_puntos WHERE categoria = ? AND sexo = ? AND prueba = ? GROUP BY club ORDER BY puntos DESC");
        $stmt->bindParam(1, $categoria);
        $stmt->bindParam(2, $sexo);
        $stmt->bindParam(3, $prueba);
        $stmt->execute();

        $row = $stmt->fetchAll();
        foreach($row as $fil) {
            $competidor = [
                'Club' => $fil['club'], 
                'Piscina' => $fil['tipoPiscina'],
                'Marca' => $fil['tiempoConvertido'],
                'Puntos' => $fil['puntos'],
                'Exclusion' => $fil['exclusion'],
                'Descalificado' => $fil['descalificado']
            ];
            array_push($competidores, $competidor);
        }
        return $competidores;
    }

    public function informesCategoriasClub() : array
    {
        $categorias = $this->getCategoria();
        $informeCategoriaClub = [];
        foreach ($categorias as $categoria) {
            $informeCategoriaClub[] = $this->informeCategoriaClub($categoria['categoria']);
        }
        return $informeCategoriaClub;
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
        $timeConvert = str_replace ('=' , '' , str_replace ('"' , '' , $competidor[17]));
        //En caso de que no tenga tiempo ese competidor se coloca el campo a null.
        if($tiempo == "")
            $tiempo = null;
        if($timeConvert == "")
            $timeConvert = null;
    
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
        $stmt->bindParam(':tiempoConvertido', $timeConvert, PDO::PARAM_STR, 8);
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
     * Elimina todos los competidores de la tabla
     *
     * @return void
     */
    public function deleteAll() : void
    {
        $stmt = $this->pdo->prepare('TRUNCATE TABLE competidores');
        $stmt->execute();
        $stmt = $this->pdo->prepare('TRUNCATE TABLE competidores_puntos');
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
    //var_dump($consulta->informeCategoria('Infantil'));
    //echo json_encode($consulta->informeCategoriaClub('Infantil'));
    //var_dump($consulta->informeCategoriaClub('Infantil'));
    //echo json_encode($consulta->getCompetidoresCategoria('Infantil', 'F', '50 m. remolque de maniquí pequeño'));
    //var_dump($consulta->getPosiciones('50 m. remolque de maniquí', 'M', 'Absoluto', '5º Jornada Liga - CANARIAS', 7, 20, 2, 'N'));
    //var_dump($consulta->informesCategorias());
    //var_dump(key($consulta->getAllCompetidores()));
?>