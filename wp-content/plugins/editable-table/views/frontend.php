<?php if (!isset($id)) $id = 0; ?>
<style> 
    #secondary { display: none; }
    #primary { width: 100%; }
    
    .save-row { display: none; }
    .save-row.active { display: table-row; }
	.table-hover thead tr:hover th, .table-hover tbody tr:hover td { background-color: #c3cfdb; } 
    .hover-buttons {
        position:absolute;
        top: -10px;
        left: 0px;
        display:none;
        }
    tr.selected .hover-buttons {
        display:block;    
    }
    .table tbody td {
        position:relative;
    }
    .table .focused {
        outline: 2px solid #ccc;
        background: #ddd;
    }
</style>
 
<div class='table-containertb'>
    <div align="right">
     <button type="button" name="add" id="add" class="btn btn-info" style='display:none;'><span class="glyphicon glyphicon-plus"></span> New</button>
    <button type='button' id='form-delete' class='btn btn-danger' style='display:none;'>Delete</button>
    </div>
    <br />
    <div id="alert_message<?php echo $id; ?>"></div>
	
	  <?php $i35=0; $colsize36=0; 
	  foreach($this->config['columns_front'] as $column) :
      $colsize36 += $this->config['columns_size'][$i35];		                			 
      $i35++; endforeach;
	  
	  if ($colsize36 < 1) {$colsize36="100%;";}
	  else { $colsize36 .= "px;" ;}	  
	  ?>
				   
    <table id="user_data<?php echo $id; ?>" class="table table-bordered table-striped table-hover" style="width:<?php echo intval($colsize36);?> table-layout:fixed;">
     <thead>
      <tr>
        <?php foreach($this->config['columns_front'] as $column) : ?>
		 <th> <?php echo esc_html($column); ?> </th>		
        <?php endforeach; ?>
      </tr>
     </thead>
    </table>
</div>
<script type='text/javascript'>
jQuery(document).ready( function ($) {

    var table = $('#user_data<?php echo $id; ?>').dataTable({
        "processing" :true,
         <?php if ($this->config['Pagination']=="Notpaging"){?>		'paging':false, <?php }else {?>	'paging':true, <?php }?>
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    	 "searching": false,
    	"info": false,
        "order" : [0, 'asc'],
        'ajax':'<?php echo admin_url('admin-ajax.php?action=edtb_fetch&edtb='.$id); ?>',
        "columnDefs": [
            {
                "render": function ( data, type, row ) {
                   return '<div class="hover-buttons"><div class="button-group"><button class="btn btn-default btn-xs btn-hv-delete"><span class="glyphicon glyphicon-trash"></span> Delete</button></div></div>' + data;
                },
                "targets": 0},
				<?php 
				if ($colsize36 !== "100%;"){
				$i63=0;  foreach($this->config['columns_front'] as $column) :
				$colsize62 = esc_html($this->config['columns_size'][$i63]);?>				                			 
				{"width":"<?php echo $colsize62;?>"+"px" , "targets": <?php echo $i63;?> },
				<?php $i63++; endforeach; }?>
				 ],
				
        'drawCallback': function () {

            var html = '<tr class="new-row">'
           
            <?php foreach ($this->config['columns'] as $idx=>$column): ?>
			
			<?php if ($this->config['edtble']!="Not"){?>
			html+='<td class="new <?php echo $idx==0?'new-placeholder':''?>" contenteditable data-column="<?php echo $column?>"><?php echo $idx==0?'+New':''?></td>'
            <?php  } endforeach; ?>
            html+= '</tr>'
            html+= '<tr class="save-row"><td colspan="<?php echo count($this->config['columns_front'])?>"><button class="save-row-btn">Save and Sort</button></td></tr>'
            $('#user_data'+<?php echo $id; ?>).find('tbody').append(html);
            
            var tr = window.tr<?php echo $id; ?>,td = window.td<?php echo $id; ?>
            
            if ($('#user_data<?php echo $id; ?>').find('tr:eq('+tr+')').find('td:eq('+(td+1)+')').length) {
				$('#user_data<?php echo $id; ?>').find('tr:eq('+tr+')').find('td:eq('+(td+1)+')')
            } else {
			    $('#user_data<?php echo $id; ?>').find('tr:eq('+(tr+1)+')').find('td:eq(0)')
                console.log(tr+1)
            }
            }
			
        })

		
  <?php if ($this->config['edtble']!="Not"){?>
    window.dt<?php echo $id; ?> = table;
        
    $('#user_data<?php echo $id; ?>').on('focusout','.update',function (e) {
        e.preventDefault()
        var value = $(this).text().trim(),id=$(this).attr('data-id'),column=$(this).attr('data-column'),el=$(this)
        if (value != $(this).data('prev-value')) {
         window.getSelection().removeAllRanges();
         $.ajax({
            url: '<?php echo admin_url('admin-ajax.php?action=edtb_update&edtb='.$id); ?>',
            type:'post',
            data: {id:id,value:value,column:column},
            success: function (response) {
                $('#alert_message<?php echo $id; ?>').text(response)
                setTimeout(function () {
                    $('#alert_message<?php echo $id; ?>').text('')
                    $('#user_data_processing<?php echo $id; ?>').css('display','none')
                    },5000)
                e.preventDefault()
                window.td<?php echo $id; ?> = el.closest('td')[0].cellIndex;
                window.tr<?php echo $id; ?> = el.closest('tr')[0].rowIndex;
                
                var tr = window.tr<?php echo $id; ?>,td = window.td<?php echo $id; ?>
            
                if ($('#user_data<?php echo $id; ?>').find('tr:eq('+tr+')').find('td:eq('+(td+1)+')').length) {
				    $('#user_data<?php echo $id; ?>').find('tr:eq('+tr+')').find('td:eq('+(td+1)+')')
                } else {
					$('#user_data<?php echo $id; ?>').find('tr:eq('+(tr+1)+')').find('td:eq(0)')
                    console.log(tr+1)
                }
                window.getSelection().removeAllRanges();
                table.api().ajax.reload()
                }
            })   
        }
        })
        .on('focusin','.new',function () {
            $(this).closest('table').find('.save-row').addClass('active')
            }).on('focusout','.new',function () {
                if ($(this).closest('.new-row').find('.new:first').text().trim() == '') {
                     $(this).closest('table').find('.save-row').removeClass('active')
                }
            }).on('click','.save-row-btn',function () {
                if ($('.new[data-column=<?php echo $this->config['columns'][0]; ?>]').text().trim() != '' || $('.new[data-column=<?php echo $this->config['columns'][0]; ?>]').text().trim() != '+New') {
                    var data = {
                        <?php foreach($this->config['columns'] as $col): ?>
                        '<?php echo $col; ?>':$(this).closest("table").find('.new[data-column=<?php echo $col; ?>]').text().trim(),
                        <?php endforeach; ?>
                        }
                        
                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php?action=edtb_insert&edtb='.$id); ?>',
                        type:'post',
                        data:data,
                        success: function (response) {
                            $('#alert_message<?php echo $id; ?>').text(response)
                            setTimeout(function () {
                                $('#alert_message<?php echo $id; ?>').text('')
                                $('#user_data_processing<?php echo $id; ?>').css('display','none')
                                },5000)
                            table.api().ajax.reload()
                            }
                        })
                }
            }).on('click','tr:not(.new-row):not(.save-row)',function () {
                $('tr.selected').removeClass('selected')
                $(this).addClass('selected')
            }).on('click','.btn-hv-delete',function () {
                var id = $(this).closest('tr').find('.update').attr('data-id')
                if (confirm('Are you sure you want to delete this row?')) {
                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php?action=edtb_delete&edtb='.$id); ?>',
                        type:'post',
                        data:{id:id},
                        success: function (response) {
                            $('#alert_message<?php echo $id; ?>').text(response)
                            setTimeout(function () {
                                $('#alert_message<?php echo $id; ?>').text('')
                                $('#user_data_processing<?php echo $id; ?>').css('display','none')
                                },5000)
                            table.api().ajax.reload()
                            }
                    })    
                }
            }).on('focusin','.new-placeholder',function() {
                if ($(this).text().trim() == '+New') $(this).text('')    
            }).on('focusout','.new-placeholder',function () {
                if ($(this).text().trim() == '') $(this).text('+New')    
            }).on('focusin','.update',function () {
                $(this).data('prev-value',$(this).text().trim())    
            })

    <?php } ?>		// end not editable here		
			
    })
    
</script>