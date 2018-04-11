<?php
/**
 * Страница с кастомным шаблоном (page-custom.php)
 * @package WordPress
 * @subpackage your-clean-template-3
 * Template Name: Страница с кастомным шаблоном
 */
get_header();
global $wpdb;
?>
<section>
	<div class="container">
		<div class="row">

            <?php require_once ('blocks/api.php'); ?>
            <!--
            Введите цены на рынке (через запятую): <input type="text" name="price" value="" id="price"><br><br>
                <!--Введите период скользящей стредней: <input type="text" name="period" value="" id="period"><br><br>
                <button id="send">Отправить данные</button><br><br>-->
<!--            <div id="loading" style="display: none">Загрузка</div>-->
<!--            <div id="data"></div>-->
		</div>
	</div>
</section>
<?php get_footer(); ?>