# Registro de cambios

## 2026-11-07 — Revisión inicial del ZIP

**Tipo:** análisis y documentación.  
**Código fuente:** sin modificaciones.

### Revisado

- Estructura del proyecto y flujo `public/index.php` → `routes/rutas.php` → controlador → modelo/vista.
- Controladores y modelos de Inventario, Compras y Contratos.
- Vistas principales y archivos JavaScript.
- Script `BD_ISHUME.sql`.
- Presencia de tablas de Ventas y DetalleVenta, sin implementación del módulo en PHP/JS.

### Comprobaciones estáticas

- 24 archivos PHP comprobados con `php -l`: sin errores de sintaxis.
- 6 archivos JavaScript comprobados con `node --check`: sin errores de sintaxis.

### Hallazgos importantes

- Contratos: el formulario y el controlador no coinciden en el nombre del campo de dirección del cliente.
- Contratos: el endpoint de registro de colegio devuelve `ok: false` en la respuesta de éxito.
- Contratos: el botón de registro no tiene manejador en `contratos.js`; además, el formulario no contiene los datos de adelanto que el controlador/modelo esperan.
- Contratos: la función `validarDatos()` existe, pero no se invoca y contiene inconsistencias.
- Panel principal: el CSS de inventario se solicita como `Inventario.css`, mientras el archivo del proyecto es `inventario.css`. Puede fallar en servidores sensibles a mayúsculas/minúsculas.
- Compras: revisar cómo se conserva y representa el importe de las anulaciones parciales y totales.
- Ventas: existen tablas en SQL, pero no se encontraron archivos de implementación del módulo.

### Próximo paso recomendado

Completar y probar el flujo de Compras; después corregir el registro de Contratos para que la creación de contratos y pagos sea confiable; luego implementar Ventas utilizando las reglas de negocio acordadas y el esquema SQL validado.
