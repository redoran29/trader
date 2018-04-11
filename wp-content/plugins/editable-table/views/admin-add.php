<h1>Add EDTB</h1>
<h4>Database Settings</h4>
<form id='edtb-form'>
<table class='form-table'>
<h3>Please ,&ensp;What shall be improve ? &ensp;   <a href="mailto:ze@18k.com.br">ze@18k.com.br</a>  or whattsApp +5511965140000 </h3>
	
	 * SQL TABLE MUST HAVE AUTO INCREMENT PRIMARY KEY </br>
	 * SIZE (type only numbers or leave Blank For Table automatic width 100% ) 
	 
<td>
   <strong>Edit Table</strong>&ensp;
   <select name='editable' id='editable'>  <option value="Yes">Yes</option>  <option value="Not">Not</option></select> &ensp;   <select name='Pagination' id='Pagination'>   <option value="Paging">Pagination</option>   <option value="Notpaging">Not Paging</option>   </select>
  
</td>
</tr>

</table>
</br>
<table class='column-table'>

<tr>
    <th>Table Name (in Database)&emsp;</th>
</tr>
<tr>
<td>
    <input type='text' name='tablename' id='tablename' size='40'/>
</td>
</tr>
<tr><td>&ensp;</td></tr>
<tr><th>&ensp;</th></tr>
<tr>
    <th>Column Name (in Database)&emsp;&emsp;</th>
    <th>Column Label (Frontend Label)</th>
    <th>Size(px)</th>
</tr>
<tr>
	<td>
	<input type='text' name='columns[0][name]' size='40'/>&emsp;</td><td><input type='text' name='columns[0][label]' size='40' /><td><input type='text' name='columns[0][size]' size='7' />
	</td>
</tr>

  <tr class='action-row'>
    <td colspan='2'></br><button type='button' class='add-column button'>Add Column</button></td>
 </tr>

</table>

<p class='submit'><button class='button submit-button'>Creat New EdtbTable & Save</button></p>
<p class='data-response'></p>
</form>

<?php
$rows = 10;
$page = isset($_GET['current'])?intval($_GET['current']):1;
$offset = ($page - 1) * $rows;
?>
<h1>EDTB</h1>
<div class='edtb-list'>
    <table class='widefat fixed'>
        <thead>
            <tr>
            <th style="" class="manage-column column-shortcode" id="shortcode" scope="col">SHORTCODE</th>
			<th width="800px" style="" class="manage-column column-contentfield" id="contentfield" scope="col">SETINGS</th>
            <th style="" class="manage-column column-actions" id="actions" scope="col"></th>
            </tr>
        </thead>
        <tbody>
        <?php
        $query = new WP_Query(array(
            'post_type'=>'edtb',
            'post_status'=>'publish',
            'post_count'=>$rows,
            'offset'=>$offset
            ));
        if ($query->have_posts()):
		while ($query->have_posts()): $query->the_post(); ?>
		<tr>
		<td>[edtbplugin id=<?php $idpost65=the_ID(); ?>] <input type="hidden" id="idpost" value="<?php the_ID();?>" />
		</td>
		<td contenteditable="true" id="contentedit">
		<?php
		$post_str65 = get_post($idpost65);
		$content65 = $post_str65->post_content ;
		echo $content65;?>
			</td>
            <td>
			<a class='button' id='Btedit'>Edit</a>
                <a class='button' href="<?php echo admin_url();?>?action=deleteadmla&idsias=<?php echo get_the_id();?>">Delete</a> 
            </td>
            </tr>
        <?php endwhile; wp_reset_postdata(); else: ?>
            <tr><td colspan='4'>No EDTB created yet.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<!-- End:: List -->


<script type='text/javascript'>

 var x="<?php cleanBlank(); ?>"; 
jQuery(document).ready(function ($) {
    var count = 1;
    $('.add-column').click(function () {
        var html = "<tr><td><input type='text' name='columns["+count+"][name]' size='40' />&emsp;</td><td><input type='text' name='columns["+count+"][label]'size='40' /></td><td><input type='text' name='columns["+count+"][size]'size='7' /></td></tr>";
        $(html).insertBefore('.column-table .action-row');
        count++;
    })
    $('#edtb-form').submit(function (e) {
        e.preventDefault();
       
        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php?action=save_new_edtb'); ?>',
            type:'post',
            data: $('#edtb-form').serialize(),
            dataType: 'json',
            beforeSend: function () {
                $('.data-response').empty()
                },
            success: function (resp) {
            
               
                if (resp.success) {					
                    alert("EDTB Added successfully");
                    $tr='<tr><td>'+resp.post_id+'</td><td>'+resp.title+'</td><td>'+resp.scode+'</td><td><a class="button" href="<?php echo admin_url();?>admin.php?action=deleteadmla&idsias='+resp.post_id+'">Delete</a> </td></tr>'
                    $('.widefat tbody').prepend($tr);
                    $('.data-response').html('EDTB Added successfully');					
                    $('#edtb-form')[0].reset()
					window.location=document.location.href;
				   
                } else {
                    var html = ''
                    $.each(resp.errors,function (i,e) {
                        html += '<p>' + e + '</p>'
                    })   
                    $('.data-response').html(html)
                }
            }
        });
        
        return false;
        });
	$('.edtb-list table tbody tr td #Btedit').each(function(){
    $(this).click(function (e) {
            e.preventDefault();
    
    var value = $(this).parent().prev().text();

    var id = $(this).parent().prev().prev().children("input").val();
   alert('S u c c e s s    edtbplugin id='+id);
        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php?action=atualiza_post_content'); ?>',
            type:'post',
            data:{value:value,id:id},
            dataType: 'json',     
            beforeSend: function () {
                $('.data-response').empty()
                },
            success: function (resp) {
                    if (resp.success) {         
                } 
            }
      
        });
        
        return false;   
    });
       });
})
</script>
<?php 
	
	
function cleanBlank(){
	
	global $wpdb;
    $query = "SELECT ID FROM $wpdb->posts where post_type ='edtb' && post_status !='trash' ORDER BY ID DESC LIMIT 0,1";	 
    $result = $wpdb->get_results($query);	 
    $row = $result[0];		
	if (isset($row)){ 		
    $idpost=$row->ID;	
    $post_str = get_post($idpost); 
	$content = $post_str->post_content ;   	
	$obj_post = json_decode($content,true);
	$cnt = 0;	                                    
	foreach ($obj_post[columns] as $item) {
	 if (strlen($obj_post[columns][$cnt][name])<1){
		unset($obj_post[columns][$cnt]);
	  }
	$cnt++;
     }	                                   
	 $obj_cleanBlank = json_encode($obj_post,true);	 
	  $post_clean = array(
		  'ID'           => $idpost,
		  'post_content' => $obj_cleanBlank,
	   );
	  wp_update_post( $post_clean );	  
	}	
}

?>

