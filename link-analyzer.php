<?php
/**
 * Plugin Name: Link Analyzer
 * Description: Analiza y muestra todos los enlaces internos y salientes de tu sitio web.
 * Version: 1.0
 * Author: Tu Nombre
 * License: GPL2
 */

//Este código evita que alguien acceda directamente al plugin a través de la URL.
// Verifica si la constante ABSPATH está definida. Representa la ruta absoluta al directorio de instalación de WordPress
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Definir constantes del plugin
define( 'LINK_ANALYZER_VERSION', '1.0' ); //Útil para gestionar actualizaciones y dependencias
define( 'LINK_ANALYZER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) ); //Ruta absoluta al directorio actual, archivo principal del plugin
define( 'LINK_ANALYZER_PLUGIN_URL', plugin_dir_url( __FILE__ ) ); //URL absoluta al directorio del archivo actual.

// Incluir el archivo de la clase principal
require_once LINK_ANALYZER_PLUGIN_DIR . 'includes/class-link-analyzer.php';

// Inicializar el plugin creando una instancia de la clase principal y ejecutando su método de arranque.
function run_link_analyzer() {
    $plugin = new Link_Analyzer();
    $plugin->run();
}
run_link_analyzer(); //inicia el plugin cuando el archivo principal sea ejecutado.
