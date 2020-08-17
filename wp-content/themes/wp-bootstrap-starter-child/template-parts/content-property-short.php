<div class="card shadow-sm rounded border-0 card-alt overflow-hidden mb-3">
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <header class="entry-header card-img-top">
            <?php if (get_field('property_image')): ?>
            <div class="embed-responsive embed-responsive-16by9">
                <img class="embed-responsive-item" src="<?php echo get_field('property_image'); ?>" alt="<?php _e(get_the_title()) ?>">
            </div>
            <?php endif; ?>
        </header>
        <div class="entry-content card-body">
            <div>
                <small>
                <?php
                $taxonomies = get_taxonomies('','names');
                $terms = wp_get_post_terms($post->ID, get_taxonomies('','names'));
                foreach ($terms as $term) {
                    echo $term->name;
                }
                ?>
                </small>
            </div>
            <?php
            the_title( '<h2 class="entry-title card-title h5 text-truncate mb-3">', '</h2>' );
            ?>
            <dl class="row">
                <?php
                $prop_text_fields = ['location_gps', 'house_number', 'building_type', 'environmental_friendliness', 'group_rooms'];
                foreach($prop_text_fields as $key) {
                    $field = get_field_object($key, false, false);
                    $value = get_field($key);
                    if ($value) {
                        if ($key == 'group_rooms') $value = count($value);
                        if ($key == 'location_gps') $value = implode('<br>', explode(',', $value));
                        echo '<dt class="col-8 text-truncate">'.$field['label'].'</dt><dd class="col-4 text-right">'.$value.'</dd>';
                    }
                }
                ?>
            </dl>
            <div class="text-right">
                <a class="btn btn-link text-uppercase font-weight-bold" href="<?php echo esc_url( get_permalink() ) ?>"><?php _e('Read more') ?></a>
            </div>
        </div>
    </article>
</div>
