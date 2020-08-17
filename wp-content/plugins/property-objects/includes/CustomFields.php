<?php


namespace Property;


class CustomFields
{
    const POST_TYPE = 'property';

    public static function getFields()
    {
        // 7. мульти-блок-полей "помещение", где будут такие поля как
        $room_fields = [
            // 1. "площадь (input)"
            [
                'key' => 'room_area',
                'label' => 'Area',
                'name' => 'room_area',
                'type' => 'number',
                'instructions' => '',
                'required' => true,
                'conditional_logic' => 0,
                'wrapper' => [
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ],
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
            ],
            // 2. "кол.комнат (1-10, radio)"
            [
                'key' => 'number_of_rooms',
                'label' => 'Number of rooms',
                'name' => 'number_of_rooms',
                'type' => 'radio',
                'required' => true,
                'choices' => array_combine(range(1,10), range(1,10)),
                'other_choice' => false,
                'save_other_choice' => false,
                'layout' => 0,
            ],
            // 3. "балкон (да/нет, radio)"

            [
                'key' => 'balcony',
                'label' => 'Balcony',
                'name' => 'balcony',
                'type' => 'true_false',
                'required' => true,
            ],
            // 4. "санузел (да/нет, radio)"
            [
                'key' => 'bathroom',
                'label' => 'Bathroom',
                'name' => 'bathroom',
                'type' => 'true_false',
                'required' => true,
            ],
            // 5. "изображение".
            [
                'key' => 'room_image',
                'label' => 'Image',
                'name' => 'room_image',
                'type' => 'image',
                'required' => true,
                'return_format' => 'url',
                'preview_size' => 'thumbnail',
                'library' => 'all',
                'min_width' => 0,
                'min_height' => 0,
                'min_size' => 0,
                'max_width' => 0,
                'max_height' => 0,
                'max_size' => 10,
                'mime_types' => '',
            ],
        ];

        return [
            'key' => 'group_prop_object',
            'title' => 'Property fields',
            'fields' => [
                // 1. "название дома (input)"
                [
                    'key' => 'house_number',
                    'label' => 'House number',
                    'name' => 'house_number',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => true,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ],

                // 2. "координаты местонахождения (input)"
                [
                    'key' => 'location_gps',
                    'label' => 'Location',
                    'name' => 'location_gps',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => true,
                    'conditional_logic' => 0,
                    'wrapper' => [
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ],
                    'center_lat' => '',
                    'center_lng' => '',
                    'zoom' => '',
                    'height' => '',
                ],

                // 3. "количество этажей (1-20, list)",
                [
                    'key' => 'number_of_floors',
                    'label' => 'Number of floors',
                    'name' => 'number_of_floors',
                    'type' => 'select',
                    'instructions' => '',
                    'required' => true,
                    'choices' => array_combine(range(1,20), range(1,20)),
                    'allow_null' => false,
                    'multiple' => false,
                    'ui' => 0,
                    'ajax' => 0,
                    'placeholder' => 'Select...',
                ],


                // 4. "тип строения (панель/кирпич/пеноблок, radio)"
                [
                    'key' => 'building_type',
                    'label' => 'Building type',
                    'name' => 'building_type',
                    'type' => 'radio',
                    'instructions' => '',
                    'required' => true,
                    'choices' => [
                        'panel' => 'Panel',
                        'brick' => 'Brick',
                        'block' => 'Block',
                    ],
                    'other_choice' => false,
                    'save_other_choice' => false,
                    'layout' => 0,
                ],

                // 5. "экологичность" (1-5, list)
                [
                    'key' => 'environmental_friendliness',
                    'label' => 'Environmental friendliness',
                    'name' => 'environmental_friendliness',
                    'type' => 'select',
                    'required' => true,
                    'choices' => array_combine(range(1,5), range(1,5)),
                    'allow_null' => false,
                    'multiple' => false,
                    'ui' => false,
                    'ajax' => false,
                    'placeholder' => 'Select...',
                ],

                // 6. "изображение"
                [
                    'key' => 'property_image',
                    'label' => 'Image',
                    'name' => 'property_image',
                    'type' => 'image',
                    'required' => true,
                    /* (string) Specify the type of value returned by get_field(). Defaults to 'array'.
                    Choices of 'array' (Image Array), 'url' (Image URL) or 'id' (Image ID) */
                    'return_format' => 'url',
                    'preview_size' => 'thumbnail',
                    'library' => 'all',
                    'min_width' => 0,
                    'min_height' => 0,
                    'min_size' => 0,
                    'max_width' => 0,
                    'max_height' => 0,
                    'max_size' => 10,
                    'mime_types' => '',
                ],

                [
                    'key' => 'group_rooms',
                    'label' => 'Rooms',
                    'name' => 'group_rooms',
                    'type' => 'repeater',
                    'layout' => 'block',
                    'button_label' => '',
                    'sub_fields' => $room_fields
                ],
            ],
            'location' => [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => self::POST_TYPE,
                    ],
                ],
            ],
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
        ];
    }
}