-- drop database BD_ISHUME_1;
create database BD_ISHUME_1;
use BD_ISHUME_1;

create table proveedores (
idProveedor char(8) primary key not null,
NombreProveedor varchar(50) not null,
RazonSocial varchar(100) not null,
Ruc char(11) not null,
Telefono varchar(20) null,
Correo varchar(100) null,
Estado boolean default true not null
);
-- proveedores 
INSERT INTO proveedores VALUES ('PRV-001', 'Líder Book', 'Corporación Imagen Pro S.A.C.', '20615645592', '','',true);
INSERT INTO proveedores VALUES ('PRV-002', 'Lalo', 'Arteco Innovation and Leadership S.A.C.', '20604566747', '(01) 428-1888','ventas@diplomasl...com.pe',true);
INSERT INTO proveedores VALUES ('PRV-003', 'Joselito', 'Impresiones y Arte Sociedad Anónima Cerrada', '20609451514', '960 541 693','diplomasjoselito@gmail.com',true);

create table categorias (
idCategoria char(7) primary key not null,
NombreCategoria varchar (50) not null
);
INSERT INTO categorias VALUES ('CAT-001','Anuarios');
INSERT INTO categorias VALUES ('CAT-002','Cuadros');

create table TipoMarco (
idTipoMarco char(7) primary key not null,
NombreMarco varchar(100)
);
INSERT INTO TipoMarco VALUES ('TIP-001','Bio Madera');
INSERT INTO TipoMarco VALUES ('TIP-002','Bio Madera 3D');
INSERT INTO TipoMarco VALUES ('TIP-003','Biogloss');
INSERT INTO TipoMarco VALUES ('TIP-004','Resinado');
INSERT INTO TipoMarco VALUES ('TIP-005','Brillante');
INSERT INTO TipoMarco VALUES ('TIP-006','Encajonado');
INSERT INTO TipoMarco VALUES ('TIP-007','Melamine');
INSERT INTO TipoMarco VALUES ('TIP-008','MDF');
INSERT INTO TipoMarco VALUES ('TIP-009','Holografiado');


CREATE TABLE Promociones (
    idPromocion CHAR(8) PRIMARY KEY NOT NULL,
    NombrePromo VARCHAR(100) NOT NULL,
    PrecioEspecial DECIMAL(10,2) NULL,
    Descripcion VARCHAR(500) NULL,
    TipoPromocion VARCHAR(50) NOT NULL,
    Estado BOOLEAN NOT NULL DEFAULT TRUE,
 
    CONSTRAINT chk_promocion_precioespecial CHECK (PrecioEspecial IS NULL OR PrecioEspecial >= 0)
);

