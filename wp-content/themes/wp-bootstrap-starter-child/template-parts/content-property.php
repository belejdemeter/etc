<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Bootstrap_Starter
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(['class' => 'mt-3']); ?>>
    <!--
    <div class="post-thumbnail">
        <?php /*the_post_thumbnail(); */?>
    </div>
-->
    <?php if (get_field('property_image')): ?>
    <div class="embed-responsive embed-responsive-16by9">
        <img class="embed-responsive-item" src="<?php echo get_field('property_image'); ?>" alt="<?php _e(get_the_title()) ?>">
    </div>
    <?php endif; ?>
    <header class="entry-header">
        <?php
        if ( is_single() ) :
            the_title( '<h1 class="entry-title my-3">', '</h1>' );
        else :
            the_title( '<h2 class="entry-title my-3"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
        endif;

        if ( 'post' === get_post_type() ) : ?>
            <div class="entry-meta">
                <?php wp_bootstrap_starter_posted_on(); ?>
            </div><!-- .entry-meta -->
        <?php
        endif; ?>
    </header><!-- .entry-header -->
    <div class="entry-content">
        <div class="row">
            <div class="col-md-8">
            <?php
            if ( is_single() ) :
                the_content();
            else :
                the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'wp-bootstrap-starter' ) );
            endif;

            wp_link_pages( array(
                'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'wp-bootstrap-starter' ),
                'after'  => '</div>',
            ) );
            ?>
            </div>
            <div class="col-md-4">
                <?php
                //    location_gps
                //    39.6752077,-121.9738321
                //    house_number
                //    111
                //    number_of_floors
                //    1
                //    building_type
                //    brick
                //    environmental_friendliness
                //    5
                //    property_image
                //    http://iloveworpress.test/wp-content/uploads/2020/08/code.jpg
                //    group_rooms
                //    Array
                ?>
                <dl>
                <?php
                $prop_text_fields = ['location_gps', 'house_number', 'building_type', 'environmental_friendliness'];
                foreach($prop_text_fields as $key) {
                    $field = get_field_object($key, false, false);
                    $value = get_field($key);
                    if ($value) echo '<dt>'.$field['label'].'</dt><dd>'.$value.'</dd>';
                }
                ?>
                </dl>
            </div>
        </div>

        <h2 class="mb-3">Rooms</h2>
        <div>
            <?php
            $room_text_fields = ['room_area', 'number_of_rooms', 'bathroom'];

            if( have_rows('group_rooms') ):
            while( have_rows('group_rooms') ): the_row();
            ?>
            <div class="row mb-3 d-flex">
                <div class="col-sm-3 align-self-center">
                    <img class="img-fluid" src="<?php echo get_sub_field('room_image'); ?>" alt="<?php _e(get_the_title()) ?>">
                </div>
                <div class="col-sm-9 align-self-center">
                    <dl class="row mb-0">
                    <?php
                    foreach($room_text_fields as $key) {
                        $field = get_sub_field_object($key, false, false);
                        $value = get_sub_field($key);
                        if ($field['type'] == 'true_false') $value = $value == true ? _('Yes') : _('No');
                        if ($value) echo '<dt class="col-sm-9">'.$field['label'].'</dt><dd class="col-sm-3 text-right">'.$value.'</dd>';
                    }
                    ?>
                    </dl>
                </div>
            </div>
            <?php endwhile; ?>
            <?php endif; ?>
        </div>

    </div><!-- .entry-content -->

    <footer class="entry-footer">
        <?php wp_bootstrap_starter_entry_footer(); ?>
    </footer><!-- .entry-footer -->
</article><!-- #post-## -->
