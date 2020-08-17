<?php

namespace Property;

class PropertyObjectPlugin
{
    /** @var string */
    protected $plugin_name;

    /** @var string */
    protected $version;

    /**
     * PropertyObjectPlugin constructor.
     */
    public function __construct()
    {
        if (defined('PROPERTY_OBJECT_VERSION')) {
            $this->version = PROPERTY_OBJECT_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'property-object';
    }

    /**
     * Initialize.
     */
    public function run()
    {
        add_action('init', function() {
            return $this->registerPostType();
        }, 0);
        add_action('init', function() {
            return $this->registerTaxonomy();
        }, 0);

        $this->registerAcfFields();
        $this->registerAcfQuery();
        $this->registerRestApi();


        add_shortcode( 'properties' , ['PropertyObjectShortcode', 'handle'] );

        // Register the widget
        add_action( 'widgets_init', function() {
            register_widget( 'PropertyObjectWidget' );
        });


    }

    /**
     * Register a new taxonomy
     */
    private function registerTaxonomy()
    {
        $labels = [
            'name' => 'District',
            'singular_name' => 'District',
            'search_items' => 'Search district',
            'all_items' => 'All district',
            'view_item ' => 'View district',
            'parent_item' => 'Parent district',
            'parent_item_colon' => 'Parent district:',
            'edit_item' => 'Edit district',
            'update_item' => 'Update district',
            'add_new_item' => 'Add New district',
            'new_item_name' => 'New District name',
            'menu_name' => 'Districts',
        ];
        $args = [
            'label' => '', // определяется параметром $labels->name
            'labels' => $labels,
            'description' => '',
            'public' => true,
            // 'publicly_queryable'    => null, // равен аргументу public
            // 'show_in_nav_menus'     => true, // равен аргументу public
            'show_ui'               => true, // равен аргументу public
            'show_in_menu'          => true, // равен аргументу show_ui
            // 'show_tagcloud'         => true, // равен аргументу show_ui
            'show_in_quick_edit'    => null, // равен аргументу show_ui
            'hierarchical' => true,
            'rewrite' => true,
            'capabilities' => array(),
            'meta_box_cb' => null, // html метабокса. callback: `post_categories_meta_box` или `post_tags_meta_box`. false — метабокс отключен.
            'show_admin_column' => false, // авто-создание колонки таксы в таблице ассоциированного типа записи. (с версии 3.5)
            'show_in_rest' => null, // добавить в REST API
            'rest_base' => null, // $taxonomy
            // '_builtin'              => false,
            //'update_count_callback' => '_update_post_term_count',
        ];
        register_taxonomy('property-object', ['property'], $args);
    }

    /**
     * Register a new post type
     */
    private function registerPostType()
    {
        $labels = array(
            'name'                => __( 'Properties' ),
            'singular_name'       => __( 'Property'),
            'menu_name'           => __( 'Properties'),
            'parent_item_colon'   => __( 'Parent'),
            'all_items'           => __( 'All Properties'),
            'view_item'           => __( 'View Property'),
            'add_new_item'        => __( 'Add New Property'),
            'add_new'             => __( 'Add New'),
            'edit_item'           => __( 'Edit Property'),
            'update_item'         => __( 'Update Property'),
            'search_items'        => __( 'Search Property'),
            'not_found'           => __( 'Not Found'),
            'not_found_in_trash'  => __( 'Not found in Trash')
        );
        $args = array(
            'label'               => __( 'properties'),
            'description'         => __( 'Best Properties'),
            'labels'              => $labels,
            'supports'            => array( 'title', 'editor', /*'excerpt', 'author', 'thumbnail', 'revisions',*/ 'custom-fields'),
            'public'              => true,
            'hierarchical'        => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'has_archive'         => true,
            'can_export'          => true,
            'exclude_from_search' => false,
            'yarpp_support'       => true,
            'taxonomies' => [
                'property-object'
            ],
            'publicly_queryable'  => true,
            'capability_type'     => 'page',
            'menu_icon' => 'dashicons-admin-home',

            'show_in_rest'          => true,
            'rest_controller_class' => 'WP_REST_Posts_Controller',
            'rest_base'             => 'property',
        );

        register_post_type('property', $args );
    }

    /**
     * Register ACF stuff.
     */
    private function registerAcfFields()
    {
        if (function_exists('acf_add_local_field_group')) acf_add_local_field_group(CustomFields::getFields());
    }

    private function registerAcfQuery()
    {
        // Repeater support
        add_filter( 'posts_where' , function ( $where ) {
            $where = str_replace("meta_key = 'group_rooms_$", "meta_key LIKE 'group_rooms_%", $where);
            return $where;
        });


        add_action('pre_get_posts', function ( $query ) {
            if( is_admin() ) return;
            if( !$query->is_main_query() ) return;

            $meta_query = $query->get('meta_query');

            $params = [];

            foreach( $GLOBALS['property_object_query_filters'] as $key => $name ) {
                if( empty($_GET[ $name ]) ) {
                    continue;
                }
                $value = explode(',', $_GET[ $name ]);

                // With fix PHP 7.X+ bug: Fatal error: Uncaught Error: [] operator not supported for strings
                // Do not append directly to $meta_query
                if (in_array($key, ['room_area','number_of_rooms','balcony','bathroom'])) {
                    $params[] = [
                        'key'		=> 'group_rooms_$_'.$name,
                        'value'		=> array_values($value),
                        'compare'	=> 'IN',
                    ];
                } else {
                    $params[] = [
                        'key'		=> $name,
                        'value'		=> array_values($value),
                        'compare'	=> 'IN',
                    ];
                }
            }
            $meta_query = array_filter(array_merge((array) $meta_query, $params));
            $query->set('meta_query', $meta_query);

            if( isset($query->query_vars['post_type']) && $query->query_vars['post_type'] == 'property' ) {
                $order_by  = isset($_GET['orderby']) && array_key_exists($_GET['orderby'], $GLOBALS['property_object_query_ordering_params']) ? $_GET['orderby'] : 'date';
                $order_dir = isset($_GET['order']) && in_array($_GET['order'], ['asc', 'desc']) ? $_GET['order'] : 'desc';

                $query->set('orderby', $order_by);
                $query->set('order', $order_dir);
            }

        }, 10, 1);
    }

    private function registerRestApi()
    {
        add_filter('rest_endpoints', function ($routes) {
            if (($route =& $routes['/wp/v2/property'])) {
                // Add environmental_friendliness ordering
                $route[0]['args']['orderby']['enum'][] = 'environmental_friendliness';
                $route[0]['args']['meta_key'] = array(
                    'description'       => 'The meta key to query.',
                    'type'              => 'string',
                    'enum'              => ['environmental_friendliness'],
                    'validate_callback' => 'rest_validate_request_arg',
                );
            }
            return $routes;
        });



        add_filter( 'rest_property_query', function( $args, $request ) {
            $params = [];
            foreach( $GLOBALS['property_object_query_filters'] as $key => $name ) {
                if( empty($_GET[ $name ]) ) {
                    continue;
                }
                $value = explode(',', $request->get_param($name));
                if (in_array($key, ['room_area','number_of_rooms','balcony','bathroom'])) {
                    $params[] = [
                        'key'		=> 'group_rooms_$_'.$name,
                        'value'		=> array_values($value),
                        'compare'	=> 'IN',
                    ];
                } else {
                    $params[] = [
                        'key'		=> $name,
                        'value'		=> array_values($value),
                        'compare'	=> 'IN',
                    ];
                }
            }
            $args['meta_query'] = array_filter($params);
            return $args;
        }, 10, 2 );
    }
}