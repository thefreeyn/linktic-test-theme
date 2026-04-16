<?php
/**
 * LinkTIC Test Theme - functions.php
 * Tema hijo de Hello Elementor
 *
 * @package LinkTIC_Test
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'LINKTIC_THEME_VERSION', '1.0.0' );
define( 'LINKTIC_THEME_DIR', get_stylesheet_directory() );
define( 'LINKTIC_THEME_URI', get_stylesheet_directory_uri() );

/* =========================================================================
   1. ENQUEUE STYLES & SCRIPTS
   ========================================================================= */

function linktic_enqueue_assets() {
    // Parent theme
    wp_enqueue_style(
        'hello-elementor',
        get_template_directory_uri() . '/style.css',
        [],
        LINKTIC_THEME_VERSION
    );

    // Child theme
    wp_enqueue_style(
        'linktic-test-style',
        get_stylesheet_uri(),
        [ 'hello-elementor' ],
        LINKTIC_THEME_VERSION
    );

    // Google Fonts - Inter
    wp_enqueue_style(
        'linktic-google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap',
        [],
        null
    );

    // Custom CSS
    wp_enqueue_style(
        'linktic-custom',
        LINKTIC_THEME_URI . '/assets/css/custom.css',
        [ 'linktic-test-style' ],
        LINKTIC_THEME_VERSION
    );

    // Accessibility JS
    wp_enqueue_script(
        'linktic-accessibility',
        LINKTIC_THEME_URI . '/assets/js/accessibility.js',
        [],
        LINKTIC_THEME_VERSION,
        true
    );

    // Popup JS
    wp_enqueue_script(
        'linktic-popup',
        LINKTIC_THEME_URI . '/assets/js/popup.js',
        [],
        LINKTIC_THEME_VERSION,
        true
    );

    // Localize AJAX data
    wp_localize_script( 'linktic-accessibility', 'linkticAjax', [
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'linktic_form_nonce' ),
    ] );
}
add_action( 'wp_enqueue_scripts', 'linktic_enqueue_assets' );

/* =========================================================================
   2. THEME SUPPORT & MENUS
   ========================================================================= */

function linktic_theme_support() {
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'custom-logo' );
}
add_action( 'after_setup_theme', 'linktic_theme_support' );

function linktic_register_menus() {
    register_nav_menus( [
        'primary' => __( 'Menú Principal', 'linktic-test' ),
    ] );
}
add_action( 'init', 'linktic_register_menus' );

/* =========================================================================
   3. REGISTER CPT "LIBROS"
   ========================================================================= */

function linktic_register_cpt_libros() {
    $labels = [
        'name'                  => 'Libros',
        'singular_name'         => 'Libro',
        'menu_name'             => 'Libros',
        'add_new'               => 'Añadir nuevo',
        'add_new_item'          => 'Añadir nuevo libro',
        'edit_item'             => 'Editar libro',
        'new_item'              => 'Nuevo libro',
        'view_item'             => 'Ver libro',
        'search_items'          => 'Buscar libros',
        'not_found'             => 'No se encontraron libros',
        'not_found_in_trash'    => 'No se encontraron libros en la papelera',
        'all_items'             => 'Todos los libros',
        'archives'              => 'Archivo de libros',
        'featured_image'        => 'Portada del libro',
        'set_featured_image'    => 'Establecer portada',
        'remove_featured_image' => 'Quitar portada',
        'use_featured_image'    => 'Usar como portada',
    ];

    $args = [
        'labels'        => $labels,
        'public'        => true,
        'has_archive'   => true,
        'show_in_rest'  => true,
        'menu_icon'     => 'dashicons-book-alt',
        'supports'      => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
        'rewrite'       => [ 'slug' => 'libros' ],
        'show_in_menu'  => true,
        'menu_position' => 5,
    ];

    register_post_type( 'libros', $args );
}
add_action( 'init', 'linktic_register_cpt_libros' );

