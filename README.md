# 🛒 CRUD de Productos - Fetch API

Sistema de gestión de productos desarrollado con Fetch API, PHP OOP y 
MySQL.

## 👨‍💻 Información del Proyecto

- **Estudiante:** [Tu Nombre Completo]
- **Curso:** ISF131 / ISF132
- **Profesor:** Ing. Irina Fong
- **Universidad:** Universidad Tecnológica de Panamá
- **Fecha:** Noviembre 2024

## 📋 Descripción

Aplicación web CRUD (Create, Read, Update, Delete) que permite la gestión 
de productos mediante operaciones asíncronas utilizando la Fetch API de 
JavaScript y programación orientada a objetos en PHP.

## 🚀 Tecnologías Utilizadas

- **Frontend:**
  - HTML5
  - CSS3
  - JavaScript (ES6+)
  - Bootstrap 5.3
  - SweetAlert2
  - Fetch API

- **Backend:**
  - PHP 7.4+ (Programación Orientada a Objetos)
  - MySQL 8.0
  - PDO (PHP Data Objects)

## 📁 Estructura del Proyecto
```
crud-productos/
├── index.html              # Interfaz principal
├── script.js              # Lógica del frontend con Fetch
├── registrar.php          # Controlador con Switch
├── Modelo/
│   ├── conexion.php      # Clase DB (Singleton)
│   └── Productos.php     # Clase Producto (CRUD)
└── README.md             # Documentación
```

## ⚙️ Instalación

### 1. Requisitos Previos
- MAMP/XAMPP/WAMP (servidor local)
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Navegador web moderno

### 2. Clonar el Repositorio
```bash
git clone https://github.com/TU_USUARIO/crud-productos-fetch.git
cd crud-productos-fetch
```

### 3. Configurar Base de Datos

**a) Crear la base de datos:**
```sql
CREATE DATABASE productosdb;
USE productosdb;
```

**b) Crear la tabla:**
```sql
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL,
    producto VARCHAR(100) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    cantidad INT NOT NULL
);
```

**c) Insertar datos de prueba (opcional):**
```sql
INSERT INTO productos (codigo, producto, precio, cantidad) VALUES
('P001', 'Laptop HP', 850.00, 15),
('P002', 'Mouse Logitech', 25.50, 50),
('P003', 'Teclado Mecánico', 75.00, 30);
```

### 4. Configurar Conexión

Edita el archivo `Modelo/conexion.php` y ajusta las credenciales:
```php
private $host = 'localhost';
private $db = 'productosdb';
private $usuario = 'root';
private $password = 'root'; // Cambia según tu configuración
```

### 5. Ejecutar el Proyecto

1. Coloca el proyecto en la carpeta de tu servidor:
   - MAMP: `/Applications/MAMP/htdocs/crud-productos/`
   - XAMPP: `C:/xampp/htdocs/crud-productos/`
   - WAMP: `C:/wamp64/www/crud-productos/`

2. Inicia tu servidor local

3. Abre el navegador y ve a:
```
   http://localhost:8888/crud-productos/
```

## 💡 Funcionalidades

✅ **Guardar Producto:** Registrar nuevos productos con validación
✅ **Buscar Producto:** Búsqueda por código de producto
✅ **Editar Producto:** Actualizar información de productos existentes
✅ **Listar Productos:** Visualizar todos los productos en tabla dinámica
✅ **Validaciones:** Cliente (JavaScript) y servidor (PHP)
✅ **Mensajes Interactivos:** Notificaciones con SweetAlert2
✅ **Diseño Responsivo:** Compatible con dispositivos móviles

## 🎯 Características Técnicas

### Switch en PHP
- Centralización de acciones (Guardar, Modificar, Buscar, Listar)
- Manejo de casos con `switch($_POST['Accion'])`

### Switch en JavaScript
- Control de respuestas del servidor
- Manejo de estados con `switch(data.success)`

### Programación Orientada a Objetos
- **Clase DB:** Patrón Singleton para conexión a BD
- **Clase Producto:** Encapsulamiento de lógica CRUD

### Seguridad
- Prepared Statements (PDO)
- Validación de datos (cliente y servidor)
- Protección contra SQL Injection
- Headers CORS configurados

## 📸 Capturas de Pantalla

![Sistema CRUD](screenshot.png)
*Interfaz principal del sistema*

## 🧪 Pruebas

### Test de Guardar
1. Completar formulario con datos válidos
2. Clic en "Registrar Producto"
3. Verificar mensaje de éxito
4. Confirmar en tabla

### Test de Buscar
1. Ingresar código en campo "Código"
2. Clic en "Buscar por Código"
3. Verificar que formulario se llena automáticamente

### Test de Editar
1. Buscar un producto existente
2. Modificar datos
3. Clic en "Actualizar Producto"
4. Verificar cambios en tabla

## 📝 Notas de Desarrollo

- Proyecto desarrollado como laboratorio académico
- Cumple con rúbrica de evaluación de 100 puntos
- Incluye todas las funcionalidades requeridas
- Código documentado y comentado

## 🤝 Contribuciones

Este es un proyecto académico. Si deseas contribuir o reportar problemas:

1. Fork el proyecto
2. Crea una rama (`git checkout -b feature/mejora`)
3. Commit tus cambios (`git commit -m 'Agregar mejora'`)
4. Push a la rama (`git push origin feature/mejora`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto es de uso académico y educativo.

## 📧 Contacto

**[Tu Nombre]**
- Email: tu-email@ejemplo.com
- GitHub: [@tu-usuario](https://github.com/tu-usuario)

---

⭐ **Si este proyecto te fue útil, considera darle una estrella en 
GitHub**
