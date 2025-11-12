<?php
/**
 * Clase DB - Manejo de conexión a base de datos con PDO
 * Patrón Singleton para una única instancia de conexión
 */
class DB {
    private static $instance = null;
    private $conexion;
    
    // Configuración de la base de datos
    private $host = 'localhost';
    private $db = 'productosdb';
    private $usuario = 'root';
    private $password = 'root'; // En MAMP por defecto es 'root', en XAMPP suele ser ''
    private $charset = 'utf8mb4';

    /**
     * Constructor privado para patrón Singleton
     */
    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";
            $opciones = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->conexion = new PDO($dsn, $this->usuario, $this->password, $opciones);
        } catch (PDOException $e) {
            throw new Exception("Error de conexión: " . $e->getMessage());
        }
    }

    /**
     * Obtener instancia única de la clase
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Obtener la conexión PDO
     */
    public function getConexion() {
        return $this->conexion;
    }

    /**
     * Insertar datos de forma segura
     * @param string $tabla Nombre de la tabla
     * @param array $datos Array asociativo con columna => valor
     * @return int ID del registro insertado
     */
    public function insertSeguro($tabla, $datos) {
        try {
            $columnas = implode(', ', array_keys($datos));
            $valores = ':' . implode(', :', array_keys($datos));
            
            $sql = "INSERT INTO {$tabla} ({$columnas}) VALUES ({$valores})";
            $stmt = $this->conexion->prepare($sql);
            
            foreach ($datos as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }
            
            $stmt->execute();
            return $this->conexion->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Error al insertar: " . $e->getMessage());
        }
    }

    /**
     * Actualizar datos de forma segura
     * @param string $tabla Nombre de la tabla
     * @param array $datos Array asociativo con columna => valor
     * @param string $condicion Condición WHERE
     * @param array $params Parámetros para la condición
     * @return bool True si se actualizó correctamente
     */
    public function updateSeguro($tabla, $datos, $condicion, $params = []) {
        try {
            $set = [];
            foreach ($datos as $key => $value) {
                $set[] = "{$key} = :{$key}";
            }
            $setString = implode(', ', $set);
            
            $sql = "UPDATE {$tabla} SET {$setString} WHERE {$condicion}";
            $stmt = $this->conexion->prepare($sql);
            
            // Bind de los datos a actualizar
            foreach ($datos as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }
            
            // Bind de los parámetros de la condición
            foreach ($params as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }
            
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar: " . $e->getMessage());
        }
    }

    /**
     * Ejecutar consulta SELECT
     * @param string $sql Consulta SQL
     * @param array $params Parámetros para prepared statement
     * @return array Resultados de la consulta
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->conexion->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }
            
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Error en consulta: " . $e->getMessage());
        }
    }

    /**
     * Evitar clonación del objeto
     */
    private function __clone() {}

    /**
     * Evitar deserialización del objeto
     */
    public function __wakeup() {
        throw new Exception("No se puede deserializar Singleton");
    }
}
?>