-- Migración para MariaDB con el esquema anterior de BD_ISHUME.sql.
USE BD_ISHUME_1;

ALTER TABLE Ventas
    DROP CONSTRAINT IF EXISTS chk_venta_estado,
    ADD CONSTRAINT chk_venta_estado
        CHECK (Estado IN ('PENDIENTE', 'FINALIZADA', 'CANCELADA', 'ANULADA'));

START TRANSACTION;

UPDATE Ventas SET Estado = 'ANULADA' WHERE Estado = 'CANCELADA';

UPDATE Contratos c
INNER JOIN Ventas v ON v.idContrato = c.idContrato
SET c.Estado = 'CANCELADO'
WHERE v.Estado = 'ANULADA' AND c.Estado <> 'CANCELADO';

UPDATE Contratos c
INNER JOIN Ventas v ON v.idContrato = c.idContrato
SET c.Estado = 'FINALIZADO'
WHERE c.Estado = 'PROCESO' AND v.Estado IN ('PENDIENTE', 'FINALIZADA');

COMMIT;

ALTER TABLE Ventas
    DROP CONSTRAINT chk_venta_estado,
    ADD CONSTRAINT chk_venta_estado
        CHECK (Estado IN ('PENDIENTE', 'FINALIZADA', 'ANULADA'));