/* =========================================================================
   4. REGISTER TAXONOMY "GENEROS"
   ========================================================================= */

function linktic_register_taxonomy_generos() {
    $labels = [
        'name'              => 'Géneros',
        'singular_name'     => 'Género',
        'search_items'      => 'Buscar géneros',
        'all_items'         => 'Todos los géneros',
        'parent_item'       => 'Género padre',
        'parent_item_colon' => 'Género padre:',
        'edit_item'         => 'Editar género',
        'update_item'       => 'Actualizar género',
        'add_new_item'      => 'Añadir nuevo género',
        'new_item_name'     => 'Nombre del nuevo género',
        'menu_name'         => 'Géneros',
    ];

    $args = [
        'labels'            => $labels,
        'hierarchical'      => true,
        'public'            => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'generos' ],
    ];

    register_taxonomy( 'generos', 'libros', $args );
}
add_action( 'init', 'linktic_register_taxonomy_generos' );

/* =========================================================================
   5. METABOX "AUTOR DEL LIBRO"
   ========================================================================= */

function linktic_add_metabox_autor() {
    add_meta_box(
        'linktic_autor_libro',
        'Autor del Libro',
        'linktic_render_metabox_autor',
        'libros',
        'side',
        'high'
    );
}
add_action( 'add_meta_boxes', 'linktic_add_metabox_autor' );

function linktic_render_metabox_autor( $post ) {
    $autor = get_post_meta( $post->ID, '_linktic_autor_libro', true );
    wp_nonce_field( 'linktic_save_autor', 'linktic_autor_nonce' );
    ?>
    <label for="linktic_autor_libro" style="display:block;margin-bottom:5px;font-weight:600;">
        Nombre del autor:
    </label>
    <input
        type="text"
        id="linktic_autor_libro"
        name="linktic_autor_libro"
        value="<?php echo esc_attr( $autor ); ?>"
        style="width:100%;"
        placeholder="Ej: Gabriel García Márquez"
    />
    <?php
}

function linktic_save_metabox_autor( $post_id ) {
    // Verify nonce.
    if ( ! isset( $_POST['linktic_autor_nonce'] ) ||
         ! wp_verify_nonce( $_POST['linktic_autor_nonce'], 'linktic_save_autor' ) ) {
        return;
    }

    // Check autosave.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check permissions.
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Save.
    if ( isset( $_POST['linktic_autor_libro'] ) ) {
        update_post_meta(
            $post_id,
            '_linktic_autor_libro',
            sanitize_text_field( wp_unslash( $_POST['linktic_autor_libro'] ) )
        );
    }
}
add_action( 'save_post_libros', 'linktic_save_metabox_autor' );

/* =========================================================================
   6. ACCESSIBILITY BAR (injected after <body>)
   ========================================================================= */

function linktic_accessibility_bar() {
    ?>
    <div id="linktic-accessibility-bar" role="toolbar" aria-label="Controles de accesibilidad">
        <button id="linktic-contrast-toggle" title="Alternar contraste alto" aria-label="Alternar contraste alto" aria-pressed="false">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 2a10 10 0 0 1 0 20V2z"/></svg>
        </button>
        <button id="linktic-zoom-in" title="Aumentar tamaño de texto" aria-label="Aumentar tamaño de texto">
            A+
        </button>
        <button id="linktic-zoom-out" title="Disminuir tamaño de texto" aria-label="Disminuir tamaño de texto">
            A&minus;
        </button>
    </div>
    <?php
}
add_action( 'wp_body_open', 'linktic_accessibility_bar' );

/* =========================================================================
   7. POPUP HTML (injected before </body>)
   ========================================================================= */

