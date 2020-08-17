<?php

class PropertyObjectWidget extends WP_Widget
{

    protected $visible = [
        'number_of_floors',
        'building_type',
        'environmental_friendliness',
        // 'room_area',
        'number_of_rooms',
        'balcony',
        'bathroom'
    ];

    // Main constructor
    public function __construct()
    {
        parent::__construct('properties_widget', __( 'Properties Widget', 'text_domain' ), []);


        add_filter('rest_endpoints', function ($routes) {
            // I'm modifying multiple types here, you won't need the loop if you're just doing posts
            foreach (['property'] as $type) {
                if (!($route =& $routes['/wp/v2/' . $type])) {
                    continue;
                }

                // Allow ordering by my meta value
                $route[0]['args']['orderby']['enum'][] = 'environmental_friendliness';

                // Allow only the meta keys that I want
                $route[0]['args']['meta_key'] = array(
                    'description'       => 'The meta key to query.',
                    'type'              => 'string',
                    'enum'              => ['environmental_friendliness'],
                    'validate_callback' => 'rest_validate_request_arg',
                );
            }

            return $routes;
        });

        // Enable Dashicons in WordPress Frontend
        add_action( 'wp_enqueue_scripts', function () {
            wp_enqueue_style( 'dashicons' );
        });
    }

    // The widget form (for the backend )
    public function form( $instance ) {
        $defaults = [
            'title' => '',
            'description' => '',
        ];

        extract( wp_parse_args( ( array ) $instance, $defaults ) );

        $out = '';
        $out.= '<p>';
        $out.= '<label for="'.esc_attr($this->get_field_id('title')).'">Title</label>';
        $out.= '<input class="widefat" type="text" id="'.esc_attr($this->get_field_id('title')).'" name="'.esc_attr($this->get_field_id('title')).'" value="'.esc_attr( $title ).'">';
        $out.= '</p>';
        $out.= '<p>';
        $out.= '<label for="'.esc_attr($this->get_field_id('description')).'">Description</label>';
        $out.= '<textarea class="widefat" id="'.esc_attr($this->get_field_id('description')).'" name="'.esc_attr($this->get_field_id('description')).'">'.wp_kses_post( $description ).'</textarea>';
        $out.= '</p>';

        echo $out;
    }

    // Update widget settings
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = isset( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
        $instance['description'] = isset( $new_instance['description'] ) ? wp_strip_all_tags( $new_instance['description'] ) : '';
        return $instance;
    }

