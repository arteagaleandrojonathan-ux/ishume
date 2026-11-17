    # Pendientes del proyecto

    > Los apartados históricos recogen la revisión del ZIP del 2026-11-07. La revisión del repositorio del 2026-11-17 incorpora estos pendientes prioritarios.

    ## Hallazgos del 2026-11-17

    - [ ] Aplicar `agregar_motivo_anulacion_ventas.sql` a la instalación existente y comprobar los paneles de confirmación en el navegador.

    - [ ] Verificar en el panel con datos reales los límites de Próximos eventos, el enlace al contrato y el desplazamiento en móvil.

    - [ ] Corregir la consulta de anulación de compras. Tiene una coma antes de `FROM` y le falta el agregado `total` que usa la anulación parcial.
    - [x] Unificar el estado de anulación en el código y el esquema nuevo. Se conserva una venta por contrato y el historial de pagos.
    - [ ] Aplicar `migrar_anulacion_ventas.sql` a la instalación existente y verificar una anulación desde el navegador.
    - [ ] Alinear el estado de la venta con el saldo al registrar el pago inicial y determinar en qué operación se descuenta el stock.
    - [ ] Retirar el volcado del formulario de contratos y revisar los datos de depuración ya guardados en el historial de Git.
    - [ ] Verificar control de acceso, protección CSRF y escape de salida en inventario.
    - [ ] Verificar en MySQL y navegador los flujos de compras, inventario, contratos, PDF, ventas y pagos.

    El formulario de contratos ya envía `DireccionCliente` y el controlador ya llama a `validarDatos()`. El módulo de ventas ya existe. Las referencias históricas a esas tareas reflejan el estado del ZIP.

    ## Prioridad alta — completar y probar procesos existentes

    ### Compras

    - [ ] Probar registro de compra con un proveedor activo y varios modelos.
    - [ ] Confirmar que el total y cada subtotal se calculen en el servidor.
    - [ ] Probar que una compra fallida no deje detalles ni stock parcialmente modificados.
    - [ ] Probar anulación de una sola línea, varias líneas y todas las líneas.
    - [ ] Confirmar que el stock nunca quede negativo al anular.
    - [ ] Definir cómo se registra el importe exacto revertido en anulaciones parciales y totales. Actualmente la tabla `AnularCompras` no conserva un importe de reversión y el comportamiento de `TotalCompra` difiere entre anulación parcial y completa.
    - [ ] Revisar validación de fechas: el controlador muestra un error si `fechaDesde` es posterior a `fechaHasta`, pero debe comprobarse que no se consulte y muestre una lista como si el filtro fuera válido.
    - [ ] Validar y limitar los parámetros de paginación recibidos por URL.

    ### Contratos

    - [ ] Corregir la coincidencia del campo de dirección del cliente: la vista envía `Direccion-cliente` y el controlador lee `DireccionCliente`.
    - [ ] Corregir la respuesta exitosa de `registrarColegio()`: actualmente devuelve `ok: false` aunque el registro se realice.
    - [ ] Implementar el evento del botón `btnRegistrarContrato` en `contratos.js` y enviar los datos al endpoint `registrarContrato`.
    - [ ] Completar el formulario con los campos que el controlador/modelo requieren: adelanto, método de pago y número de operación; revisar también Condiciones.
    - [ ] Revisar el flujo HTML: la vista actual no contiene un formulario que envíe los datos del contrato.
    - [ ] Cambiar la opción inicial de TipoDOI para que represente correctamente el valor vacío si el documento es opcional.
    - [ ] Corregir `validarDatos()`: inicializa `$errorres` pero usa `$errores`; también consulta `$cliente['NumeroDOI']` aunque el dato se llama `NumDOI`.
    - [ ] Invocar la validación del lado del servidor antes de llamar al modelo.
    - [ ] Revisar las validaciones de provincia/distrito y que el distrito pertenezca a la provincia seleccionada.
    - [ ] Probar que cliente, contrato y pago inicial se registren juntos o que todo se revierta si una parte falla.

    ## Prioridad media — desarrollar el módulo de Ventas

    - [ ] Definir el flujo funcional completo de venta antes de programar.
    - [ ] Confirmar cómo una venta se relaciona con un contrato (la tabla `Ventas` tiene `UNIQUE(idContrato)`: actualmente permite como máximo una venta por contrato).
    - [ ] Definir cómo se calcula `CantidadPedidos`, `PrecioTotal`, `PrecioUnitario`, `Descuento` y `SubTotal`.
    - [ ] Implementar validación de promociones y descuentos, respetando que cada detalle solo pueda tener uno de los dos.
    - [ ] Definir y programar el movimiento de stock al registrar, finalizar o cancelar una venta.
    - [ ] Definir cómo se relacionan los pagos parciales de `Pago` con el saldo del contrato/venta.
    - [ ] Crear rutas, controlador, modelo, vistas, JavaScript y CSS de Ventas.
    - [ ] Probar efectos de ventas y cancelaciones sobre stock, importes, pagos y estadísticas.

    ## Prioridad media — consistencia general

    - [ ] Corregir el nombre/ruta del CSS de Inventario: `inventario.css` frente a `Inventario.css`.
    - [ ] Revisar la conexión PDO para que un fallo de conexión se gestione sin devolver `null` donde se exige `PDO`.
    - [ ] Revisar generación de IDs basada en `SELECT MAX(...) + 1`: puede producir colisiones si hay registros simultáneos.
    - [ ] Añadir protección CSRF a formularios POST que todavía no la tengan.
    - [ ] Verificar validación y escape de datos en todas las vistas y endpoints.
    - [ ] Probar rutas, permisos, mensajes de error y redirecciones desde el navegador.

    ## Prioridad baja — módulos complementarios

    - [ ] Definir el contenido y las estadísticas del Dashboard.
    - [ ] Desarrollar el modelo del Dashboard (actualmente está vacío).
    - [ ] Definir qué opciones debe ofrecer Configuración.
    - [ ] Revisar y documentar promociones, descuentos y sus relaciones.
    - [ ] Limpiar archivos temporales/checkpoints si ya no se necesitan.

    ## Próxima tarea concreta

    Comenzar con las pruebas funcionales de Compras, sin reescribir el módulo. Registrar qué funciona y qué falla. Luego resolver los puntos de Contratos antes de depender de ellos para Ventas.
