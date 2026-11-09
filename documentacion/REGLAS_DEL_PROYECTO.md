# Reglas del proyecto ISHUME

## 1. Reglas técnicas

- Mantener la separación actual: rutas → controlador → modelo → vista.
- Usar PDO y consultas preparadas para valores recibidos del usuario.
- No colocar consultas SQL dentro de las vistas.
- No confiar en cálculos enviados por JavaScript: recalcular importes en PHP.
- Usar transacciones cuando una operación inserte/actualice varias tablas que deban mantenerse consistentes.
- No modificar archivos fuera del alcance solicitado.
- Antes de cambiar una regla de negocio, explicar qué otros módulos puede afectar.
- No inventar nombres de tablas, columnas, funciones, IDs o rutas.
- No guardar contraseñas, tokens ni credenciales en estos documentos.
- Probar cada cambio y anotar el resultado real.

## 2. Inventario y stock

- `modelos.Cantidad` representa el stock actual del modelo.
- Las bajas de inventario registran motivo y cantidad en `BajasInventario` y descuentan esa cantidad del stock.
- El listado principal de inventario muestra modelos activos (`Estado = true`).
- Las operaciones que descuenten stock deben impedir que la cantidad resulte negativa.
- Las compras aumentan stock; las ventas/entregas y anulaciones deben aplicar movimientos coherentes y trazables.

## 3. Compras

- Una compra pertenece a un proveedor.
- Una compra puede tener varios detalles/productos.
- El subtotal se calcula con cantidad × precio de compra en el servidor.
- Al registrar una compra, el stock aumenta según las cantidades de sus detalles.
- La anulación puede ser parcial o total, por líneas de detalle.
- Para anular, se requiere motivo y se debe verificar que el stock disponible permita revertir las unidades que ingresaron.
- La anulación debe conservar el importe exacto asociado a las líneas anuladas; no se debe perder la trazabilidad económica al recalcular totales.
- Los cambios de compra, detalle y stock deben ser atómicos mediante transacción.

## 4. Contratos y clientes

- Al registrar un contrato se crea un **cliente nuevo**; no se debe convertir el flujo en selección de un cliente existente sin una decisión explícita.
- El colegio se busca por nombre; si no existe, se permite registrar uno nuevo.
- La provincia determina los distritos disponibles.
- El contrato contempla evento, servicio, fecha del evento, dirección, provincia/distrito y condiciones.
- El registro de cliente, contrato y pago inicial debe ser consistente: si falla una parte, se revierte toda la operación.
- Los datos de adelanto y método de pago deben validarse antes de registrar el pago.

## 5. Ventas, promociones y descuentos

Reglas de negocio definidas previamente para orientar el desarrollo; confirmar si se actualizan antes de implementarlas:

- ISHUME vende cuadros y anuarios.
- En el combo de anuario + cuadro, el cuadro puede tener un precio especial de S/ 90 en lugar del precio individual de S/ 130, según la regla comercial acordada.
- Para pedidos con alumnos identificados, el combo se aplica a los alumnos que correspondan; puede haber cantidades sin nombres que se cobran como cuadro individual. Ejemplo definido: para 20 estudiantes, 17 combos y 3 cuadros individuales.
- Descuento por provincia acordado: Chincha S/ 15 y Pisco S/ 5.
- En cada detalle de venta se aplica una promoción **o** un descuento, nunca ambos a la vez. La tabla `DetalleVenta` ya incluye una restricción para impedir ambos simultáneamente.
- Las compras y los pagos simulados/registrados en el sistema son procesos separados. No asumir integración real con Yape, tarjetas u otra pasarela de pago.

## 6. Anulaciones y pagos

- Las anulaciones deben dejar motivo, fecha y relación con el registro original.
- Revertir una operación no significa borrar su historial.
- Antes de cambiar el estado de una compra, venta, contrato o pago, determinar el impacto en stock, total, saldo y reportes.
- No asumir que anular una compra equivale a registrar un pago real o un reembolso bancario: esa operación debe modelarse según la regla contable que se defina.

## 7. Forma de trabajo entre asistentes

- Al iniciar una sesión, leer `PROJECT_CONTEXT.md`, `PENDIENTES.md`, `REGLAS_DEL_PROYECTO.md` y los últimos cambios de `CHANGELOG.md`.
- Trabajar sobre la versión actual del repositorio, no sobre una copia antigua.
- No editar simultáneamente el mismo archivo en Claude y ChatGPT sin sincronizar.
- Explicar qué archivo se modificará, qué cambio se hará y qué impacto tendrá.
- Al terminar, actualizar `CHANGELOG.md` y `PENDIENTES.md`; actualizar el contexto o las reglas si cambió la arquitectura o una decisión.
- Diferenciar siempre entre código escrito, código probado y funcionalidad terminada.
