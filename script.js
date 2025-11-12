/**
 * Script.js - Manejo de eventos y peticiones Fetch
 * Laboratorio CRUD con Fetch API
 */

// Variables globales
let modoEdicion = false;

// Event Listeners cuando el DOM está cargado
document.addEventListener('DOMContentLoaded', function() {
    // Cargar productos al iniciar
    listarProductos();

    // Event listener para el formulario
    document.getElementById('formProducto').addEventListener('submit', manejarSubmit);

    // Event listener para el botón buscar
    document.getElementById('btnBuscar').addEventListener('click', buscarProducto);

    // Event listener para el botón limpiar
    document.getElementById('btnLimpiar').addEventListener('click', limpiarFormulario);
});

/**
 * Manejador del submit del formulario
 * Determina si se debe guardar o modificar según el modo
 */
async function manejarSubmit(e) {
    e.preventDefault();

    // Validación del lado del cliente
    if (!validarFormulario()) {
        return;
    }

    // Obtener datos del formulario
    const formData = new FormData(e.target);

    // Determinar la acción según el modo
    const accion = modoEdicion ? 'Modificar' : 'Guardar';
    formData.append('Accion', accion);

    // Enviar datos
    await enviarDatos(formData);
}

/**
 * Validar formulario del lado del cliente
 */
function validarFormulario() {
    const codigo = document.getElementById('codigo').value.trim();
    const producto = document.getElementById('producto').value.trim();
    const precio = document.getElementById('precio').value;
    const cantidad = document.getElementById('cantidad').value;

    if (!codigo || !producto || !precio || !cantidad) {
        mostrarAlerta('warning', 'Validación', 'Todos los campos son obligatorios');
        return false;
    }

    if (parseFloat(precio) < 0) {
        mostrarAlerta('warning', 'Validación', 'El precio debe ser mayor o igual a 0');
        return false;
    }

    if (parseInt(cantidad) < 0) {
        mostrarAlerta('warning', 'Validación', 'La cantidad debe ser mayor o igual a 0');
        return false;
    }

    return true;
}

/**
 * Enviar datos al servidor mediante Fetch
 */
async function enviarDatos(formData) {
    try {
        // Mostrar loading
        Swal.fire({
            title: 'Procesando...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Realizar petición fetch
        const response = await fetch('registrar.php', {
            method: 'POST',
            body: formData
        });

        // Verificar si la respuesta es ok
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }

        // Obtener respuesta JSON
        const data = await response.json();

        // Cerrar loading
        Swal.close();

        // Manejar respuesta según el resultado
        if (data.success) {
            // Éxito
            await Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });

            // Limpiar formulario y recargar lista
            limpiarFormulario();
            listarProductos();
        } else {
            // Error de validación o lógica de negocio
            let erroresHTML = '';
            if (data.errors && Array.isArray(data.errors)) {
                erroresHTML = '<ul class="text-start">';
                data.errors.forEach(error => {
                    erroresHTML += `<li>${error}</li>`;
                });
                erroresHTML += '</ul>';
            }

            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: `<p>${data.message}</p>${erroresHTML}`
            });
        }

    } catch (error) {
        Swal.close();
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo conectar con el servidor. Por favor intente nuevamente.'
        });
    }
}

/**
 * Buscar producto por código
 */
