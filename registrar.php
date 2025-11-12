<?php
// Establecer el tipo de contenido como JSON
header("Content-Type: application/json");

// Habilitar CORS si es necesario
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Incluir la clase Producto
require_once 'Modelo/Productos.php';

try {
    // Verificar que sea una petición POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            'success' => false,
            'message' => 'Método no permitido. Use POST.'
        ]);
        exit;
    }

    // Verificar que exista el parámetro Accion
    if (!isset($_POST['Accion'])) {
        echo json_encode([
            'success' => false,
            'message' => 'No se especificó la acción a realizar',
            'errors' => ['El parámetro Accion es requerido']
        ]);
        exit;
    }

    // Obtener la acción a realizar
    $accion = trim($_POST['Accion']);

    // Instanciar la clase Producto
    $producto = new Producto();

    // Switch para manejar las diferentes acciones
    switch ($accion) {
        case 'Guardar':
            // Validar que vengan todos los campos necesarios
            if (!isset($_POST['codigo']) || !isset($_POST['producto']) || 
                !isset($_POST['precio']) || !isset($_POST['cantidad'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Faltan campos obligatorios',
                    'errors' => ['Debe enviar: codigo, producto, precio y cantidad']
                ]);
                exit;
            }

            // Establecer los datos del producto
            $producto->setCodigo($_POST['codigo']);
            $producto->setProducto($_POST['producto']);
            $producto->setPrecio($_POST['precio']);
            $producto->setCantidad($_POST['cantidad']);

            // Guardar el producto
            $respuesta = $producto->guardar();
            echo json_encode($respuesta);
            break;

        case 'Modificar':
            // Validar que vengan todos los campos necesarios incluyendo el ID
            if (!isset($_POST['id']) || !isset($_POST['codigo']) || 
                !isset($_POST['producto']) || !isset($_POST['precio']) || 
                !isset($_POST['cantidad'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Faltan campos obligatorios',
                    'errors' => ['Debe enviar: id, codigo, producto, precio y cantidad']
                ]);
                exit;
            }

            // Establecer los datos del producto
            $producto->setId($_POST['id']);
            $producto->setCodigo($_POST['codigo']);
            $producto->setProducto($_POST['producto']);
            $producto->setPrecio($_POST['precio']);
            $producto->setCantidad($_POST['cantidad']);

            // Editar el producto
            $respuesta = $producto->editar();
            echo json_encode($respuesta);
            break;

        case 'Buscar':
            // Validar que venga el código
            if (!isset($_POST['codigo']) || empty(trim($_POST['codigo']))) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Código no especificado',
                    'errors' => ['Debe proporcionar el código del producto a buscar']
                ]);
                exit;
            }

            // Buscar el producto
            $respuesta = $producto->buscar(trim($_POST['codigo']));
            echo json_encode($respuesta);
            break;

        case 'Listar':
            // Listar todos los productos
            $productos = $producto->listarTodos();
            echo json_encode([
                'success' => true,
                'message' => 'Productos obtenidos exitosamente',
                'data' => $productos
            ]);
            break;

        default:
            // Acción no válida
            echo json_encode([
                'success' => false,
                'message' => 'Acción no válida',
                'errors' => ['Las acciones válidas son: Guardar, Modificar, Buscar, Listar']
            ]);
            break;
    }

} catch (Exception $e) {
    // Capturar cualquier excepción no manejada
    echo json_encode([
        'success' => false,
        'message' => 'Error en el servidor',
        'errors' => [$e->getMessage()]
    ]);
}
?>