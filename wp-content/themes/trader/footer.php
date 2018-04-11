<?php
/**
 * Шаблон подвала (footer.php)
 * @package WordPress
 * @subpackage your-clean-template-3
 */
?>
	<footer>
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<?php $args = array(
						'theme_location' => 'bottom',
						'container'=> false,
						'menu_class' => 'nav nav-pills bottom-menu',
				  		'menu_id' => 'bottom-nav',
				  		'fallback_cb' => false
				  	);
					wp_nav_menu($args);
					?>
				</div>
			</div>
		</div>
	</footer>
<script>
    var ajaxurl = '<?php echo site_url() ?>/wp-admin/admin-ajax.php';
    jQuery(function($){
            var data = {
                'action': 'action'
            };
            $.ajax({
                url:ajaxurl,
                data:data,
                type:'POST',
                beforeSend: function() {
                    $("#loading").show();
                },
                success:function(data){
                        $("#data").html(data);
                        $("#loading").remove();
                }
            });
    });
</script>
<?php wp_footer(); ?>
</body>
</html>