async function buscarProducto() {
    const codigo = document.getElementById('codigo').value.trim();

    if (!codigo) {
        Swal.fire({
            icon: 'warning',
            title: 'Atención',
            text: 'Por favor ingrese un código para buscar'
        });
        return;
    }

    try {
        // Crear FormData
        const formData = new FormData();
        formData.append('Accion', 'Buscar');
        formData.append('codigo', codigo);

        // Mostrar loading
        Swal.fire({
            title: 'Buscando...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Realizar petición
        const response = await fetch('registrar.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }

        const data = await response.json();
        Swal.close();

        // Switch para manejar la respuesta
        switch (data.success) {
            case true:
                // Producto encontrado - llenar formulario
                document.getElementById('productoId').value = data.data.id;
                document.getElementById('codigo').value = data.data.codigo;
                document.getElementById('producto').value = data.data.producto;
                document.getElementById('precio').value = data.data.precio;
                document.getElementById('cantidad').value = data.data.cantidad;

                // Cambiar a modo edición
                modoEdicion = true;
                document.getElementById('btnGuardar').innerHTML = 
                    '<i class="bi bi-pencil"></i> Actualizar Producto';
                document.getElementById('btnGuardar').classList.remove('btn-success');
                document.getElementById('btnGuardar').classList.add('btn-warning');

                await Swal.fire({
                    icon: 'success',
                    title: 'Producto Encontrado',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                });
                break;

            case false:
                // Producto no encontrado
                Swal.fire({
                    icon: 'info',
                    title: 'No encontrado',
                    text: data.message
                });
                break;

            default:
                Swal.fire({
                    icon: 'warning',
                    title: 'Respuesta inesperada',
                    text: 'La respuesta del servidor no es válida'
                });
        }

    } catch (error) {
        Swal.close();
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de búsqueda',
            text: 'No se pudo realizar la búsqueda. Intente nuevamente.'
        });
    }
}

/**
 * Listar todos los productos en la tabla
 */
async function listarProductos() {
    try {
        // Crear FormData
        const formData = new FormData();
        formData.append('Accion', 'Listar');

        // Realizar petición
        const response = await fetch('registrar.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }

        const data = await response.json();

        // Obtener el tbody
        const tbody = document.getElementById('tablaProductos');

        if (data.success && data.data.length > 0) {
            // Construir filas de la tabla
            let html = '';
            data.data.forEach(producto => {
                html += `
                    <tr>
                        <td>${producto.id}</td>
                        <td>${producto.codigo}</td>
                        <td>${producto.producto}</td>
                        <td>$${parseFloat(producto.precio).toFixed(2)}</td>
                        <td>${producto.cantidad}</td>
                        <td>
                            <button class="btn btn-sm btn-warning action-btn" 
                                    onclick="editarProducto(${producto.id}, '${producto.codigo}', '${producto.producto}', ${producto.precio}, ${producto.cantidad})">
                                <i class="bi bi-pencil"></i> Editar
                            </button>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        } else {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                        <p class="mt-2">No hay productos registrados</p>
                    </td>
                </tr>
            `;
        }

    } catch (error) {
        console.error('Error al listar productos:', error);
        document.getElementById('tablaProductos').innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-danger">
                    <i class="bi bi-exclamation-triangle"></i> Error al cargar los productos
                </td>
            </tr>
        `;
    }
}

/**
 * Llenar formulario para editar un producto
 */
function editarProducto(id, codigo, nombre, precio, cantidad) {
    document.getElementById('productoId').value = id;
    document.getElementById('codigo').value = codigo;
    document.getElementById('producto').value = nombre;
    document.getElementById('precio').value = precio;
    document.getElementById('cantidad').value = cantidad;

    // Cambiar a modo edición
    modoEdicion = true;
    document.getElementById('btnGuardar').innerHTML = 
        '<i class="bi bi-pencil"></i> Actualizar Producto';
    document.getElementById('btnGuardar').classList.remove('btn-success');
    document.getElementById('btnGuardar').classList.add('btn-warning');

    // Scroll hacia el formulario
    document.getElementById('formProducto').scrollIntoView({ behavior: 'smooth' });

    // Mostrar alerta
    mostrarAlerta('info', 'Modo Edición', 'Modifique los datos y presione "Actualizar Producto"');
}

/**
 * Limpiar formulario y volver a modo inserción
 */
function limpiarFormulario() {
    document.getElementById('formProducto').reset();
    document.getElementById('productoId').value = '';
    
    // Volver a modo inserción
    modoEdicion = false;
    document.getElementById('btnGuardar').innerHTML = 
        '<i class="bi bi-save"></i> Registrar Producto';
    document.getElementById('btnGuardar').classList.remove('btn-warning');
    document.getElementById('btnGuardar').classList.add('btn-success');

    // Limpiar alerta si existe
    document.getElementById('alertContainer').innerHTML = '';
}

/**
 * Mostrar alerta con Bootstrap
 */
function mostrarAlerta(tipo, titulo, mensaje) {
    const alertContainer = document.getElementById('alertContainer');
    const iconos = {
        success: 'check-circle-fill',
        warning: 'exclamation-triangle-fill',
        danger: 'exclamation-octagon-fill',
        info: 'info-circle-fill'
    };

    const html = `
        <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
            <i class="bi bi-${iconos[tipo]}"></i>
            <strong>${titulo}:</strong> ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;

    alertContainer.innerHTML = html;

    // Auto-cerrar después de 5 segundos
    setTimeout(() => {
        alertContainer.innerHTML = '';
    }, 5000);
}
