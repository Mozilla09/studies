<!doctype html>
<html>

<head>
    <title>Шаблон</title>

    <?php wp_head(); ?>
</head>

<body>
    <header>
        <div class="topmenu">
        <?php 
$args = array(
	'theme_location' => 'primary_menu'
);
wp_nav_menu($args);
?>
        </div>
    </header>