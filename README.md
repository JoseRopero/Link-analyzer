# 📄 Link Analyzer
<img src="./assets/images/link_analyzer_icon.png" alt="Logo del Plugin" width="50%">

![Licencia](https://img.shields.io/badge/licencia-GPLv2-blue.svg)
![Versión](https://img.shields.io/badge/version-1.0.0-green.svg)
![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-brightgreen.svg)

## Descripción

**Link Analyzer** es un potente plugin para WordPress que analiza y muestra todos los enlaces internos y externos de tu sitio web. Con una interfaz intuitiva, te permite diferenciar fácilmente entre enlaces dofollow, nofollow, internos y externos, proporcionando enlaces directos a las páginas de origen para una gestión eficiente de tus enlaces.

## 🌟 Características

- **Análisis Completo de Enlaces**: Escanea todas las publicaciones y páginas para identificar todos los enlaces internos y externos.
- **Diferenciación de Tipos de Enlaces**:
  - **Internos**: Enlaces que apuntan a otras páginas de tu sitio.
  - **Externos**: Enlaces que apuntan a sitios externos.
  - **Dofollow**: Enlaces que pasan autoridad SEO.
  - **Nofollow**: Enlaces que no pasan autoridad SEO.
- **Interfaz de Administración Intuitiva**: Presenta los resultados en una tabla clara y organizada.
- **Filtros Personalizados**: Filtra los enlaces por tipo (Interno, Externo) y relación (Dofollow, Nofollow).
- **Exportación a CSV**: Descarga los resultados del análisis en formato CSV para su análisis externo.
- **Actualización Automática**: Programa análisis diarios automáticos para mantener tus enlaces actualizados.
- **Estilos Personalizados**: Mejora la apariencia de la interfaz con estilos CSS personalizados.
- - **Tabla Interactiva**: Usa **DataTables** para añadir funcionalidades como paginación, búsqueda y ordenación.

## 🚀 Instalación

### 1. Descarga el Plugin

Puedes descargar el plugin directamente desde el repositorio de GitHub o utilizar el método de instalación manual.

### 2. Instalación Manual

1. **Descarga el Archivo ZIP**:
   - Ve a la página del repositorio de [Link Analyzer](https://github.com/tu-usuario/link-analyzer) en GitHub.
   - Haz clic en el botón **"Code"** y selecciona **"Download ZIP"**.

2. **Sube el Plugin a WordPress**:
   - Accede al panel de administración de WordPress.
   - Navega a **Plugins > Añadir nuevo**.
   - Haz clic en **"Subir plugin"** y selecciona el archivo ZIP descargado.
   - Haz clic en **"Instalar ahora"** y luego en **"Activar"**.

### 3. Activación

Una vez activado, encontrarás un nuevo menú llamado **Link Analyzer** en el panel de administración de WordPress.

## 📝 Uso

### 1. Acceder al Plugin

- En el panel de administración de WordPress, haz clic en **"Link Analyzer"** en el menú lateral.

### 2. Analizar Enlaces

- En la página principal del plugin, haz clic en el botón **"Analizar Enlaces"** para iniciar el análisis manual de los enlaces de tu sitio.

### 3. Ver Resultados

- Los resultados del análisis se mostrarán en una tabla con las siguientes columnas:
  - **Enlace**: URL del enlace.
  - **Tipo**: Indica si el enlace es Interno o Externo.
  - **Relación**: Indica si el enlace es Dofollow o Nofollow.
  - **Página de Origen**: Enlace a la página donde se encuentra el enlace analizado.

### 4. Filtros

- Utiliza los filtros para mostrar solo los enlaces de un tipo específico (Interno, Externo) o relación (Dofollow, Nofollow).

### 5. Tabla Interactiva con DataTables

El plugin utiliza **DataTables.js** para proporcionar una tabla interactiva con funcionalidades de búsqueda, ordenación y paginación.

#### Cargar Scripts

El archivo `link-analyzer-init.js` es el encargado de inicializar **DataTables** en la tabla de resultados del plugin. Asegúrate de que los archivos JavaScript necesarios se carguen correctamente.

1. **DataTables.js** y **jQuery** se encolan automáticamente en la página de administración del plugin.
2. El archivo `link-analyzer-init.js` contiene el siguiente código de inicialización:

```javascript
jQuery(document).ready(function($) {
    $(".link-analyzer-table").DataTable({
        "pageLength": 10,
        "order": [[0, "asc"]],
        "language": {
            "search": "Buscar:",
            "lengthMenu": "Mostrar _MENU_ enlaces por página",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ enlaces",
            "infoEmpty": "No hay enlaces disponibles",
            "infoFiltered": "(filtrado de _MAX_ enlaces totales)",
            "paginate": {
                "first": "Primera",
                "last": "Última",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        }
    });
});
```


### 6. Exportar a CSV

- Haz clic en el botón **"Exportar a CSV"** para descargar los resultados del análisis en un archivo CSV.

## 🤝 Contribuciones

¡Las contribuciones son bienvenidas! Si deseas mejorar este plugin, sigue estos pasos:

1. **Fork** el repositorio.
2. Crea una **rama** nueva para tu funcionalidad o corrección de errores.
3. Realiza tus **cambios** y **commits**.
4. Envía un **Pull Request** describiendo tus cambios.

Asegúrate de seguir las [buenas prácticas de desarrollo de WordPress](https://developer.wordpress.org/plugins/intro/best-practices/).

## 📜 Licencia

Este plugin está licenciado bajo la [GPLv2](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html) o superior.

## 📞 Contacto

Si tienes alguna pregunta, sugerencia o necesitas soporte, puedes contactarme a través de [josemanuelropero@hotmail.com](mailto:josemanuelropero@hotmail.com).

---

¡Gracias por usar **Link Analyzer**! Esperamos que te ayude a gestionar y optimizar los enlaces de tu sitio web de manera eficiente.
