<?php
//Получить ID текущей страницы
$pageid = get_the_ID();

$title = get_post_meta($pageid, 'banner_title', true );
$style = get_post_meta($pageid, 'banner_style', true );
$image = get_post_meta($pageid, 'banner_image', true );

    if ($image) {

        $imagearray = wp_get_attachment_image_src($image, 'full');

          $imageurl = $imagearray[0];
    }

?>


<div class="banner <?php echo $style; ?>" id="" style="background-image:url(<?php echo $imageurl; ?>)">
<div class="substrate">
<h1>Заголовок - <?php echo $title; ?></h1>


</div>
</div>