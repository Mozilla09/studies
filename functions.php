<?php

   // правильный способ подключить стили и скрипты
   add_action( 'wp_enqueue_scripts', 'theme_name_scripts' );
   // add_action('wp_print_styles', 'theme_name_scripts'); // можно использовать этот хук он более поздний
   function theme_name_scripts() {
       wp_enqueue_style( 'main', get_template_directory_uri() . '/css/main.css', array(), filemtime(get_template_directory() . '/css/main.css'),'all');
       //wp_enqueue_script( 'script-name', get_template_directory_uri() . '/js/example.js', array(), '1.0.0', true );
   } 

//Функция регистрирует меню
function mytheme_register_nav_menu()
{
    register_nav_menus(array(
        'primary_menu' => __('Primary Menu', 'text_domain'),
        'footer_menu'  => __('Footer Menu', 'text_domain'),
    ));
}

//Добавляет функцию в https://wp-kama.ru/hook/add_meta_boxes
add_action('add_meta_boxes', 'mytheme_add_custom_box');

//Функция отображает meta_box для указаных в переменой screens типов страниц
function mytheme_add_custom_box()
{
    $screens = array('post', 'page');
    add_meta_box('mytheme_banner', 'Баннер', 'banner_metabox', $screens);
}


// HTML код блока картинки
function banner_metabox($post, $meta)
{
    $screens = $meta['args'];

    // значение поля BANNER_IMAGE - ID картинки сохраненный в БД
    $attachment_id = get_post_meta($post->ID, 'banner_image', true);
    // значение поля BANNER_TITLE
    $title = get_post_meta($post->ID, 'banner_title', true);

  	$style = get_post_meta($post->ID, 'banner_style', true);
  
    // Поля формы для введения данных
    echo '<label for="mytheme_banner_titel">Заголовок</label>';
    echo '<input type="text" id="mytheme_banner_titel" name="mytheme_banner_titel" value="' . $title . '" size="25" />';
  
    echo '<br><br><label for="mytheme_banner_style">Стиль</label>';
    echo '<select type="file" id="mytheme_banner_style" name="mytheme_banner_style">';

  	$selectoptions = array('dark','light');
  	foreach ($selectoptions as $option) {
      $selected = '';
      if ($option == $style) $selected = 'selected';
      echo '<option '.$selected.' value="'.$option.'">'.$option.'</option>';
    }
  	echo '</select>';

    echo '<br><br><label for="mytheme_banner_img">Картинка</label>';
    echo '<input type="file" id="mytheme_banner_img" name="mytheme_banner_img"/>';

    if ($attachment_id) {
        error_log($attachment_id);
        $imagearray = wp_get_attachment_image_src($attachment_id, 'full');

        error_log(print_r($imagearray, true));
        $imageurl = $imagearray[0];

        echo '<img loading="lazy" src="' . $imageurl . '">';
    }
}


//Обработка и сохранение введеных пользователем полей
add_action('save_post', 'banner_save');

function banner_save($post_id)
{

    // если это автосохранение ничего не делаем
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

    // проверяем права юзера
    if (!current_user_can('edit_post', $post_id))
        return;


    if (array_key_exists('mytheme_banner_titel', $_POST)) {
        // Все ОК. Теперь, нужно найти и сохранить данные
        // Очищаем значение поля input.
        $my_data = sanitize_text_field($_POST['mytheme_banner_titel']);

        // Обновляем данные в базе данных.
        update_post_meta($post_id, 'banner_title', $my_data);
    }
   if (array_key_exists('mytheme_banner_style', $_POST)) {
        // Все ОК. Теперь, нужно найти и сохранить данные
        // Очищаем значение поля input.
        $my_data = sanitize_text_field($_POST['mytheme_banner_style']);

        // Обновляем данные в базе данных.
        update_post_meta($post_id, 'banner_style', $my_data);
    }
    if (array_key_exists('mytheme_banner_img', $_FILES)) {

        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }
        

        // Все ОК. Теперь, нужно найти и сохранить данные
        // Очищаем значение поля input.

        $file = $_FILES['mytheme_banner_img'];
        $overrides = array('test_form' => FALSE);
        $my_image = wp_handle_upload($file, $overrides);
        $upload_dir = wp_get_upload_dir();

        if ($my_image && empty($my_image['error'])) {

            $attachment = array(
                'guid'           => $my_image['url'],
                'post_mime_type' => $my_image['type'],
                'post_title'     => sanitize_text_field(basename($my_image['url'])),
                'post_content'   => '',
                'post_status'    => 'inherit',
                'post_parent'    => $post_id,
            );

            $attachment_id = wp_insert_attachment($attachment, $upload_dir['path'] .'/'. basename($my_image['url']), $post_id);

            if (is_wp_error($attachment_id)) {
                unlink($my_image['file']);
            } else {
            $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload_dir['path'] .'/'. basename($my_image['url']));
            wp_update_attachment_metadata($attachment_id,  $attachment_data);
            }
            // Обновляем данные в базе данных.
            update_post_meta($post_id, 'banner_image', $attachment_id);
        } else {
        }
    }
}