function linktic_popup_html() {
    ?>
    <div id="linktic-popup-overlay" class="linktic-popup-hidden" role="dialog" aria-modal="true" aria-labelledby="linktic-popup-title">
        <div id="linktic-popup-content">
            <button id="linktic-popup-close" aria-label="Cerrar popup">&times;</button>
            <div class="linktic-popup-body">
                <h3 id="linktic-popup-title">&#128075; ¡Hola!</h3>
                <p>Soy <strong>Jhojan [Tu Apellido]</strong>.</p>
                <p>Gracias por revisar mi prueba técnica de WordPress.</p>
            </div>
        </div>
    </div>
    <?php
}
add_action( 'wp_footer', 'linktic_popup_html' );

/* =========================================================================
   8. CONTACT FORM – AJAX HANDLER (Google Sheets webhook)
   ========================================================================= */

/**
 * URL del Google Apps Script web app.
 * Reemplazar con la URL real después del deploy del script.
 * Ver README.md para instrucciones de configuración.
 */
if ( ! defined( 'LINKTIC_SHEETS_WEBHOOK' ) ) {
    define( 'LINKTIC_SHEETS_WEBHOOK', '' );
}

function linktic_handle_contact_form() {
    check_ajax_referer( 'linktic_form_nonce', 'nonce' );

    $name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
    $email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
    $phone   = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
    $company = isset( $_POST['company'] ) ? sanitize_text_field( wp_unslash( $_POST['company'] ) ) : '';
    $message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

    if ( empty( $name ) || empty( $email ) ) {
        wp_send_json_error( [ 'message' => 'Nombre y correo son obligatorios.' ] );
    }

    if ( ! is_email( $email ) ) {
        wp_send_json_error( [ 'message' => 'El correo electrónico no es válido.' ] );
    }

    $data = [
        'nombre'   => $name,
        'correo'   => $email,
        'telefono' => $phone,
        'empresa'  => $company,
        'mensaje'  => $message,
        'fecha'    => current_time( 'Y-m-d H:i:s' ),
    ];

    $sheet_ok = true;

    // Send to Google Sheets if webhook is configured.
    if ( LINKTIC_SHEETS_WEBHOOK ) {
        $response = wp_remote_post( LINKTIC_SHEETS_WEBHOOK, [
            'body'    => wp_json_encode( $data ),
            'headers' => [ 'Content-Type' => 'application/json' ],
            'timeout' => 15,
        ] );

        if ( is_wp_error( $response ) ) {
            $sheet_ok = false;
        }
    }

    // Save locally as backup evidence.
    $entries   = get_option( 'linktic_form_entries', [] );
    $entries[] = $data;
    update_option( 'linktic_form_entries', $entries );

    if ( $sheet_ok ) {
        wp_send_json_success( [ 'message' => '¡Mensaje enviado correctamente!' ] );
    } else {
        wp_send_json_error( [ 'message' => 'Error al enviar a la hoja. Se guardó localmente.' ] );
    }
}
add_action( 'wp_ajax_linktic_contact_form', 'linktic_handle_contact_form' );
add_action( 'wp_ajax_nopriv_linktic_contact_form', 'linktic_handle_contact_form' );

/* =========================================================================
   9. ADMIN PAGE – VIEW LOCAL FORM ENTRIES (evidencia)
   ========================================================================= */

function linktic_admin_menu_entries() {
    add_menu_page(
        'Entradas del Formulario',
        'Formulario LinkTIC',
        'manage_options',
        'linktic-form-entries',
        'linktic_render_form_entries',
        'dashicons-email-alt',
        30
    );
}
add_action( 'admin_menu', 'linktic_admin_menu_entries' );

