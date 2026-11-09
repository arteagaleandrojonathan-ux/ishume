# ISHUME — Contexto del proyecto

**Última revisión:** 2026-11-07  
**Versión revisada:** ZIP `BD_ISHUME_1(1).zip`  
**Ruta local de trabajo indicada:** `C:\\xampp\\htdocs\\BD_ISHUME_1`

## 1. Objetivo

ISHUME es un sistema web para gestionar el inventario de cuadros y anuarios, compras a proveedores, contratos con clientes/colegios y, posteriormente, ventas y pagos. La base de datos contempla promociones y descuentos.

## 2. Tecnologías y entorno

- PHP.
- MySQL/MariaDB mediante PDO.
- HTML, CSS y JavaScript.
- XAMPP como entorno local.
- Arquitectura MVC sencilla, con un enrutador central.

La conexión está definida en `app/config/conection.php`. En la copia revisada usa `localhost`, base `BD_ISHUME_1`, usuario `root` y contraseña vacía. **No añadir credenciales reales a esta documentación**; conviene trasladarlas a configuración local no versionada cuando se prepare el entorno.

## 3. Arquitectura y flujo

Punto de entrada:

- `public/index.php`: carga `routes/rutas.php`.

Enrutamiento:

- `routes/rutas.php`: lee `$_GET['opcion']` y deriva la petición al controlador correspondiente.

Separación por capas:

- `app/controllers/`: recibe peticiones, prepara datos y decide qué vista cargar.
- `app/models/`: contiene consultas y operaciones de base de datos.
- `app/views/`: interfaz HTML/PHP.
- `public/js/`: interacciones del navegador.
- `public/css/`: estilos.
- `public/img/`: imágenes de la interfaz y modelos.

`PanelPrincipal.php` funciona como plantilla general y carga la vista indicada mediante `$vista`.

## 4. Estructura identificada

```text
BD_ISHUME_1/
├── app/
│   ├── config/conection.php
│   ├── controllers/
│   │   ├── compra.php
│   │   ├── configuracion.php
│   │   ├── contratos.php
│   │   ├── dashboard.php
│   │   └── inventario.php
│   ├── models/
│   │   ├── compra.php
│   │   ├── contrato.php
│   │   ├── dashboard.php
│   │   └── inventario.php
│   └── views/
│       ├── compra.php
│       ├── configuracion.php
│       ├── contratos.php
│       ├── darDeBaja.php
│       ├── dashboard.php
│       ├── detalleModelo.php
│       ├── editarModelo.php
│       ├── inventario.php
│       ├── nuevoContrato.php
│       ├── PanelPrincipal.php
│       ├── Papelera.php
│       └── registrosCompra.php
├── documentacion/
├── public/
│   ├── css/
│   ├── img/
│   ├── js/
│   └── index.php
├── routes/rutas.php
└── BD_ISHUME.sql
```

## 5. Estado observado por módulo

### Inventario

Archivos principales:

- `app/controllers/inventario.php`
- `app/models/inventario.php`
- `app/views/inventario.php`
- `app/views/editarModelo.php`
- `app/views/darDeBaja.php`
- `app/views/Papelera.php`
- `app/views/detalleModelo.php`
- `public/js/editarModelo.js`
- `public/js/bajaInventario.js`
- `public/js/detalleModelo.js`

Se identifican listado, filtros, paginación, edición, detalle, baja de cantidades y consulta de bajas. El listado principal filtra modelos activos. La baja se procesa dentro de una transacción: registra la operación y resta unidades del stock.

### Compras

Archivos principales:

- `app/controllers/compra.php`
- `app/models/compra.php`
- `app/views/compra.php`
- `app/views/registrosCompra.php`
- `public/js/compra.js`
- `public/js/anularCompra.js`

Se identifica registro de compras, selección de proveedor y modelos, historial con filtros, detalle y anulación parcial o total. El modelo calcula subtotales en el servidor, aumenta el stock al registrar y descuenta el stock al anular líneas seleccionadas. Las operaciones principales usan transacciones.

La anulación parcial recalcula `Compras.TotalCompra` con los detalles que siguen vigentes. En la anulación completa se cambia el estado de la compra, pero se conserva el total original. La tabla `AnularCompras` no tiene una columna específica para el importe revertido. Este comportamiento requiere una decisión y pruebas antes de dar el módulo por cerrado.

### Contratos

Archivos principales:

- `app/controllers/contratos.php`
- `app/models/contrato.php`
- `app/views/contratos.php`
- `app/views/nuevoContrato.php`
- `public/js/contratos.js`

Se identifica búsqueda y registro de colegios, carga de distritos por provincia y una operación de registro que intenta crear cliente, contrato y pago inicial dentro de una transacción.

La implementación del formulario de registro está incompleta/inconsistente. Revisar `PENDIENTES.md` antes de continuar con este módulo.

### Ventas

En `BD_ISHUME.sql` existen las tablas `Ventas` y `DetalleVenta`, con relaciones hacia contratos, modelos, promociones y descuentos. Sin embargo, en el ZIP revisado no se encontraron controlador, modelo, vista, JavaScript, CSS ni rutas específicos de ventas. Por tanto, el módulo todavía no está implementado en el código entregado.

### Dashboard y configuración

- `app/models/dashboard.php` está vacío en la copia revisada.
- Hay un controlador y una vista básicos para Dashboard.
- Configuración también tiene una estructura mínima.

## 6. Base de datos identificada

El script `BD_ISHUME.sql` define, entre otras, estas tablas:

- `proveedores`
- `categorias`
- `TipoMarco`
- `modelos`
- `BajasInventario`
- `Compras`
- `DetalleCompra`
- `AnularCompras`
- `Cliente`
- `Colegios`
- `Provincias`
- `Distritos`
- `Contratos`
- `Ventas`
- `DetalleVenta`
- `Pago`
- `MetodoPago`
- `Promociones`
- `Descuentos`
- `Promocion_Detalle`

La estructura de `DetalleVenta` establece que un detalle puede tener promoción o descuento, pero no ambos simultáneamente (`chk_detalleventa_un_beneficio`).

## 7. Comprobaciones realizadas en esta revisión

- Se revisó el contenido del ZIP y la estructura de carpetas.
- Se revisaron los controladores, modelos, vistas, rutas, JavaScript y el script SQL.
- Se ejecutó `php -l` sobre los 24 archivos PHP: no se detectaron errores de sintaxis.
- Se ejecutó `node --check` sobre los 6 archivos JavaScript: no se detectaron errores de sintaxis.

Estas comprobaciones son estáticas. **No equivalen a probar el sistema en el navegador ni a ejecutar todas las operaciones contra una base de datos real.** No se verificó la ejecución funcional completa, los datos actuales de MySQL ni los flujos visuales.

## 8. Reglas para continuar

1. Mantener la arquitectura existente salvo que se acuerde una mejora.
2. Antes de modificar un proceso que afecta stock, compras, ventas, contratos o pagos, explicar su impacto en los demás módulos.
3. No inventar tablas, columnas, métodos, rutas o archivos: comprobarlos primero.
4. Calcular importes importantes en el servidor; no confiar en totales enviados por JavaScript.
5. Usar transacciones cuando una operación modifique varias tablas relacionadas.
6. No afirmar que una función está terminada hasta probar el flujo completo.
7. Actualizar `CHANGELOG.md` y `PENDIENTES.md` al terminar cada tarea.
