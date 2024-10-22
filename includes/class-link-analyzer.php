<?php
// Evitar acceso directo
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Link_Analyzer {
    
    //Se encarga de iniciar las diferentes partes del plugin según el contexto (administración o frontend)
    public function run() {
        // Cargar clases de administración
        if ( is_admin() ) { //Retorna true si está en el área de administración
            require_once LINK_ANALYZER_PLUGIN_DIR . 'includes/admin/class-link-analyzer-admin.php';
            $admin = new Link_Analyzer_Admin();  //Instancia del manejador de la interfaz de administración del plugin.
            $admin->run();
        }
    }
}
