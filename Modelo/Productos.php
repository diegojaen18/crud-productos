<?php
require_once 'conexion.php';

/**
 * Clase Producto - Manejo de operaciones CRUD para productos
 */
class Producto {
    private $db;
    private $id;
    private $codigo;
    private $producto;
    private $precio;
    private $cantidad;
    private $errores = [];

    /**
     * Constructor
     */
    public function __construct() {
        $this->db = DB::getInstance();
    }

    /**
     * Setters
     */
    public function setId($id) {
        $this->id = $id;
    }

    public function setCodigo($codigo) {
        $this->codigo = trim($codigo);
    }

    public function setProducto($producto) {
        $this->producto = trim($producto);
    }

    public function setPrecio($precio) {
        $this->precio = $precio;
    }

    public function setCantidad($cantidad) {
        $this->cantidad = $cantidad;
    }

    /**
     * Getters
     */
    public function getId() {
        return $this->id;
    }

    public function getCodigo() {
        return $this->codigo;
    }

    public function getProducto() {
        return $this->producto;
    }

    public function getPrecio() {
        return $this->precio;
    }

    public function getCantidad() {
        return $this->cantidad;
    }

    public function getErrores() {
        return $this->errores;
    }

    /**
     * Validar los datos del producto
     * @return bool True si los datos son válidos
     */
    public function validar() {
        $this->errores = [];

        // Validar código
        if (empty($this->codigo)) {
            $this->errores[] = "El código es obligatorio";
        } elseif (strlen($this->codigo) > 20) {
            $this->errores[] = "El código no puede exceder 20 caracteres";
        }

        // Validar nombre del producto
        if (empty($this->producto)) {
            $this->errores[] = "El nombre del producto es obligatorio";
        } elseif (strlen($this->producto) > 100) {
            $this->errores[] = "El nombre del producto no puede exceder 100 caracteres";
        }

        // Validar precio
        if (empty($this->precio) && $this->precio !== '0') {
            $this->errores[] = "El precio es obligatorio";
        } elseif (!is_numeric($this->precio) || $this->precio < 0) {
            $this->errores[] = "El precio debe ser un número válido mayor o igual a 0";
        }

        // Validar cantidad
        if (empty($this->cantidad) && $this->cantidad !== '0') {
            $this->errores[] = "La cantidad es obligatoria";
        } elseif (!is_numeric($this->cantidad) || $this->cantidad < 0 || floor($this->cantidad) != $this->cantidad) {
            $this->errores[] = "La cantidad debe ser un número entero mayor o igual a 0";
        }

        return empty($this->errores);
    }

    /**
     * Guardar un nuevo producto
     * @return array Respuesta con success, message y data
     */
    public function guardar() {
        try {
            // Validar datos
            if (!$this->validar()) {
                return [
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $this->errores
                ];
            }

            // Verificar si el código ya existe
            $sql = "SELECT id FROM productos WHERE codigo = :codigo";
            $resultado = $this->db->query($sql, ['codigo' => $this->codigo]);
            
            if (!empty($resultado)) {
                return [
                    'success' => false,
                    'message' => 'El código del producto ya existe',
                    'errors' => ['El código ya está registrado en el sistema']
                ];
            }

            // Preparar datos para insertar
            $datos = [
                'codigo' => $this->codigo,
                'producto' => $this->producto,
                'precio' => $this->precio,
                'cantidad' => $this->cantidad
            ];

            // Insertar en la base de datos
            $idInsertado = $this->db->insertSeguro('productos', $datos);

            return [
                'success' => true,
                'message' => 'Producto guardado exitosamente',
                'accion' => 'Guardar',
                'data' => [
                    'id' => $idInsertado,
                    'codigo' => $this->codigo,
                    'producto' => $this->producto,
                    'precio' => $this->precio,
                    'cantidad' => $this->cantidad
                ]
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al guardar el producto',
                'errors' => [$e->getMessage()]
            ];
        }
    }

    /**
     * Editar un producto existente
     * @return array Respuesta con success, message y data
     */
    public function editar() {
        try {
            // Validar que tenga ID
            if (empty($this->id)) {
                return [
                    'success' => false,
                    'message' => 'ID del producto no especificado',
                    'errors' => ['Se requiere el ID del producto para editar']
                ];
            }

            // Validar datos
            if (!$this->validar()) {
                return [
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $this->errores
                ];
            }

            // Verificar si el producto existe
            $sql = "SELECT id FROM productos WHERE id = :id";
            $resultado = $this->db->query($sql, ['id' => $this->id]);
            
            if (empty($resultado)) {
                return [
                    'success' => false,
                    'message' => 'Producto no encontrado',
                    'errors' => ['El producto con el ID especificado no existe']
                ];
            }

            // Verificar si el código ya existe en otro producto
            $sql = "SELECT id FROM productos WHERE codigo = :codigo AND id != :id";
            $resultado = $this->db->query($sql, [
                'codigo' => $this->codigo,
                'id' => $this->id
            ]);
            
            if (!empty($resultado)) {
                return [
                    'success' => false,
                    'message' => 'El código del producto ya existe',
                    'errors' => ['El código ya está registrado en otro producto']
                ];
            }

            // Preparar datos para actualizar
            $datos = [
                'codigo' => $this->codigo,
                'producto' => $this->producto,
                'precio' => $this->precio,
                'cantidad' => $this->cantidad
            ];

            // Actualizar en la base de datos
            $this->db->updateSeguro('productos', $datos, 'id = :id', ['id' => $this->id]);

            return [
                'success' => true,
                'message' => 'Producto actualizado exitosamente',
                'accion' => 'Modificar',
                'data' => [
                    'id' => $this->id,
                    'codigo' => $this->codigo,
                    'producto' => $this->producto,
                    'precio' => $this->precio,
                    'cantidad' => $this->cantidad
                ]
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al editar el producto',
                'errors' => [$e->getMessage()]
            ];
        }
    }

    /**
     * Buscar producto por código
     * @param string $codigo Código del producto a buscar
     * @return array Respuesta con success, message y data
     */
    public function buscar($codigo) {
        try {
            $sql = "SELECT * FROM productos WHERE codigo = :codigo";
            $resultado = $this->db->query($sql, ['codigo' => $codigo]);

            if (empty($resultado)) {
                return [
                    'success' => false,
                    'message' => 'Producto no encontrado',
                    'data' => null
                ];
            }

            return [
                'success' => true,
                'message' => 'Producto encontrado',
                'accion' => 'Buscar',
                'data' => $resultado[0]
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al buscar el producto',
                'errors' => [$e->getMessage()]
            ];
        }
    }

    /**
     * Listar todos los productos
     * @return array Lista de productos
     */
    public function listarTodos() {
        try {
            $sql = "SELECT * FROM productos ORDER BY id DESC";
            return $this->db->query($sql);
        } catch (Exception $e) {
            return [];
        }
    }
}
?>