    // Display the widget
    public function widget( $args, $instance )
    {
        extract( $args );
        $title       = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
        $description = isset( $instance['description'] ) ? $instance['description'] : '';
        ?>

        <div id="archive-filters">


            <div class="row">
                <div class="col-9">
                    <div class="filter form-group" data-filter="orderby">
                        <div class="filter-label"><?php echo _('Ordering'); ?></div>
                        <select class="form-control" name="orderby" id="acf-ordering">
                            <?php
                            $current = get_query_var('orderby', 'date');
                            foreach ($GLOBALS['property_object_query_ordering_params'] as $key => $text) {
                                echo '<option value="'.$key.'"'.($current == $key ? ' selected': '').'>'.$text.'</option>';
                            } ?>
                        </select>
                    </div>
                </div>
                <div class="col-3">
                    <div class="order-toggle-group">
                        <div class="filter form-group" data-filter="order">
                            <?php $current = get_query_var('order', 'desc'); ?>
                            <input id="order-asc" type="radio" name= "order" value="desc" class="order-toggle" <?php echo $current != 'asc' ? ' checked': '' ?>>
                            <label class="btn" for="order-desc"><span class="dashicons dashicons-arrow-up-alt"></span></label>
                            <input id="order-desc" type="radio" name= "order" value="asc" class="order-toggle" <?php echo $current != 'desc' ? ' checked': '' ?>>
                            <label class="btn" for="order-asc"><span class="dashicons dashicons-arrow-down-alt"></span></label>
                        </div>
                    </div>
                </div>
            </div>


            <?php
            foreach( $GLOBALS['property_object_query_filters'] as $key => $name ):

                if (!in_array($key, $this->visible)) continue;

                // get the field's settings without attempting to load a value
                $field = get_field_object($key, false, false);
                // set value if available
                if( isset($_GET[ $name ]) ) {
                    $field['value'] = explode(',', $_GET[ $name ]);
                }
                ?>

                <div class="filter mb-3 pb-3 border-bottom" data-filter="<?php echo $name; ?>">
                    <div class="filter-label"><?php echo $field['label']; ?></div>
                    <?php
                    $field_id = 'acf-' . $field['key'];

                    if ($field['type'] == 'true_false') {
                        $checked = isset($_GET[$name]) && $_GET[$name] == 1 ? ' checked' : '';
                        echo '<div class="form-check">';
                        echo '<label class="form-check-label" for="' . $field_id . '">';
                        echo '<input class="form-check-input" type="checkbox" id="' . $field_id . '" value="1"' .$checked.'>';
                        echo '&nbsp;' . _('Yes');
                        echo '</label>';
                        echo '</div>';
                    } else {
                        $current_values = $field['value'];
                        echo '<div class="row">';
                        foreach ($field['choices'] as $value => $text) {
                            $checked = is_array($current_values) && in_array($value, $current_values) ? ' checked' : '';
                            echo '<div class="col-4"><div class="form-check">';
                            echo '<label class="form-check-label" for="' . $field_id . '">';
                            echo '<input class="form-check-input" type="checkbox" id="' . $field_id . '" value="' . $value . '"' . $checked . '>';
                            echo '&nbsp;' . $text;
                            echo '</label>';
                            echo '</div></div>';
                        }
                        echo '</div>';
                    }
                    ?>
                </div>

            <?php endforeach; ?>
            <button class="btn btn-block" id="apply-filters" type="button"><?php _e('Show all') ?></button>
            <div id="property-preview"></div>
        </div>

        <script type="text/javascript">
            (function($) {

                var args = {};

                var renderQueryResults = function (data) {
                    var $container = $('#property-preview');
                    $container.html('<div class="mb-3"><?php _e("Preview") ?></div>');
                    $.each(data, function(i,e) {
                        var $item = $('<a class="card mb-3 preview-item" href="' + e.link + '"></a>');
                        $('<img class="card-img-top" src="' + e.acf.property_image + '">').appendTo($item);

                        var summary = $(e.content.rendered).text().substring(0,50) + '...';
                        $('<div class="card-body"><h3 class="h5">' + e.title.rendered + '</h3><div>'+summary+'</div></div>').appendTo($item);

                        $item.appendTo($container);
                    })
                }

                $('#apply-filters').click(function(e) {
                    e.preventDefault();
                    var url = '<?php echo home_url('property'); ?>';
                    url += '?';
                    $.each(args, function( name, value ){
                        if (value) url += name + '=' + value + '&';
                    });
                    url = url.slice(0, -1);
                    window.location.replace( url );
                });

                $('#archive-filters').on('change', 'input[type="checkbox"],input[type="radio"],select', function(){

                    $('#archive-filters .filter').each(function(){
                        var filter = $(this).data('filter')
                        var vals = [];
                        $(this).find('input:checked').each(function(){
                            vals.push( $(this).val() );
                        });
                        $(this).find('select option:selected').each(function(){
                            vals.push( $(this).val() );
                        });
                        args[ filter ] = vals.join(',');
                    });

                    jQuery.ajax({
                        type : 'get',
                        url : '<?php echo home_url("/wp-json/wp/v2/property") ?>',
                        data : Object.assign({}, args, {
                            action : 'properties',
                            per_page: 5,
                        }),
                        beforeSend: function() {
                            // $input.prop('disabled', true);
                            // $content.addClass('loading');
                        },
                        success : function( response ) {
                            // $input.prop('disabled', false);
                            console.log(response);
                            renderQueryResults(response);
                            // $content.removeClass('loading');
                            // $content.html( response );
                        }
                    });
                });
            })(jQuery);
        </script>


<?php
        // WordPress core before_widget hook (always include )
        echo $before_widget;
        echo '<div class="widget-text wp_widget_plugin_box">';
        if ( $title ) echo $before_title . $title . $after_title;
        echo '</div>';
        $fields = acf_get_fields('group_prop_object');
        echo $after_widget;
    }

}