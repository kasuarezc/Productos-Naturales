# Guía paso a paso — Portal II Congreso Colombiano de Productos Naturales

## Paso 1. Instalar el proyecto en XAMPP
1. Instala XAMPP (PHP 8.0 o superior) desde https://www.apachefriends.org
2. Descomprime `congreso.zip` dentro de `C:\xampp\htdocs\` → queda `C:\xampp\htdocs\congreso\`
   (en macOS: `/Applications/XAMPP/htdocs/congreso/`).
3. Abre el **XAMPP Control Panel** y pulsa **Start** en **Apache** (MySQL no es necesario).
4. Abre en el navegador: **http://localhost/congreso/**

> Si usas otro nombre de carpeta, edita la línea `ErrorDocument 404 /congreso/404.php` del archivo `.htaccess`.

## Paso 2. Conocer la estructura
```
congreso/
├── index.php, noticias.php, noticia.php, comites.php, resumenes.php,
│   escuela.php, sede.php, contacto.php, 404.php      ← páginas
├── includes/
│   ├── config.php      ← ⭐ TODO lo editable: enlaces, redes, correo, fechas, menú
│   ├── functions.php   ← utilidades
│   ├── icons.php       ← iconos SVG animados (química)
│   ├── header.php / footer.php   ← estándar para todo el sitio
│   └── components/     ← logo animado, tarjeta de persona, tarjeta de noticia, encabezado
├── data/               ← ⭐ CONTENIDO en JSON (personas, noticias, patrocinadores, aliados, fechas…)
├── api/contacto.php    ← recibe el formulario de contacto
├── assets/
│   ├── css/ (variables.css = paleta) · js/ · fonts/ · docs/plantilla-resumen.docx
│   └── img/ brand/ hero/ personas/ patrocinadores/ aliados/ sede/ noticias/
├── storage/            ← respaldo CSV de los mensajes (protegido)
└── docs/               ← esta guía + Google Apps Script
```

## Paso 3. Reemplazar imágenes pendientes
| Qué | Dónde guardarla | Nota |
|---|---|---|
| Fondo del hero | `assets/img/hero/brand-hero.png` | Si existe, reemplaza automáticamente al provisional `brand-hero.jpg`. Ideal 1920×1080. |
| Logo Universidad de la Amazonia | `assets/img/brand/uniamazonia.svg` | Mientras no exista, se muestra un rótulo de texto. |
| Fotos faltantes de comités | `assets/img/personas/galeano.jpg`, `silva.jpg`, `nerio.jpg`, `cuellar.jpg` | Cuadradas, 600×600 px. |
| 2 entidades aliadas faltantes | `assets/img/aliados/aliado-4.png`, `aliado-5.png` | Y cambia el nombre/URL en `data/aliados.json`. |
| Lugares de Florencia sin foto | `assets/img/sede/diosa-chaira.jpg`, `ciencia-hombre-manigua.jpg`, `museo-etnografico.jpg`, `jardin-botanico.jpg` | |

Cualquier imagen que falte muestra automáticamente un recuadro **"Imagen pendiente"**.

## Paso 4. Completar enlaces y datos pendientes (`includes/config.php`)
- `$LINKS['inscripcion']` → formulario de inscripción
- `$LINKS['envio_resumenes']` → enlace para envío de resúmenes
- `$LINKS['inscripcion_escuela']` → inscripción a cursos de la Escuela
- `$REDES[...]` → Facebook, Instagram, YouTube, X, LinkedIn, WhatsApp (vacío = el botón muestra "pendiente")
- `$SITE['telefono']` → opcional
- Fechas de resúmenes → `data/fechas.json` (campos `fecha` y `fecha_texto`)

## Paso 5. Agregar o editar contenido (sin tocar código)
- **Nueva persona**: copia un bloque en `data/personas.json`. `grupo` puede ser: `internacional`, `nacional`, `sequiamaz`, `organizador`, `editorial`, `cientifico`. La tarjeta, el filtro y el modal se generan solos.
- **Nueva noticia**: agrega un bloque en `data/noticias.json` con un `slug` único (sin espacios ni tildes). Aparece en el carrusel y en Noticias.
- **Patrocinador**: `data/patrocinadores.json` + logo en `assets/img/patrocinadores/`.
- Valida el JSON en https://jsonlint.com si algo deja de mostrarse (una coma de más rompe el archivo).


## Agregar un nuevo conferencista (o integrante de comité)

**1. Prepara la foto**
- Formato **JPG**, cuadrada (recomendado **600 × 600 px**), rostro centrado, peso menor a 200 KB
  (puedes recortarla y comprimirla gratis en https://squoosh.app).
- Nombre en minúsculas, sin tildes ni espacios: `apellido.jpg` → ej. `rodriguez.jpg`
- Guárdala en: `assets/img/personas/`

**2. Agrega sus datos en `data/personas.json`**
Copia este bloque y pégalo antes del último `]`, separado del anterior por una **coma**:
```json
  {
    "id": "nombre-apellido",
    "titulo": "Dr.",
    "nombre": "Nombre Completo Apellidos",
    "grupo": "internacional",
    "institucion": "Universidad, País",
    "nacionalidad": "País",
    "perfil": "Formación académica en una o dos frases.",
    "biografia": [
      "Primer párrafo de la biografía.",
      "Segundo párrafo (opcional)."
    ],
    "ponencia": "Título de la conferencia",
    "foto": "img/personas/rodriguez.jpg",
    "orcid": "https://orcid.org/0000-0000-0000-0000",
    "scholar": "",
    "enlaces": [
      { "label": "Perfil institucional", "url": "https://..." }
    ]
  }
