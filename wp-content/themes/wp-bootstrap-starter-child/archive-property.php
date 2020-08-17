<?php
/**
 * The template for displaying archive pages
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Bootstrap_Starter
 */

get_header(); ?>

    <section id="primary" class="content-area col-sm-12 col-lg-9 mt-3">
        <main id="main" class="site-main" role="main">

            <div class="row">
            <?php
            if ( have_posts() ) : ?>

                <!--header class="page-header">
                    <?php
                    the_title('<h1 class="page-title">', '</h1>');

                    ?>
                </header-->

                <?php
                /* Start the Loop */
                while ( have_posts() ) : the_post();
                    echo '<div class="col-12 col-sm-6 col-md-6">';
                    get_template_part( 'template-parts/content', 'property-short' );
                    echo '</div>';
                endwhile;

                the_posts_navigation();

            else :

                get_template_part( 'template-parts/content', 'none' );

            endif; ?>
            </div>

            <footer>
                <?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
            </footer>

        </main><!-- #main -->
    </section><!-- #primary -->

<?php
get_sidebar(['class' => 'col-sm-12 col-lg-3']);
get_footer();
