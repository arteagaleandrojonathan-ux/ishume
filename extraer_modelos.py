import fitz
import cv2
import numpy as np
import os


# ============================================================
# CONFIGURACIÓN
# ============================================================

PDF = "public/img/modelo/CATALOGO PRONOEI - INICIAL.pdf"

CARPETA_SALIDA = "public/img/modelo/extraidas"

ZOOM = 2.5

# Comenzamos desde la página 4
PAGINA_INICIAL = 4

# Qué tan parecidas tienen que ser dos imágenes
# para considerarlas duplicadas.
#
# 0 = idénticas
# Valores más altos = permite pequeñas diferencias.
UMBRAL_DUPLICADO = 8


# ============================================================
# CREAR CARPETA
# ============================================================

os.makedirs(
    CARPETA_SALIDA,
    exist_ok=True
)


# ============================================================
# FUNCIÓN PARA CREAR UNA HUELLA DE LA IMAGEN
# ============================================================

def huella_imagen(imagen):

    # Convertir a gris
    gris = cv2.cvtColor(
        imagen,
        cv2.COLOR_BGR2GRAY
    )

    # Reducir la imagen a un tamaño pequeño
    reducido = cv2.resize(
        gris,
        (32, 32)
    )

    # Calcular promedio
    promedio = reducido.mean()

    # Crear una huella binaria
    huella = reducido > promedio

    return huella.flatten()


# ============================================================
# FUNCIÓN PARA COMPARAR DOS IMÁGENES
# ============================================================

def distancia_huellas(huella1, huella2):

    # Contar cuántos píxeles son diferentes
    diferencia = np.count_nonzero(
        huella1 != huella2
    )

    return diferencia


# ============================================================
# ABRIR PDF
# ============================================================

print("========================================")
print("ABRIENDO CATÁLOGO")
print("========================================")

doc = fitz.open(PDF)

print(f"Total de páginas: {len(doc)}")
print(f"Comenzando desde página: {PAGINA_INICIAL}")


# ============================================================
# LISTA DE IMÁGENES YA GUARDADAS
# ============================================================

huellas_guardadas = []


contador_modelo = 1


# ============================================================
# RECORRER TODAS LAS PÁGINAS
# ============================================================

for numero_pagina in range(
    PAGINA_INICIAL - 1,
    len(doc)
):

    print(
        f"\n----------------------------------------"
    )

    print(
        f"Analizando página {numero_pagina + 1}..."
    )


    pagina = doc[numero_pagina]


    # ========================================================
    # CONVERTIR PÁGINA PDF EN IMAGEN
    # ========================================================

    matriz = fitz.Matrix(
        ZOOM,
        ZOOM
    )

    pix = pagina.get_pixmap(
        matrix=matriz,
        alpha=False
    )


    img = np.frombuffer(
        pix.samples,
        dtype=np.uint8
    )

    img = img.reshape(
        pix.height,
        pix.width,
        pix.n
    )


    # RGB → BGR
    if pix.n == 4:

        img = cv2.cvtColor(
            img,
            cv2.COLOR_RGBA2BGR
        )

    else:

        img = cv2.cvtColor(
            img,
            cv2.COLOR_RGB2BGR
        )


    alto, ancho = img.shape[:2]


    # ========================================================
    # ZONA DONDE ESTÁ EL CUADRO
    # ========================================================

    x1 = int(ancho * 0.10)
    x2 = int(ancho * 0.90)

    y1 = int(alto * 0.28)
    y2 = int(alto * 0.75)


    zona = img[
        y1:y2,
        x1:x2
    ].copy()


    # ========================================================
    # DETECTAR EL CUADRO
    # ========================================================

    zona_suave = cv2.GaussianBlur(
        zona,
        (5, 5),
        0
    )


    gris = cv2.cvtColor(
        zona_suave,
        cv2.COLOR_BGR2GRAY
    )


    mascara = cv2.threshold(
        gris,
        245,
        255,
        cv2.THRESH_BINARY_INV
    )[1]


    # Limpiar ruido
    kernel = np.ones(
        (7, 7),
        np.uint8
    )


    mascara = cv2.morphologyEx(
        mascara,
        cv2.MORPH_CLOSE,
        kernel
    )


    mascara = cv2.morphologyEx(
        mascara,
        cv2.MORPH_OPEN,
        kernel
    )


    # ========================================================
    # BUSCAR CONTORNOS
    # ========================================================

    contornos, _ = cv2.findContours(
        mascara,
        cv2.RETR_EXTERNAL,
        cv2.CHAIN_APPROX_SIMPLE
    )


    if not contornos:

        print(
            "⚠ No se encontró ningún cuadro."
        )

        continue


    area_total = (
        zona.shape[0] *
        zona.shape[1]
    )


    contornos_validos = []


    for contorno in contornos:

        area = cv2.contourArea(
            contorno
        )

        if area > area_total * 0.03:

            contornos_validos.append(
                contorno
            )


    if not contornos_validos:

        print(
            "⚠ No se encontró un cuadro válido."
        )

        continue


    # ========================================================
    # TOMAR EL CONTORNO MÁS GRANDE
    # ========================================================

    contorno_principal = max(
        contornos_validos,
        key=cv2.contourArea
    )


    x, y, w, h = cv2.boundingRect(
        contorno_principal
    )


    # ========================================================
    # MARGEN
    # ========================================================

    margen = 10


    x = max(
        0,
        x - margen
    )

    y = max(
        0,
        y - margen
    )


    w = min(
        zona.shape[1] - x,
        w + margen * 2
    )


    h = min(
        zona.shape[0] - y,
        h + margen * 2
    )


    # ========================================================
    # RECORTE FINAL
    # ========================================================

    cuadro = zona[
        y:y+h,
        x:x+w
    ].copy()


    # ========================================================
    # CREAR HUELLA
    # ========================================================

    huella_actual = huella_imagen(
        cuadro
    )


    # ========================================================
    # COMPROBAR SI YA EXISTE
    # ========================================================

    duplicado = False


    for huella_anterior in huellas_guardadas:

        distancia = distancia_huellas(
            huella_actual,
            huella_anterior
        )


        if distancia <= UMBRAL_DUPLICADO:

            duplicado = True

            print(
                "⚠ Este cuadro ya apareció anteriormente."
            )

            print(
                f"→ Se salta la página {numero_pagina + 1}"
            )

            break


    # ========================================================
    # SI ES DUPLICADO → NO GUARDAR
    # ========================================================

    if duplicado:

        continue


    # ========================================================
    # GUARDAR NUEVO MODELO
    # ========================================================

    nombre = (
        f"MOD-{contador_modelo:03d}.png"
    )


    ruta = os.path.join(
        CARPETA_SALIDA,
        nombre
    )


    cv2.imwrite(
        ruta,
        cuadro
    )


    # Guardar su huella
    huellas_guardadas.append(
        huella_actual
    )


    print(
        f"✓ NUEVO CUADRO → {nombre}"
    )


    contador_modelo += 1


# ============================================================
# FINAL
# ============================================================

doc.close()


print("\n")
print("========================================")
print("EXTRACCIÓN TERMINADA")
print("========================================")
print(
    f"Cuadros únicos encontrados: "
    f"{contador_modelo - 1}"
)
print(
    f"Carpeta: {CARPETA_SALIDA}"
)
print("========================================")