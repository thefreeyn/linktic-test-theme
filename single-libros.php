<?php
/**
 * Template for single Libro (CPT)
 *
 * Muestra: título, imagen destacada, contenido, autor (metabox) y géneros (taxonomía).
 *
 * @package LinkTIC_Test
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

while ( have_posts() ) :
    the_post();

    $autor   = get_post_meta( get_the_ID(), '_linktic_autor_libro', true );
    $generos = get_the_terms( get_the_ID(), 'generos' );
    ?>

    <main id="content" class="linktic-single-libro" role="main">
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

            <!-- Breadcrumb simple -->
            <nav class="libro-breadcrumb" aria-label="Breadcrumb">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Inicio</a>
                <span aria-hidden="true">&rsaquo;</span>
                <a href="<?php echo esc_url( get_post_type_archive_link( 'libros' ) ); ?>">Libros</a>
                <span aria-hidden="true">&rsaquo;</span>
                <span aria-current="page"><?php the_title(); ?></span>
            </nav>

            <div class="libro-header">
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="libro-portada">
                        <?php the_post_thumbnail( 'large', [
                            'alt'   => esc_attr( get_the_title() ),
                            'class' => 'libro-portada-img',
                        ] ); ?>
                    </div>
                <?php else : ?>
                    <div class="libro-portada libro-portada-placeholder">
                        <div class="placeholder-icon">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#0094FF" stroke-width="1.5" aria-hidden="true">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                            </svg>
                            <span>Sin portada</span>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="libro-info">
                    <h1 class="libro-titulo"><?php the_title(); ?></h1>

                    <?php if ( $autor ) : ?>
                        <div class="libro-autor">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            <span><strong>Autor:</strong> <?php echo esc_html( $autor ); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ( $generos && ! is_wp_error( $generos ) ) : ?>
                        <div class="libro-generos">
                            <strong>Géneros:</strong>
                            <ul>
                                <?php foreach ( $generos as $genero ) : ?>
                                    <li>
                                        <a href="<?php echo esc_url( get_term_link( $genero ) ); ?>">
                                            <?php echo esc_html( $genero->name ); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if ( has_excerpt() ) : ?>
                        <div class="libro-excerpt">
                            <em><?php the_excerpt(); ?></em>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="libro-contenido">
                <?php the_content(); ?>
            </div>

            <footer class="libro-footer">
                <a href="<?php echo esc_url( get_post_type_archive_link( 'libros' ) ); ?>" class="libro-back-link">
                    &larr; Volver a todos los libros
                </a>
            </footer>

        </article>
    </main>

    <?php
endwhile;

get_footer();