function linktic_render_form_entries() {
    $entries = get_option( 'linktic_form_entries', [] );
    ?>
    <div class="wrap">
        <h1>Entradas del Formulario de Contacto</h1>
        <p>Estas entradas se guardan localmente como evidencia. Total: <strong><?php echo count( $entries ); ?></strong></p>
        <?php if ( ! empty( $entries ) ) : ?>
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Empresa</th>
                        <th>Mensaje</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( array_reverse( $entries ) as $i => $entry ) : ?>
                        <tr>
                            <td><?php echo esc_html( count( $entries ) - $i ); ?></td>
                            <td><?php echo esc_html( $entry['nombre'] ?? '' ); ?></td>
                            <td><?php echo esc_html( $entry['correo'] ?? '' ); ?></td>
                            <td><?php echo esc_html( $entry['telefono'] ?? '' ); ?></td>
                            <td><?php echo esc_html( $entry['empresa'] ?? '' ); ?></td>
                            <td><?php echo esc_html( $entry['mensaje'] ?? '' ); ?></td>
                            <td><?php echo esc_html( $entry['fecha'] ?? '' ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>No hay entradas aún.</p>
        <?php endif; ?>
    </div>
    <?php
}

/* =========================================================================
   10. THEME ACTIVATION SETUP
   ========================================================================= */

function linktic_theme_activation() {
    // Ensure CPT and taxonomy are registered before creating content.
    linktic_register_cpt_libros();
    linktic_register_taxonomy_generos();

    // Create home page.
    $home_page = get_page_by_path( 'inicio' );
    if ( ! $home_page ) {
        $page_id = wp_insert_post( [
            'post_title'   => 'Inicio',
            'post_name'    => 'inicio',
            'post_content' => '',
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ] );

        if ( $page_id && ! is_wp_error( $page_id ) ) {
            update_option( 'show_on_front', 'page' );
            update_option( 'page_on_front', $page_id );
            // Set Elementor canvas template.
            update_post_meta( $page_id, '_wp_page_template', 'elementor_header_footer' );
        }
    }

    // Create sample book.
    $existing = get_posts( [
        'post_type'   => 'libros',
        'name'        => 'cien-anos-de-soledad',
        'numberposts' => 1,
    ] );

    if ( empty( $existing ) ) {
        $book_id = wp_insert_post( [
            'post_title'   => 'Cien Años de Soledad',
            'post_name'    => 'cien-anos-de-soledad',
            'post_content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
            'post_status'  => 'publish',
            'post_type'    => 'libros',
            'post_excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent vel erat nec libero lobortis blandit.',
        ] );

        if ( $book_id && ! is_wp_error( $book_id ) ) {
            update_post_meta( $book_id, '_linktic_autor_libro', 'Gabriel García Márquez' );

            // Create and assign genres.
            $term = wp_insert_term( 'Realismo Mágico', 'generos' );
            if ( ! is_wp_error( $term ) ) {
                wp_set_object_terms( $book_id, [ (int) $term['term_id'] ], 'generos' );
            }

            wp_insert_term( 'Novela', 'generos' );
            wp_insert_term( 'Ciencia Ficción', 'generos' );
            wp_insert_term( 'Poesía', 'generos' );
            wp_insert_term( 'Historia', 'generos' );
        }
    }

    // Set permalink structure.
    global $wp_rewrite;
    $wp_rewrite->set_permalink_structure( '/%postname%/' );
    flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'linktic_theme_activation' );

/* =========================================================================
   11. SHORTCODE FOR CONTACT FORM
   ========================================================================= */

function linktic_contact_form_shortcode() {
    ob_start();
    ?>
    <form id="linktic-contact-form" class="linktic-contact-form" novalidate>
        <div class="form-group">
            <label for="linktic-name">Nombre *</label>
            <input type="text" id="linktic-name" name="name" required placeholder="Tu nombre completo" />
        </div>
        <div class="form-group">
            <label for="linktic-email">Correo electrónico *</label>
            <input type="email" id="linktic-email" name="email" required placeholder="correo@ejemplo.com" />
        </div>
        <div class="form-group">
            <label for="linktic-phone">Teléfono</label>
            <input type="tel" id="linktic-phone" name="phone" placeholder="+57 300 000 0000" />
        </div>
        <div class="form-group">
            <label for="linktic-company">Empresa</label>
            <input type="text" id="linktic-company" name="company" placeholder="Nombre de tu empresa" />
        </div>
        <div class="form-group">
            <label for="linktic-message">Mensaje *</label>
            <textarea id="linktic-message" name="message" required placeholder="¿En qué podemos ayudarte?"></textarea>
        </div>
        <button type="submit" class="form-submit">Enviar mensaje</button>
        <div class="linktic-form-message" aria-live="polite"></div>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode( 'linktic_contact_form', 'linktic_contact_form_shortcode' );

/* =========================================================================
   9. NAVIGATION MENU WITH DROPDOWNS
   ========================================================================= */

function linktic_nav_shortcode() {
    ob_start();
    ?>
    <nav class="linktic-nav" role="navigation" aria-label="Menú principal">
        <ul class="linktic-nav-list">

            <li class="linktic-nav-item has-submenu">
                <a href="#servicios" class="linktic-nav-link" aria-haspopup="true" aria-expanded="false">
                    Servicios
                    <svg class="linktic-nav-arrow" xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
                </a>
                <ul class="linktic-submenu" role="menu">
                    <li role="none"><a href="#" role="menuitem" class="linktic-submenu-link">Consultoría TI</a></li>
                    <li role="none"><a href="#" role="menuitem" class="linktic-submenu-link">Desarrollo de Software</a></li>
                    <li role="none"><a href="#" role="menuitem" class="linktic-submenu-link">Ciberseguridad</a></li>
                    <li role="none"><a href="#" role="menuitem" class="linktic-submenu-link">Cloud &amp; Infraestructura</a></li>
                </ul>
            </li>

            <li class="linktic-nav-item has-submenu">
                <a href="#empresa" class="linktic-nav-link" aria-haspopup="true" aria-expanded="false">
                    Empresa
                    <svg class="linktic-nav-arrow" xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
                </a>
                <ul class="linktic-submenu" role="menu">
                    <li role="none"><a href="#" role="menuitem" class="linktic-submenu-link">Quiénes Somos</a></li>
                    <li role="none"><a href="#" role="menuitem" class="linktic-submenu-link">Historia</a></li>
                    <li role="none"><a href="#" role="menuitem" class="linktic-submenu-link">Sostenibilidad</a></li>
                    <li role="none"><a href="#" role="menuitem" class="linktic-submenu-link">Noticias</a></li>
                </ul>
            </li>

            <li class="linktic-nav-item has-submenu">
                <a href="#clientes" class="linktic-nav-link" aria-haspopup="true" aria-expanded="false">
                    Nuestro Talento
                    <svg class="linktic-nav-arrow" xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
                </a>
                <ul class="linktic-submenu" role="menu">
                    <li role="none"><a href="#" role="menuitem" class="linktic-submenu-link">Únete a LinkTIC</a></li>
                    <li role="none"><a href="#" role="menuitem" class="linktic-submenu-link">Cultura Organizacional</a></li>
                    <li role="none"><a href="#" role="menuitem" class="linktic-submenu-link">Bienestar</a></li>
                </ul>
            </li>

            <li class="linktic-nav-item has-submenu">
                <a href="#blog" class="linktic-nav-link" aria-haspopup="true" aria-expanded="false">
                    Blog
                    <svg class="linktic-nav-arrow" xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
                </a>
                <ul class="linktic-submenu" role="menu">
                    <li role="none"><a href="#" role="menuitem" class="linktic-submenu-link">Artículos</a></li>
                    <li role="none"><a href="#" role="menuitem" class="linktic-submenu-link">Casos de Éxito</a></li>
                </ul>
            </li>

            <li class="linktic-nav-item">
                <a href="#contacto" class="linktic-nav-link linktic-nav-cta">Contáctanos</a>
            </li>

        </ul>
    </nav>
    <?php
    return ob_get_clean();
}
add_shortcode( 'linktic_nav', 'linktic_nav_shortcode' );