CREATE TABLE MetodoPago (
    idMetodoPago CHAR(8) PRIMARY KEY NOT NULL,
    NombreMetodo VARCHAR(50) NOT NULL,
    Estado BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE Cliente (
    idCliente CHAR(8) PRIMARY KEY NOT NULL,
    Nombre VARCHAR(100) NOT NULL,
    Apellidos VARCHAR(100) NOT NULL,
    Testigo VARCHAR(100) NULL,
    Direccion VARCHAR(200) NOT NULL,
    TipoDOI VARCHAR(20) NULL,
    NumDOI VARCHAR(20) NULL,
    Telefono VARCHAR(20) NOT NULL,
    Correo VARCHAR(100) NULL
);

CREATE TABLE Colegios (
    idColegio CHAR(8) PRIMARY KEY NOT NULL,
    NombreColegio VARCHAR(150) NOT NULL
);

CREATE TABLE Provincias (
    idProvincia CHAR(8) PRIMARY KEY NOT NULL,
    NomProvincia VARCHAR(100) NOT NULL
);

INSERT INTO Provincias VALUES ('PRO-0001', 'Ica');
INSERT INTO Provincias VALUES ('PRO-0002', 'Chincha');
INSERT INTO Provincias VALUES ('PRO-0003', 'Pisco');


CREATE TABLE Distritos (
    idDistrito CHAR(8) PRIMARY KEY NOT NULL,
    idProvincia CHAR(8) NOT NULL,
    NombreDistrito VARCHAR(100) NOT NULL,
    CONSTRAINT fk_distrito_provincia FOREIGN KEY (idProvincia) REFERENCES Provincias(idProvincia)
);

INSERT INTO Distritos VALUES
-- Ica: 3 distritos
('DIS-0001', 'PRO-0001', 'Ica'),
('DIS-0002', 'PRO-0001', 'La Tinguiña'),
('DIS-0003', 'PRO-0001', 'Parcona'),

-- Chincha: 11 distritos
('DIS-0004', 'PRO-0002', 'Chincha Alta'),
('DIS-0005', 'PRO-0002', 'Alto Larán'),
('DIS-0006', 'PRO-0002', 'Chavín'),
('DIS-0007', 'PRO-0002', 'Chincha Baja'),
('DIS-0008', 'PRO-0002', 'El Carmen'),
('DIS-0009', 'PRO-0002', 'Grocio Prado'),
('DIS-0010', 'PRO-0002', 'Pueblo Nuevo'),
('DIS-0011', 'PRO-0002', 'San Juan de Yánac'),
('DIS-0012', 'PRO-0002', 'San Pedro de Huacarpana'),
('DIS-0013', 'PRO-0002', 'Sunampe'),
('DIS-0014', 'PRO-0002', 'Tambo de Mora'),

-- Pisco: 5 distritos
('DIS-0015', 'PRO-0003', 'Pisco'),
('DIS-0016', 'PRO-0003', 'Paracas'),
('DIS-0017', 'PRO-0003', 'San Andrés'),
('DIS-0018', 'PRO-0003', 'San Clemente'),
('DIS-0019', 'PRO-0003', 'Túpac Amaru Inca');

                                                                       
CREATE TABLE Descuentos (	
    idDescuento CHAR(8) PRIMARY KEY NOT NULL,
    idProvincia CHAR(8) NULL,
    Nombre VARCHAR(100) NOT NULL,
    TipoDescuento VARCHAR(50) NOT NULL,
    ValorDescuento DECIMAL(10,2) NOT NULL,
    CantidadMinima INT NULL,
    FechaInicio DATE NULL,
    FechaFin DATE NULL,
    Estado BOOLEAN NOT NULL DEFAULT TRUE,
 
    CONSTRAINT chk_descuento_valor CHECK (ValorDescuento >= 0),
    CONSTRAINT chk_descuento_cantidad CHECK (CantidadMinima IS NULL OR CantidadMinima > 0),
    CONSTRAINT fk_descuento_provincia FOREIGN KEY (idProvincia) REFERENCES Provincias(idProvincia)
);

CREATE TABLE Promocion_Detalle (
    idPromocionDetalle CHAR(8) PRIMARY KEY NOT NULL,
    idPromocion CHAR(8) NOT NULL,
    idDescuento CHAR(8) NOT NULL,

    CONSTRAINT fk_promodetalle_promocion FOREIGN KEY (idPromocion) REFERENCES Promociones(idPromocion),
    CONSTRAINT fk_promodetalle_descuento FOREIGN KEY (idDescuento) REFERENCES Descuentos(idDescuento)
);

create table modelos(
idModelo	char(7) primary key not null,
idCategoria char(7) not null,
idTipoMarco char(7) not null,
NombreMaterial varchar(100) not null,
Precio	decimal(10,2) not null,
Cantidad int not null,
TamañoHorizontal decimal(5,2) not null,
TamañoVertical decimal(5,2) not null,
Color varchar(100) not null,
NivelEducativo varchar (100) not null,
Imagen varchar(255) NULL DEFAULT NULL,
Estado boolean default true not null,


constraint fk_categoria foreign key (idCategoria) references categorias(idCategoria),
constraint fk_tipomarco foreign key (idTipoMarco) references TipoMarco(idTipoMarco)
);

INSERT INTO modelos VALUES ('MOD-001', 'CAT-002', 'TIP-001', 'Unicornio', 125.00, 0, 60.00, 50.00, 'Rosado', 'Inicial', 'img/modelo/extraidas/MOD-001.png', true);
INSERT INTO modelos VALUES ('MOD-002', 'CAT-002', 'TIP-001', 'Mi Principe', 125.00, 0, 60.00, 50.00, 'Azul', 'Inicial', 'img/modelo/extraidas/MOD-002.png', true);
INSERT INTO modelos VALUES ('MOD-003', 'CAT-002', 'TIP-001', 'Mi Dulce Princesa', 125.00, 0, 60.00, 50.00, 'Rosado', 'Inicial', 'img/modelo/extraidas/MOD-003.png', true);
INSERT INTO modelos VALUES ('MOD-004', 'CAT-002', 'TIP-001', 'Mi Principe', 125.00, 0, 60.00, 50.00, 'Azul', 'Inicial', 'img/modelo/extraidas/MOD-004.png', true);
INSERT INTO modelos VALUES ('MOD-005', 'CAT-002', 'TIP-001', 'Unicornio', 125.00, 0, 60.00, 50.00, 'Rosado', 'Inicial', 'img/modelo/extraidas/MOD-005.png', true);
INSERT INTO modelos VALUES ('MOD-006', 'CAT-002', 'TIP-001', 'Principes', 125.00, 0, 60.00, 50.00, 'Azul', 'Inicial', 'img/modelo/extraidas/MOD-006.png', true);
INSERT INTO modelos VALUES ('MOD-007', 'CAT-002', 'TIP-003', 'Ultrabrillante', 125.00, 0, 0.00, 0.00, 'Rosado', 'Inicial', 'img/modelo/extraidas/MOD-007.png', true);
INSERT INTO modelos VALUES ('MOD-008', 'CAT-002', 'TIP-003', 'Ultrabrillante', 130.00, 0, 0.00, 0.00, 'Azul', 'Inicial', 'img/modelo/extraidas/MOD-008.png', true);

INSERT INTO modelos VALUES ('MOD-009', 'CAT-002', 'TIP-004', 'Mi Principe', 130.00, 0, 65.00, 51.00, 'Rosado', 'Inicial', 'img/modelo/extraidas/MOD-009.png', true);
INSERT INTO modelos VALUES ('MOD-010', 'CAT-002', 'TIP-004', 'Pequeños Guerreros', 130.00, 0, 65.00, 51.00, 'Azul', 'Inicial', 'img/modelo/extraidas/MOD-010.png', true);
INSERT INTO modelos VALUES ('MOD-011', 'CAT-002', 'TIP-004', 'Mi Pequeña Astronauta', 130.00, 0, 65.00, 51.00, 'Rosado', 'Inicial', 'img/modelo/extraidas/MOD-011.png', true);
INSERT INTO modelos VALUES ('MOD-012', 'CAT-002', 'TIP-004', 'Mundo Labubu', 130.00, 0, 65.00, 51.00, 'Azul', 'Inicial', 'img/modelo/extraidas/MOD-012.png', true);
INSERT INTO modelos VALUES ('MOD-013', 'CAT-002', 'TIP-004', 'Aventureros del Saber', 130.00, 0, 65.00, 51.00, 'Marrón', 'Inicial', 'img/modelo/extraidas/MOD-013.png', true);
INSERT INTO modelos VALUES ('MOD-014', 'CAT-002', 'TIP-004', 'Estrellitas del Saber', 130.00, 0, 65.00, 51.00, 'Azul', 'Inicial', 'img/modelo/extraidas/MOD-014.png', true);
INSERT INTO modelos VALUES ('MOD-015', 'CAT-002', 'TIP-004', 'Mundo Labubu', 130.00, 0, 65.00, 51.00, 'Rosado', 'Inicial', 'img/modelo/extraidas/MOD-015.png', true);
INSERT INTO modelos VALUES ('MOD-016', 'CAT-002', 'TIP-004', 'Ositos Cariñositos', 130.00, 0, 65.00, 51.00, 'Verde', 'Inicial', 'img/modelo/extraidas/MOD-016.png', true);

INSERT INTO modelos VALUES ('MOD-017', 'CAT-002', 'TIP-004', 'Sonic Speed', 130.00, 0, 45.00, 30.00, 'Multicolor', 'Inicial', 'img/modelo/extraidas/MOD-017.png', true);
INSERT INTO modelos VALUES ('MOD-018', 'CAT-002', 'TIP-004', 'Mundo Arácnido', 130.00, 0, 45.00, 30.00, 'Multicolor', 'Inicial', 'img/modelo/extraidas/MOD-018.png', true);
INSERT INTO modelos VALUES ('MOD-019', 'CAT-002', 'TIP-004', 'Encanto Rosa', 130.00, 0, 45.00, 30.00, 'Rosado', 'Inicial', 'img/modelo/extraidas/MOD-019.png', true);
INSERT INTO modelos VALUES ('MOD-020', 'CAT-002', 'TIP-004', 'Mundo de Aprendizaje', 130.00, 0, 45.00, 30.00, 'Multicolor', 'Inicial', 'img/modelo/extraidas/MOD-020.png', true);
INSERT INTO modelos VALUES ('MOD-021', 'CAT-002', 'TIP-004', 'Vocales', 130.00, 0, 45.00, 30.00, 'Rosado', 'Inicial', 'img/modelo/extraidas/MOD-021.png', true);
INSERT INTO modelos VALUES ('MOD-022', 'CAT-002', 'TIP-004', 'Desvanecido', 130.00, 0, 45.00, 30.00, 'Morado', 'Inicial', 'img/modelo/extraidas/MOD-022.png', true);

INSERT INTO modelos VALUES ('MOD-023', 'CAT-002', 'TIP-004', 'Guerreros', 130.00, 0, 65.00, 51.00, 'Azul', 'Inicial', 'img/modelo/extraidas/MOD-023.png', true);
INSERT INTO modelos VALUES ('MOD-024', 'CAT-002', 'TIP-004', 'Sticht', 130.00, 0, 65.00, 51.00, 'Azul', 'Inicial', 'img/modelo/extraidas/MOD-024.png', true);
INSERT INTO modelos VALUES ('MOD-025', 'CAT-002', 'TIP-004', 'Pequeñas Aventuras', 130.00, 0, 65.00, 51.00, 'Rosado', 'Inicial', 'img/modelo/extraidas/MOD-025.png', true);
INSERT INTO modelos VALUES ('MOD-026', 'CAT-002', 'TIP-004', 'Mundo de Fantasias', 130.00, 0, 65.00, 51.00, 'Rosado', 'Inicial', 'img/modelo/extraidas/MOD-026.png', true);
INSERT INTO modelos VALUES ('MOD-027', 'CAT-002', 'TIP-004', 'Mi Primer Logro', 130.00, 0, 45.00, 30.00, 'Azul', 'Inicial', 'img/modelo/extraidas/MOD-027.png', true);

INSERT INTO modelos VALUES ('MOD-028', 'CAT-002', 'TIP-005', 'Cuadro Egresada', 135.00, 0, 69.50, 51.00, 'Rosado', 'Inicial', 'img/modelo/extraidas/MOD-028.png', true);
INSERT INTO modelos VALUES ('MOD-029', 'CAT-002', 'TIP-005', 'Cuadro Egresado', 135.00, 0, 69.50, 51.00, 'Azul', 'Inicial', 'img/modelo/extraidas/MOD-029.png', true);
INSERT INTO modelos VALUES ('MOD-030', 'CAT-002', 'TIP-005', 'Cuadro Lila', 135.00, 0, 69.50, 51.00, 'Lila', 'Inicial', 'img/modelo/extraidas/MOD-030.png', true);
INSERT INTO modelos VALUES ('MOD-031', 'CAT-002', 'TIP-005', 'Cuadro Azul', 135.00, 0, 69.50, 51.00, 'Azul', 'Inicial', 'img/modelo/extraidas/MOD-031.png', true);
INSERT INTO modelos VALUES ('MOD-032', 'CAT-002', 'TIP-005', 'Mip Princesa Kpop', 135.00, 0, 69.50, 51.00, 'Rosado', 'Inicial', 'img/modelo/extraidas/MOD-032.png', true);
INSERT INTO modelos VALUES ('MOD-033', 'CAT-002', 'TIP-005', 'Mi principe', 135.00, 0, 69.50, 51.00, 'Azul', 'Inicial', 'img/modelo/extraidas/MOD-033.png', true);

INSERT INTO modelos VALUES ('MOD-034', 'CAT-002', 'TIP-005', 'Cuadro Box Reflex', 135.00, 0, 0.00, 0.00, 'Rosado', 'Inicial', 'img/modelo/extraidas/MOD-034.png', true);
INSERT INTO modelos VALUES ('MOD-035', 'CAT-002', 'TIP-005', 'Cuadro Marmol Blue', 140.00, 0, 69.50, 50.00, 'Azul', 'Inicial', 'img/modelo/extraidas/MOD-035.png', true);
INSERT INTO modelos VALUES ('MOD-036', 'CAT-002', 'TIP-005', 'Cuadro Marmol Pink', 140.00, 0, 69.50, 50.00, 'Rosado', 'Inicial', 'img/modelo/extraidas/MOD-036.png', true);

INSERT INTO modelos VALUES ('MOD-037', 'CAT-002', 'TIP-006', 'Caballero Roblox', 140.00, 0, 0.00, 0.00, 'Azul', 'Inicial', 'img/modelo/extraidas/MOD-037.png', true);
INSERT INTO modelos VALUES ('MOD-038', 'CAT-002', 'TIP-006', 'Princesitas', 140.00, 0, 0.00, 0.00, 'Rosado', 'Inicial', 'img/modelo/extraidas/MOD-038.png', true);
INSERT INTO modelos VALUES ('MOD-039', 'CAT-002', 'TIP-006', 'Guerreras K-pop', 140.00, 0, 0.00, 0.00, 'Rosado', 'Inicial', 'img/modelo/extraidas/MOD-039.png', true);
INSERT INTO modelos VALUES ('MOD-040', 'CAT-002', 'TIP-006', 'Super Kids', 140.00, 0, 0.00, 0.00, 'Azul', 'Inicial', 'img/modelo/extraidas/MOD-040.png', true);

INSERT INTO modelos VALUES ('MOD-041', 'CAT-002', 'TIP-006', 'Cuadro Diagron', 140.00, 0, 69.50, 51.00, 'Rosado', 'Inicial', 'img/modelo/extraidas/MOD-041.png', true);
INSERT INTO modelos VALUES ('MOD-042', 'CAT-002', 'TIP-006', 'Cuadro Diagron', 140.00, 0, 69.50, 51.00, 'Azul', 'Inicial', 'img/modelo/extraidas/MOD-042.png', true);

INSERT INTO modelos VALUES ('MOD-043', 'CAT-002', 'TIP-006', 'Dúo Mármol Blue', 140.00, 0, 0.00, 0.00, 'Azul', 'Inicial', 'img/modelo/extraidas/MOD-043.png', true);
INSERT INTO modelos VALUES ('MOD-044', 'CAT-002', 'TIP-006', 'Dúo Mármol Pink', 140.00, 0, 0.00, 0.00, 'Rosado', 'Inicial', 'img/modelo/extraidas/MOD-044.png', true);


-- ==========================================
-- BOOKS - INICIAL
-- ==========================================
INSERT INTO modelos VALUES ('MOD-045', 'CAT-001', 'TIP-007', 'Cuadro Book', 160.00, 0, 29.70, 29.70, 'Rosado', 'Inicial', 'img/modelo/extraidas/MOD-045.png', true);
INSERT INTO modelos VALUES ('MOD-046', 'CAT-001', 'TIP-007', 'Cuadro Book', 160.00, 0, 29.70, 29.70, 'Azul', 'Inicial', 'img/modelo/extraidas/MOD-046.png', true);

INSERT INTO modelos VALUES ('MOD-047', 'CAT-001', 'TIP-007', 'Cuadro Book Giratorio', 160.00, 0, 0.00, 0.00, 'Rosado', 'Inicial', 'img/modelo/extraidas/MOD-047.png', true);
INSERT INTO modelos VALUES ('MOD-048', 'CAT-001', 'TIP-007', 'Cuadro Book Giratorio', 160.00, 0, 0.00, 0.00, 'Azul', 'Inicial', 'img/modelo/extraidas/MOD-048.png', true);

INSERT INTO modelos VALUES ('MOD-049', 'CAT-001', 'TIP-007', 'Book Secret Giratorio', 175.00, 0, 0.00, 0.00, 'Morado', 'Inicial', 'img/modelo/extraidas/MOD-049.png', true);
INSERT INTO modelos VALUES ('MOD-050', 'CAT-001', 'TIP-007', 'Book Secret Giratorio', 175.00, 0, 0.00, 0.00, 'Azul', 'Inicial', 'img/modelo/extraidas/MOD-050.png', true);

INSERT INTO modelos VALUES ('MOD-051', 'CAT-001', 'TIP-008', 'Book Diagon Premium', 155.00, 0, 25.00, 25.00, 'Rosado', 'Inicial', 'img/modelo/extraidas/MOD-051.png', true);
INSERT INTO modelos VALUES ('MOD-052', 'CAT-001', 'TIP-008', 'Book Diagon Premium', 155.00, 0, 25.00, 25.00, 'Azul', 'Inicial', 'img/modelo/extraidas/MOD-052.png', true);

INSERT INTO modelos VALUES ('MOD-053', 'CAT-001', 'TIP-009', 'Book White', 140.00, 0, 30.00, 25.00, 'Rosa', 'Inicial', 'img/modelo/extraidas/MOD-053.png', true);
INSERT INTO modelos VALUES ('MOD-054', 'CAT-001', 'TIP-009', 'Book White', 140.00, 0, 30.00, 25.00, 'Azul', 'Inicial', 'img/modelo/extraidas/MOD-054.png', true);

INSERT INTO modelos VALUES ('MOD-055', 'CAT-001', 'TIP-009', 'Book Premium', 160.00, 0, 30.00, 20.00, 'Rosa', 'Inicial', 'img/modelo/extraidas/MOD-055.png', true);
INSERT INTO modelos VALUES ('MOD-056', 'CAT-001', 'TIP-009', 'Book Premium', 160.00, 0, 30.00, 20.00, 'Azul', 'Inicial', 'img/modelo/extraidas/MOD-056.png', true);


-- ==========================================
-- PRIMARIA Y SECUNDARIA
-- ==========================================
INSERT INTO modelos VALUES ('MOD-057', 'CAT-002', 'TIP-002', 'Maravillas Grad', 125.00, 0, 0.00, 0.00, 'Marrón', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-057.png', true);
INSERT INTO modelos VALUES ('MOD-058', 'CAT-002', 'TIP-001', 'Mi Promo', 125.00, 0, 0.00, 0.00, 'Multicolor', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-058.png', true);
INSERT INTO modelos VALUES ('MOD-059', 'CAT-002', 'TIP-001', 'Mi Promo', 125.00, 0, 0.00, 0.00, 'Blanco', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-059.png', true);

INSERT INTO modelos VALUES ('MOD-060', 'CAT-002', 'TIP-003', 'Ultrabrillante', 130.00, 0, 0.00, 0.00, 'Multicolor', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-060.png', true);

INSERT INTO modelos VALUES ('MOD-061', 'CAT-002', 'TIP-004', 'Mi Promocion', 130.00, 0, 68.00, 54.00, 'Negro', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-061.png', true);
INSERT INTO modelos VALUES ('MOD-062', 'CAT-002', 'TIP-004', 'Mi Promocion', 130.00, 0, 68.00, 54.00, 'Negro', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-062.png', true);
INSERT INTO modelos VALUES ('MOD-063', 'CAT-002', 'TIP-004', 'Universitario', 130.00, 0, 68.00, 54.00, 'Azul', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-063.png', true);
INSERT INTO modelos VALUES ('MOD-064', 'CAT-002', 'TIP-004', 'Class Off', 130.00, 0, 68.00, 54.00, 'Rojo', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-064.png', true);
INSERT INTO modelos VALUES ('MOD-065', 'CAT-002', 'TIP-004', 'Maravillas del Mundo', 130.00, 0, 68.00, 54.00, 'Azul', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-065.png', true);
INSERT INTO modelos VALUES ('MOD-066', 'CAT-002', 'TIP-004', 'Graduacion', 130.00, 0, 68.00, 54.00, 'Negro', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-066.png', true);
INSERT INTO modelos VALUES ('MOD-067', 'CAT-002', 'TIP-004', 'Class of Black', 130.00, 0, 68.00, 54.00, 'Negro', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-067.png', true);

INSERT INTO modelos VALUES ('MOD-068', 'CAT-002', 'TIP-005', 'Dorado', 135.00, 0, 67.00, 52.00, 'Dorado', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-068.png', true);
INSERT INTO modelos VALUES ('MOD-069', 'CAT-002', 'TIP-005', 'Azul Oscuro', 135.00, 0, 67.00, 52.00, 'Azul Oscuro', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-069.png', true);
INSERT INTO modelos VALUES ('MOD-070', 'CAT-002', 'TIP-005', 'Dorado Claro', 135.00, 0, 67.00, 52.00, 'Dorado Claro', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-070.png', true);
INSERT INTO modelos VALUES ('MOD-071', 'CAT-002', 'TIP-005', 'Marron Madera', 135.00, 0, 67.00, 52.00, 'Marrón', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-071.png', true);
INSERT INTO modelos VALUES ('MOD-072', 'CAT-002', 'TIP-005', 'White Marmol', 135.00, 0, 67.00, 52.00, 'Blanco', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-072.png', true);
INSERT INTO modelos VALUES ('MOD-073', 'CAT-002', 'TIP-005', 'Black Marmol', 135.00, 0, 67.00, 52.00, 'Negro', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-073.png', true);

INSERT INTO modelos VALUES ('MOD-074', 'CAT-002', 'TIP-006', 'Cuadro Box Reflex White y Black', 135.00, 0, 0.00, 0.00, 'Blanco y Negro', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-074.png', true);
INSERT INTO modelos VALUES ('MOD-075', 'CAT-002', 'TIP-006', 'Encajonado Blue', 140.00, 0, 67.00, 52.00, 'Azul', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-075.png', true);
INSERT INTO modelos VALUES ('MOD-076', 'CAT-002', 'TIP-006', 'Encajonado Black', 140.00, 0, 67.00, 52.00, 'Negro', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-076.png', true);
INSERT INTO modelos VALUES ('MOD-077', 'CAT-002', 'TIP-006', 'Encajonado White', 140.00, 0, 67.00, 52.00, 'Blanco', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-077.png', true);

INSERT INTO modelos VALUES ('MOD-078', 'CAT-002', 'TIP-006', 'Cuadro Diagon', 140.00, 0, 67.00, 52.00, 'Rosado', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-078.png', true);
INSERT INTO modelos VALUES ('MOD-079', 'CAT-002', 'TIP-006', 'Cuadro Diagon', 140.00, 0, 67.00, 52.00, 'Verde', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-079.png', true);
INSERT INTO modelos VALUES ('MOD-080', 'CAT-002', 'TIP-006', 'Cuadro Diagon', 140.00, 0, 67.00, 52.00, 'Negro', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-080.png', true);

INSERT INTO modelos VALUES ('MOD-081', 'CAT-002', 'TIP-006', 'Black Conmemorativo', 140.00, 0, 0.00, 0.00, 'Negro', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-081.png', true);
INSERT INTO modelos VALUES ('MOD-082', 'CAT-002', 'TIP-006', 'White Conmemorativo', 140.00, 0, 0.00, 0.00, 'Blanco', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-082.png', true);

INSERT INTO modelos VALUES ('MOD-083', 'CAT-002', 'TIP-006', 'Duo Marmol White', 140.00, 0, 0.00, 0.00, 'Blanco', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-083.png', true);
INSERT INTO modelos VALUES ('MOD-084', 'CAT-002', 'TIP-006', 'Duo Marmol Black', 140.00, 0, 0, 0, 'Negro', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-084.png', true);

INSERT INTO modelos VALUES ('MOD-085', 'CAT-002', 'TIP-006', 'Me Gradue Encajonado', 140.00, 0, 69.50, 51.00, 'Blanco', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-085.png', true);
-- ==========================================
-- BOOKS - PRIMARIA Y SECUNDARIA
-- ==========================================
INSERT INTO modelos VALUES ('MOD-086', 'CAT-001', 'TIP-009', 'Book Premium', 150.00, 0, 30.00, 20.00, 'Blanco', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-086.png', true);
INSERT INTO modelos VALUES ('MOD-087', 'CAT-001', 'TIP-008', 'Book Premium Promo 3D', 150.00, 0, 25.00, 25.00, 'Marrón', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-087.png', true);
INSERT INTO modelos VALUES ('MOD-088', 'CAT-001', 'TIP-009', 'Book Black', 140.00, 0, 30.00, 25.00, 'Negro', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-088.png', true);
INSERT INTO modelos VALUES ('MOD-089', 'CAT-001', 'TIP-009', 'Book White', 140.00, 0, 30.00, 25.00, 'Blanco', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-089.png', true);
INSERT INTO modelos VALUES ('MOD-090', 'CAT-001', 'TIP-007', 'Giratorio Book Giratorio', 160.00, 0, 33.00, 29.00, 'Negro', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-090.png', true);
INSERT INTO modelos VALUES ('MOD-091', 'CAT-001', 'TIP-007', 'Cuadro Book Secret', 160.00, 0, 29.70, 29.70, 'Marrón', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-091.png', true);
INSERT INTO modelos VALUES ('MOD-092', 'CAT-001', 'TIP-007', 'Giratorio Secret', 175.00, 0, 30.00, 20.00, 'Negro', 'Primaria y Secundaria', 'img/modelo/extraidas_primaria/MOD-PS-092.png', true);
																																
CREATE TABLE BajasInventario (
    idBaja CHAR(8) PRIMARY KEY NOT NULL,
    idModelo CHAR(7) NOT NULL,
    Cantidad INT NOT NULL,
    Motivo VARCHAR(500) NOT NULL,
    FechaBaja DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_bajainventario_modelo FOREIGN KEY (idModelo) REFERENCES modelos(idModelo),
    CONSTRAINT chk_bajainventario_cantidad CHECK (Cantidad > 0)
);

CREATE TABLE Compras (
    idCompra CHAR(8) PRIMARY KEY NOT NULL,
    idProveedor CHAR(8) NOT NULL,
    FechaCompra DATETIME NOT NULL,
    TotalCompra DECIMAL(10,2) NOT NULL,
    Observacion VARCHAR(500) NULL,
    Estado BOOLEAN NOT NULL DEFAULT TRUE,

CONSTRAINT fk_compra_proveedor FOREIGN KEY (idProveedor) REFERENCES proveedores(idProveedor)
);
 
CREATE TABLE Contratos (
		idContrato CHAR(8) PRIMARY KEY NOT NULL,
		idCliente CHAR(8) NOT NULL,
		idColegio CHAR(8) NOT NULL,
		idDistrito CHAR(8) NOT NULL,
		Evento VARCHAR(150) NOT NULL,
		Servicio VARCHAR(150) NOT NULL,
		Direccion VARCHAR(200) NOT NULL,
		FechaContrato DATETIME NOT NULL,
		FechaEvento DATE NOT NULL,
		Condiciones TEXT NULL,
		Estado varchar (20) NOT NULL DEFAULT 'PROCESO',
		
	CONSTRAINT chk_contrato_estado CHECK (Estado IN ('PROCESO', 'FINALIZADO', 'CANCELADO')),
	CONSTRAINT fk_contrato_cliente FOREIGN KEY (idCliente) REFERENCES Cliente(idCliente),
	CONSTRAINT fk_contrato_colegio FOREIGN KEY (idColegio) REFERENCES Colegios(idColegio),
	CONSTRAINT fk_contrato_distritos FOREIGN KEY (idDistrito) REFERENCES Distritos(idDistrito)
	);

CREATE TABLE Ventas (
    idVenta CHAR(8) PRIMARY KEY NOT NULL,
    idContrato CHAR(8) NOT NULL,
    CantidadPedidos INT NOT NULL,
    FechaEntrega DATETIME NULL,
	FechaLimitePago DATE NULL,
    PrecioTotal DECIMAL(10,2) NOT NULL,
    Estado VARCHAR(20) NOT NULL DEFAULT 'PENDIENTE',
 
CONSTRAINT chk_venta_estado CHECK (Estado IN ('PENDIENTE', 'FINALIZADA', 'CANCELADA')),
CONSTRAINT fk_venta_contrato FOREIGN KEY (idContrato) REFERENCES Contratos(idContrato),
CONSTRAINT uq_venta_contrato UNIQUE (idContrato),
CONSTRAINT chk_venta_cantidad CHECK (CantidadPedidos > 0),
CONSTRAINT chk_venta_precio CHECK (PrecioTotal >= 0)

);

CREATE TABLE Pago (
    idPago CHAR(8) PRIMARY KEY NOT NULL,
    idContrato CHAR(8) NOT NULL,
    idMetodoPago CHAR(8) NOT NULL,
    Monto DECIMAL(10,2) NOT NULL,
    FechaPago DATETIME NOT NULL,
    NumOperacion VARCHAR(50) NULL,
    Observacion VARCHAR(500) NULL,
    Estado BOOLEAN NOT NULL DEFAULT TRUE,
 
CONSTRAINT fk_pago_contrato FOREIGN KEY (idContrato) REFERENCES Contratos(idContrato),
CONSTRAINT fk_pago_metodopago FOREIGN KEY (idMetodoPago) REFERENCES MetodoPago(idMetodoPago),
CONSTRAINT chk_pago_monto CHECK (Monto > 0)
);

CREATE TABLE DetalleVenta (
    idDetalleVenta CHAR(8) PRIMARY KEY NOT NULL,
    idVenta CHAR(8) NOT NULL,
    idModelo CHAR(7) NOT NULL,
    idDescuento CHAR(8) NULL,
    idPromocion CHAR(8) NULL,
    Cantidad INT NOT NULL,
    PrecioUnitario DECIMAL(10,2) NOT NULL,
    Descuento DECIMAL(10,2) NOT NULL DEFAULT 0,
    SubTotal DECIMAL(10,2) NOT NULL,
 
    CONSTRAINT fk_detalleventa_venta FOREIGN KEY (idVenta) REFERENCES Ventas(idVenta),
    CONSTRAINT fk_detalleventa_modelo FOREIGN KEY (idModelo) REFERENCES modelos(idModelo),
    CONSTRAINT fk_detalleventa_descuento FOREIGN KEY (idDescuento) REFERENCES Descuentos(idDescuento),
    CONSTRAINT fk_detalleventa_promocion FOREIGN KEY (idPromocion) REFERENCES Promociones(idPromocion),
 
    CONSTRAINT chk_detalleventa_cantidad CHECK (Cantidad > 0),
    CONSTRAINT chk_detalleventa_precio CHECK (PrecioUnitario >= 0),
    CONSTRAINT chk_detalleventa_descuento CHECK (Descuento >= 0),
    CONSTRAINT chk_detalleventa_subtotal CHECK (SubTotal >= 0),
    -- una linea recibe promocion O descuento, nunca ambos
    CONSTRAINT chk_detalleventa_un_beneficio CHECK (idPromocion IS NULL OR idDescuento IS NULL)
);
 
CREATE TABLE DetalleCompra (
    idDetalleCompra CHAR(8) PRIMARY KEY NOT NULL,
    idCompra CHAR(8) NOT NULL,
    idModelo CHAR(7) NOT NULL,
    Cantidad INT NOT NULL,
    PrecioCompra DECIMAL(10,2) NOT NULL,
    SubTotal DECIMAL(10,2) NOT NULL,
	Estado BOOLEAN NOT NULL DEFAULT TRUE,
 
    CONSTRAINT fk_detallecompra_compra FOREIGN KEY (idCompra) REFERENCES Compras(idCompra),
    CONSTRAINT fk_detallecompra_modelo FOREIGN KEY (idModelo) REFERENCES modelos(idModelo),
    CONSTRAINT chk_detallecompra_cantidad CHECK (Cantidad > 0),
    CONSTRAINT chk_detallecompra_precio CHECK (PrecioCompra >= 0),
    CONSTRAINT chk_detallecompra_subtotal CHECK (SubTotal >= 0)
);


CREATE TABLE AnularCompras (
    idAnulacion CHAR(8) PRIMARY KEY NOT NULL,
    idCompra char(8) NOT NULL,
    idDetalleCompra CHAR(8) NULL,
    FechaAnulacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    Motivo VARCHAR(500) NOT NULL,
    
    CONSTRAINT fk_anularcompras_detalle FOREIGN KEY (idDetalleCompra) REFERENCES DetalleCompra(idDetalleCompra),
    CONSTRAINT fk_anularcompras_compra FOREIGN KEY (idCompra) REFERENCES Compras(idCompra)
 
);
