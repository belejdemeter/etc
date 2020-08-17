<?php


class PropertyObjectShortcode
{

    private static function queryProperties($per_page = 6)
    {
        $args = [
            'posts_per_page' => $per_page,
            'post_type' => [
                'property'
            ]
        ];
        $query = new WP_Query( $args );

        $posts = [];
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                # $posts[] = get_post(); // '<li>' . get_the_title() . '</li>';
                $posts[] = '<div class="col-12 col-sm-6 col-md-6">'.static::load_template_part().'</div>';
            }
        } else {
            // Постов не найдено
        }
        // Возвращаем оригинальные данные поста. Сбрасываем $post.
        wp_reset_postdata();
        return $posts;
    }

    private static function load_template_part() {
        ob_start();
        get_template_part( 'template-parts/content', 'property-short' );
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }


    public static function handle($attrs, $content, $tag)
    {
        $html = '';
        $per_page = 6;
        $posts = static::queryProperties($per_page);
        if ($posts) {
            $html.= '<section>';
            $html.= $content ? "<div class='mb-3'>$content</div>" : '';
            $html.= '<div class="row">';
            $html.= implode("\n", $posts);
            $html.= '</div>';
            $html.= '<div><a class="btn btn-link btn-block text-uppercase" href="/property">'._('Show all').'</a></div>';
            $html.= '</section>';
        }
        return $html;
    }
}