<?php
// Evitar acceso directo
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Link_Analyzer_Admin {
    
    //Se encarga de añadir hooks y acciones necesarias para el funcionamiento de la interfaz
    public function run() {
        add_action('admin_menu', array($this, 'add_admin_menu')); //Cuando WordPress construye el menú, llamará al método "add_admin_menu"
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts')); //Para cargar scripts y estilos.
        add_action('admin_post_link_analyzer_export', array($this, 'export_csv')); //Acción para manejar la exportación de datos a CSV
        
        // Hooks de activación y desactivación
        register_activation_hook( LINK_ANALYZER_PLUGIN_DIR . 'link-analyzer.php', array( $this, 'activation_hook' ) );
        register_deactivation_hook( LINK_ANALYZER_PLUGIN_DIR . 'link-analyzer.php', array( $this, 'deactivation_hook' ) );

        add_action( 'link_analyzer_cron_hook', array( $this, 'process_links_cron' ) ); //Acción para manejar tareas programadas (cron jobs). 
    }

    public function add_admin_menu() {
        add_menu_page(
            'Link Analyzer',                // Título de la página se muestra en el navegador
            'Link Analyzer',                // Título del menú
            'manage_options',               // Capacidad requerida para acceder a esta pagina
            'link-analyzer',                // Slug único para identificar esta página de menú
            array($this, 'admin_page'),     // Función que muestra el contenido
            'dashicons-admin-links',        // Icono predeterminado de WordPress
            80                              // Posición en el menú
        );
    }

    //Cargar estilos CSS personalizados
    public function enqueue_scripts($hook) {  //hook, identificador de la página actual en administración de WorsPress
        if ( $hook != 'toplevel_page_link-analyzer' ) {  //Si no corresponde a la página de administración no carga los estilos
            return;
        }

        wp_enqueue_style('link-analyzer-styles', LINK_ANALYZER_PLUGIN_URL . 'css/link-analyzer.css', array(), LINK_ANALYZER_VERSION);
        wp_enqueue_style('datatables-css', 'https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css');
        wp_enqueue_script('jquery');
        wp_enqueue_script('datatables-js', 'https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js', array('jquery'), null, true);

        wp_enqueue_script('link-analyzer-init', LINK_ANALYZER_PLUGIN_URL . 'js/link-analyzer-init.js', array('datatables-js'), null, true);
    }

    //Renderizar la página de administración del plugin
    public function admin_page() {
        // Verificar permisos
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        // Comprueba si se ha enviado el formulario para analizar enlaces (botón analyze_links)
        if ( isset($_POST['analyze_links']) ) {
            $this->process_links();
        }

        // Obtener los enlaces almacenados
        //Los transients son una forma de almacenar datos temporalmente en WordPress con una expiración definida.
        $links = get_transient('link_analyzer_links');
        if ( false === $links ) { //Si el transient no existe recurre a obtener los enlaces desde la opción permanente. Si no existe retorna un array vacio.
            $links = get_option('link_analyzer_links', array());
        }

        // Obtener filtros. Comprueba si se han enviado parámetros de filtro a través de la URL ($_GET).
        $filter_type = isset($_GET['filter_type']) ? sanitize_text_field($_GET['filter_type']) : '';
        $filter_rel  = isset($_GET['filter_rel']) ? sanitize_text_field($_GET['filter_rel']) : '';

        // Si se ha aplicado algun filtro, utiliza array_filter para filtrar los enlaces q coincidan con los criterios.
        if ( ! empty($filter_type ) || ! empty($filter_rel) ) {
            $filtered_links = array_filter($links, function($link) use ($filter_type, $filter_rel) { //Función anónima que evalúa cada enlace por si debe incluirse.
                $match = true;
                if ( ! empty($filter_type) ) {  //Si se ha definido el tipo comprueba cada enlace por si coincide
                    $match = $match && ( $link['type'] === $filter_type );
                }
                if ( ! empty($filter_rel) ) {  //Si se ha definido la relación del enlace...
                    $match = $match && ( $link['rel'] === $filter_rel );
                }
                return $match;
            });
        } else { //Si no se aplican filtros, asigna todos los enlaces.
            $filtered_links = $links;
        }
        
        //Renderizamos la página de administración, "wrap" es la clase estándar de WordPress para estilizar páginas de administración
        //Generamos campos ocultos de seguridad (nonces) para verificar que ha sido enviado desde la página de administración
        ?>
        <div class="wrap">
            <h1>Link Analyzer</h1>
            <form method="post">
                <?php wp_nonce_field('link_analyzer_nonce', 'link_analyzer_nonce_field'); ?>
                <p>
                    <input type="submit" name="analyze_links" class="button button-primary" value="Analizar Enlaces">
                </p>
            </form>

            <h2>Filtros</h2>
            <form method="get">
                <input type="hidden" name="page" value="link-analyzer">
                <label for="filter_type">Tipo:</label>
                <select name="filter_type" id="filter_type">
                    <option value="">Todos</option>
                    <option value="Interno" <?php selected( $filter_type, 'Interno' ); ?>>Interno</option>
                    <option value="Externo" <?php selected( $filter_type, 'Externo' ); ?>>Externo</option>
                </select>

                <label for="filter_rel">Relación:</label>
                <select name="filter_rel" id="filter_rel">
                    <option value="">Todos</option>
                    <option value="Dofollow" <?php selected( $filter_rel, 'Dofollow' ); ?>>Dofollow</option>
                    <option value="Nofollow" <?php selected( $filter_rel, 'Nofollow' ); ?>>Nofollow</option>
                </select>

                <input type="submit" class="button" value="Aplicar Filtros">
            </form>

            <?php if ( ! empty($filtered_links) ) : ?>
                <h2>Resultados del Análisis</h2>
                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                    <?php wp_nonce_field('link_analyzer_export_nonce', 'link_analyzer_export_nonce_field'); ?>
                    <input type="hidden" name="action" value="link_analyzer_export">
                    <input type="submit" class="button" value="Exportar a CSV">
                </form>
                <table class="link-analyzer-table widefat fixed" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Enlace</th>
                            <th>Tipo</th>
                            <th>Relación</th>
                            <th>Página de Origen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $filtered_links as $link ) : ?>
                            <tr>
                                <td>
                                    <a href="<?php echo esc_url($link['url']); ?>" target="_blank">
                                        <?php if ($link['type'] === 'Interno') : ?>
                                            <span class="dashicons dashicons-admin-links link-interno"></span>
                                        <?php else : ?>
                                            <span class="dashicons dashicons-external link-externo"></span>
                                        <?php endif; ?>
                                        <?php echo esc_html($link['url']); ?>
                                    </a>
                                </td>
                                <td><?php echo esc_html($link['type']); ?></td>
                                <td>
                                    <?php if ( $link['rel'] === 'Nofollow') : ?>
                                        <span class="dashicons dashicons-no link-nofollow"></span> Nofollow
                                    <?php else : ?>
                                        <span class="dashicons dashicons-yes link-dofollow"></span> Dofollow
                                    <?php endif; ?>
                                </td>
                                <td><a href="<?php echo get_permalink($link['post_id']); ?>" target="_blank">Página <?php echo esc_html($link['post_id']); ?></a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>No se han encontrado enlaces. Haz clic en "Analizar Enlaces" para comenzar.</p>
            <?php endif; ?>
        </div>

        <?php
    }
    
    //Analiza todos los enlaces internos y externos en publicaciones y páginas del sitio. Se almacena para visuañizar y exportar.
    public function process_links() {
        // Verificar nonce para prevenir ataques tipo Cross-Site Request Forgery(CSRF)
        if ( ! isset($_POST['link_analyzer_nonce_field']) || ! wp_verify_nonce($_POST['link_analyzer_nonce_field'], 'link_analyzer_nonce') ) {
            wp_die('Nonce inválido');
        }

        // Inicializar array para almacenar enlaces
        $all_links = array();

        // Obtener todas las publicaciones y páginas
        $args = array(
            'post_type'      => array('post', 'page'),
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        );

        $posts = get_posts($args);

        // Obtener la URL del sitio para diferenciar internos y externos
        $site_url = get_site_url();
        
        //Iteramos sobre cada publicación, extraemos los enlaces, clasificamos y almacenamos en el array.
        foreach ( $posts as $post ) {
            // Obtener el contenido de la publicación
            $content = $post->post_content;

            // Utilizar DOMDocument para parsear el contenido. Clase de PHP para manipular documentos HTML y XML
            libxml_use_internal_errors(true);
            $dom = new DOMDocument();
            $dom->loadHTML('<?xml encoding="utf-8" ?>' . $content);
            libxml_clear_errors();

            $links = $dom->getElementsByTagName('a'); //Obtenmos los hipervinculos

            //Iteramos sobre cada enlace
            foreach ( $links as $link ) {
                $href = $link->getAttribute('href');  //Contiene la url de destino
                $rel  = $link->getAttribute('rel');  //Puede contener valores como nofollow

                // Saltar enlaces vacíos o anclas
                if ( empty($href) || strpos($href, '#') === 0 ) {
                    continue;
                }

                // Determinar si el enlace es interno o externo
                //Si comienza con la URL del sitio o con /, se considera interno. Cualquier otro, externo
                if ( strpos($href, $site_url) === 0 || strpos($href, '/') === 0 ) {
                    $type = 'Interno';
                } else {
                    $type = 'Externo';
                }

                // Determinar si es dofollow o nofollow
                //Si el atributo rel contiene nofollow se clasifica como nofollow
                if ( strpos(strtolower($rel), 'nofollow') !== false ) {
                    $rel_type = 'Nofollow';
                } else {
                    $rel_type = 'Dofollow';
                }

                // Llamamos al método privado para asegurarnos de que todas las URLs sean absolutas, para facilitar la comparación y presentación.
                $href = $this->make_url_absolute($href, $site_url);

                // Agregar al array
                $all_links[] = array(
                    'url'    => esc_url($href),
                    'type'   => $type,
                    'rel'    => $rel_type,
                    'post_id'=> $post->ID,
                );
            }
        }

        // Eliminar duplicados
        $all_links = array_unique( array_map( 'serialize', $all_links ) );
        $all_links = array_map( 'unserialize', $all_links );

        // Almacenar en la base de datos usando transientes, que expira después de 1 dia
        set_transient('link_analyzer_links', $all_links, DAY_IN_SECONDS);

    }

    private function make_url_absolute($url, $site_url) {
        // Si la URL es relativa, convertirla a absoluta
        if ( strpos($url, '/') === 0 ) {
            return rtrim($site_url, '/') . $url;
        }
        return $url;
    }

    //Exportar los enlaces a un archivo CSV descargable.
    public function export_csv() {
        // Verificar nonce
        if ( ! isset($_POST['link_analyzer_export_nonce_field']) || ! wp_verify_nonce($_POST['link_analyzer_export_nonce_field'], 'link_analyzer_export_nonce') ) {
            wp_die('Nonce inválido para exportar CSV');
        }

        // Obtener los enlaces almacenados
        $links = get_transient('link_analyzer_links'); //Se intenta desde el transiente, si ha expirado o no existe, se recupera la opción permanente.
        if ( false === $links ) {
            $links = get_option('link_analyzer_links', array());
        }

        if ( empty($links) ) {
            wp_die('No hay enlaces para exportar.');
        }

        // Configurar las cabeceras para forzar la descarga en lugar de mostrarlo en el navegador.
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=link-analyzer.csv');

        // Abrir el flujo de salida en modo escritura.
        $output = fopen('php://output', 'w');

        // Escribir la fila de encabezados
        fputcsv($output, array('URL', 'Tipo', 'Relación', 'Página de Origen'));

        // Escribir los datos
        foreach ( $links as $link ) {
            fputcsv($output, array(
                $link['url'],
                $link['type'],
                $link['rel'],
                get_permalink($link['post_id']),
            ));
        }

        fclose($output);
        exit;
    }

    //Métodos para menejar eventos cron
    //Programar una tarea cron diaria para actualizar automáticamente el análisis de enlaces.
    public function activation_hook() {
        if ( ! wp_next_scheduled( 'link_analyzer_cron_hook' ) ) {
            wp_schedule_event( time(), 'daily', 'link_analyzer_cron_hook' );
        }
    }

    public function deactivation_hook() {
        $timestamp = wp_next_scheduled( 'link_analyzer_cron_hook' );
        if ( $timestamp ) {
            wp_unschedule_event( $timestamp, 'link_analyzer_cron_hook' );
        }
    }

    public function process_links_cron() {
        $this->process_links();
    }
}
