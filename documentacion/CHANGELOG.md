# Registro de cambios

## 2026-11-17 — Desglose mensual de cobros

- El indicador mensual suma los abonos registrados por fecha de pago. Al desplegarlo, muestra por separado los cobros asociados a ventas vigentes y a ventas anuladas.

## 2026-11-17 — Tablas de pagos del panel

- Vencimientos del mes y Últimos pagos registrados muestran el colegio enlazado al contrato, con el código debajo. Los importes se identifican como saldo pendiente o monto pagado según la tabla.

## 2026-11-17 — Contratos antiguos vinculados a ventas

- El listado usa la venta vinculada para ubicar los contratos antiguos cuyo estado todavía figura como `PROCESO`. La migración de estados incluye su sincronización para futuras ejecuciones.

## 2026-11-17 — Paneles de anulación y cancelación

- Ventas solicita y conserva un motivo de anulación. Contratos permite cancelar los registros en proceso que todavía no tienen venta y abre la sección «Cancelados».
- Ambos paneles se centran en el área de contenido, a la derecha del menú en escritorio y debajo de la barra en móvil.

## 2026-11-17 — Acceso e impresión de contratos

- El enlace del colegio en Próximos eventos y el botón de ver contrato usan estilos naranjas.
- El detalle incluye un botón para abrir la impresión del navegador.

## 2026-11-17 — Listado de contratos y próximos eventos

- El colegio encabeza el listado de contratos y el código aparece debajo del cliente.
- Próximos eventos muestra desde hoy hasta dentro de 30 días, incluso al cambiar de mes. Cada colegio enlaza al detalle del contrato.

## 2026-11-17 — Orden del listado de ventas

- Las ventas finalizadas se muestran primero, las pendientes después y las anuladas al final. Dentro de cada grupo se conserva el orden descendente por fecha de contrato.

## 2026-11-17 — Anulación de venta y contrato asociado

- Una venta por contrato. Al anular la venta, el contrato pasa a `CANCELADO` en la misma transacción y se conservan los pagos históricos.
- El listado agrupa los contratos por estado e incluye la opción «Cancelados».
- El esquema nuevo admite `ANULADA`. Para una instalación existente de MariaDB, se incluye `migrar_anulacion_ventas.sql`.

## 2026-11-17 — Revisión estática del repositorio

- PHP 8.2 validó 32 archivos y Node validó la sintaxis de 7 scripts. Las dependencias instaladas cumplen los requisitos de plataforma de Composer.
- Se detectaron un error SQL en la anulación de compras, una incompatibilidad de estados en la anulación de ventas y un registro de depuración que guarda datos de contratos.
- Ventas ya cuenta con rutas, controlador, modelo y vistas. Contratos ya envía el formulario por JavaScript y valida los datos del cliente en el servidor.
- Se revisaron los flujos por lectura del código. Las operaciones contra MySQL y la interacción en navegador quedan pendientes de verificación. Solo se actualizó la documentación.

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
