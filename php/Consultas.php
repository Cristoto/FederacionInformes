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
    private $pdo;

    function __construct(){
        $this->conectar();
    }

    /**
     * Realiza la conexión con la base de datos.
     * @return void
     */
    private function conectar() {
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
     * Obtiene todos los generos que existentes.
     * @return array
     */
    public function getGeneros() {
        $stmt = $this->pdo->prepare("SELECT DISTINCT sexo FROM competidores");
        $stmt->execute();

        return $stmt->fetchAll();  
    }

    /**
     * Obtiene todas las pruebas que existentes.
     * @return array
     */
    public function getPruebas() {
        $stmt = $this->pdo->prepare("SELECT DISTINCT prueba FROM competidores");
        $stmt->execute();

        return $stmt->fetchAll();  
    }

    /**
     * Obtiene todas las categorias que existentes.
     * @return array
     */
    public function getCategoria() {
        $stmt = $this->pdo->prepare("SELECT DISTINCT categoria FROM competidores");
        $stmt->execute();

        return $stmt->fetchAll();  
    }

    /**
     * Obtiene todas las temporadas que existen.
     *
     * @return array
     */
    public function getTemporadas(){
        $stmt = $this->pdo->prepare("SELECT DISTINCT fechaCompeticion FROM competidores");
        $stmt->execute();

        return $stmt->fetchAll();  
    }

    /**
     * Devuelve toda la informacion de los competidores.
     * @return array
     */
    public function getAllCompetidores() {
        $stmt = $this->pdo->prepare('SELECT * FROM competidores');
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Obtiene todos los competidores correspondientes a los parametros indicados.
     * @param string $categoria
     * @param string $sexo
     * @param string $prueba
     * @return array
     */
    public function getCompetidoresCategoria(string $categoria, string $sexo, string $prueba) {
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
     * Devuelve un array con la estructura necesaria para generar el informe por categorias.
     * @param string $categoria
     * @return array
     */
    public function informeCategoria(string $categoria) {
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

    public function informeClubes() {

    }
    
    public function insertarCompetidor(array $competidor) {
        $consulta = $this->pdo->prepare("INSERT INTO competidores VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $consulta->execute($competidor);
    }
}
    //$consulta = new Consultas();
    //header('Content-Type: application/json');
    //echo json_encode($consulta->informeCategoria('Infantil'));
    //echo json_encode($consulta->getCompetidoresCategoria('Infantil', 'F', '100 m. natación con obstáculos'));

    /*$array = ['PEDRO', 'PEREZ', 'M'];
    $consulta->insertarCompetidor($array);*/
?>