```
| Campo | Qué poner |
|---|---|
| `id` | Único, minúsculas y guiones (se usa internamente) |
| `grupo` | `internacional` (tarjeta grande destacada), `nacional`, `sequiamaz`, `organizador`, `editorial` o `cientifico` |
| `titulo` | `Dr.`, `Dra.`, `MSc.` o vacío `""` |
| `orcid` / `scholar` | Enlace completo; si no tiene, deja `""` y el botón no aparece |
| `ponencia` | Si la tiene, aparece en su tarjeta y en el Programa plenario |
| `foto` | Si aún no tienes la foto, deja la ruta: se mostrarán sus iniciales con "Foto pendiente" |

**3. Guarda y recarga** `http://localhost/congreso/comites.php`. La persona aparece automáticamente en su sección,
en el programa plenario, en las cifras superiores y con su biografía en la ventana emergente.
El **orden** en la página es el mismo orden del archivo JSON.

> Si la página deja de mostrar personas, casi siempre es una coma faltante o sobrante:
> pega el contenido del archivo en https://jsonlint.com para encontrar el error.

## Cambiar o agregar fechas clave
Edita `data/fechas.json`. Cada fecha alimenta automáticamente: la línea de tiempo de **Resúmenes y Fechas**,
la cuenta regresiva del "próximo hito", el pie de página y el bloque de resúmenes del Inicio.
El estado (Próximamente / Convocatoria abierta / Finalizado) se calcula solo según la fecha del día.
Las **dos primeras** fechas deben ser siempre la apertura y el cierre de resúmenes.

## Paso 6. Activar el formulario de contacto con Google (cuenta sequiamaz@uniamazonia.edu.co)
1. Inicia sesión en Google con **sequiamaz@uniamazonia.edu.co** y entra a https://script.google.com → **Nuevo proyecto**.
2. Borra el contenido y pega todo el archivo `docs/google-apps-script/Code.gs`. Guarda (nombre: *Contacto Congreso*).
3. Cambia `SECRET` por una clave propia (ej. `CongresoPN-2027-x9k2`).
4. En el selector de funciones elige **configurarPrimeraVez** → **Ejecutar** → acepta los permisos. Se crea la hoja *"Contactos Congreso – Productos Naturales 2027"* en Drive.
5. **Implementar → Nueva implementación** → tipo **Aplicación web**:
   - Ejecutar como: **Yo (sequiamaz@uniamazonia.edu.co)**
   - Quién tiene acceso: **Cualquier persona**
6. Copia la **URL** que termina en `/exec`.
7. En `includes/config.php`:
   - `GAS_WEBAPP_URL` → pega la URL
   - `GAS_SECRET` → la misma clave del punto 3
8. Prueba enviando un mensaje desde http://localhost/congreso/contacto.php. Debe: aparecer una fila en la hoja, llegar un correo a sequiamaz@ y una confirmación al remitente.

**Si en XAMPP (Windows) falla con error SSL:** descarga https://curl.se/ca/cacert.pem a `C:\xampp\php\extras\ssl\cacert.pem` y en `C:\xampp\php\php.ini` pon `curl.cainfo="C:\xampp\php\extras\ssl\cacert.pem"`; reinicia Apache. (Alternativa solo local: `CURL_VERIFY_SSL` en `false`.)
Mientras Google no esté configurado, los mensajes se guardan en `storage/contactos.csv` (ábrelo con Excel).

> Si modificas Code.gs más adelante: **Implementar → Gestionar implementaciones → Editar → Nueva versión** (la URL se mantiene).

## Paso 7. Revisar en dispositivos
- En Chrome: F12 → icono de móvil → prueba iPhone, Android, tablet.
- Prueba en tu portátil (14") y monitor grande (27"). El diseño se adapta de 320 px a 2560 px.

## Paso 8. Subir al servidor institucional
1. En `includes/config.php` cambia `define('ENTORNO', 'local');` → `'produccion'` (oculta errores).
2. Si el sitio queda en la raíz del dominio, en `.htaccess` cambia a `ErrorDocument 404 /404.php`.
3. Sube **todo** el contenido de la carpeta por FTP/SFTP (FileZilla) o el panel del servidor.
4. Requisitos del servidor: Apache con `mod_rewrite` y PHP 8+ con extensión **curl**. No requiere base de datos.
5. Da permisos de escritura a `storage/` (755 o 775) para el respaldo CSV.
6. Verifica: inicio, todas las páginas del menú, formulario de contacto, descarga de plantilla y certificado HTTPS.
7. Comprueba que `https://tudominio/data/personas.json` responda **403 Prohibido** (protección activa).

## Personalización rápida
- **Colores**: `assets/css/variables.css` (paleta oficial #96A61C, #E4F2E5, #84BFA4, #048C8C, #F28E13).
- **Menú**: arreglo `$MENU` en `includes/config.php`.
- **Fecha de la cuenta regresiva**: `$SITE['fecha_inicio']`.
