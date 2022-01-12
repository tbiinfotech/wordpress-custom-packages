jQuery(document).ready(function() {
	jQuery(".date_start").each(function() {
        jQuery(this).datepicker({
            dateFormat: 'yy-mm-dd'
        });
    });
	automsgload_block();
    automsgload_sideblock();
    
	/*jQuery('.langOpt').multiselect({
		columns: 2,
		placeholder: 'Select Site'
	});*/
	jQuery('.langOpt').multiSelect();
	jQuery('.msg_send_btn').click(function(){
		jQuery('.loadingblock').show();
		var fd = new FormData();
        var files = jQuery('#filedata')[0].files[0];
        var write_msg = jQuery('#write_msg').val();
		var userid = jQuery('#getusreid').val();
        fd.append('file',files);
        fd.append('write_msg',write_msg);
        fd.append('trigger','write_msg');
        fd.append('userid',userid);
        fd.append('action','subchatting');
		jQuery.ajax({
			url : 'https://travel.safari.africa/wp-admin/admin-ajax.php',
			type: "POST",
			data: fd,
			contentType: false,
			processData: false,
			success : function( response ) { 
				jQuery('.loadingblock').hide();
				jQuery('#img').html('');
				jQuery('#filedata').val('');
				jQuery('#write_msg').val('');
				jQuery('.msg_history_main').html(response);
				var log = jQuery('.msg_history_main');
				log.animate({ scrollTop: log.prop('scrollHeight')}, 1000);
			}
		})
	})
	jQuery('.selectclient').on('change',function(){
		var uderid = jQuery(this).val();
		if(uderid!=''){
			window.location.href='https://travel.safari.africa/wp-admin/admin.php?page=client-chatting&userid='+uderid;
		}else{
			window.location.href='https://travel.safari.africa/wp-admin/admin.php?page=client-chatting';
		}
		
	})
	jQuery('.submitdeleteclient').click(function(event){
		var id = jQuery(this).attr('id');
		event.stopPropagation();
		if(confirm("Do you want to delete?")) {
			this.click;
			jQuery.ajax({
				url : 'https://travel.safari.africa/wp-admin/admin-ajax.php',
				type : 'post',
				data : {action:'deleteclient',userid:id},
				success : function( response ) {        
					window.location.href='https://travel.safari.africa/wp-admin/admin.php?page=Clients&status=delete';
				}
			});
		}
		else{
			return false;
		}       
		event.preventDefault();
	})
	jQuery('#client_id').on('change',function(event){
		var id = jQuery(this).val();
		jQuery.ajax({
			url : 'https://travel.safari.africa/wp-admin/admin-ajax.php',
			type : 'post',
			data : {action:'checkpackage',userid:id},
			success : function( response ) {        
				var res = jQuery.parseJSON(response);
				jQuery('#package').html(res.html);
			}
		});     
		event.preventDefault();
	})
	jQuery('.submitagentdelete').click(function(event){
		var id = jQuery(this).attr('id');
		event.stopPropagation();
		if(confirm("Do you want to delete?")) {
			this.click;
			jQuery.ajax({
				url : 'https://travel.safari.africa/wp-admin/admin-ajax.php',
				type : 'post',
				data : {action:'deleteagent',userid:id},
				success : function( response ) {        
					window.location.href='https://travel.safari.africa/wp-admin/admin.php?page=agents&status=delete';
				}
			});
		}
		else{
			return false;
		}       
		event.preventDefault();
	})
	jQuery('.submitdeletecustompackage').click(function(event){
		//alert();
		var id = jQuery(this).attr('id');
		var id1 = jQuery(this).attr('data_val');
		event.stopPropagation();
		if(confirm("Do you want to delete?")) {
			this.click;
			jQuery.ajax({
				url : 'https://travel.safari.africa/wp-admin/admin-ajax.php',
				type : 'post',
				data : {action:'deletecustompackage',userid:id1,packageid:id},
				success : function( response ) {        
					window.location.href='https://travel.safari.africa/wp-admin/admin.php?page=custom-packages&status=delete';
				}
			});
		}
		else{
			return false;
		}       
		event.preventDefault();
	})
	jQuery('#previewpackage').click(function(){
		var client_names = jQuery('#client_names').val();
		var client_email = jQuery('#client_email').val();
		var client_package = jQuery('#package').val();
		var trip_start_date = jQuery('#trip_start_date').val();
		var trip_duration = jQuery('#trip_duration').val();
		var no_adults = jQuery('#no_adults').val();
		var price_adult = jQuery('#price_adult').val();
		if(client_names!='' && client_email!='' && client_package!='' && trip_start_date!='' && trip_duration!='' && no_adults!='' && price_adult!=''){
			jQuery('.parsley-errors-list').hide();
			var formdata = jQuery('#createuser').serialize();
			jQuery.ajax({
				url : 'https://travel.safari.africa/wp-admin/admin-ajax.php',
				type : 'post',
				data : {action:'previewmode',formdata:formdata},
				dataType: "JSON",
				success : function( response ) {  
					//console.log(response);
					//return false;
					var url = 'https://travel.safari.africa/?preview_id='+response.userid;
					window.open(url,'_blank');					
					//window.location.href='https://travel.safari.africa/wp-admin/admin.php?page=Clients';
				}
			});
		}else{
			jQuery('#createuser').submit();
		}
	})
});
function automsgload_block(){
	var log = jQuery('.msg_history_main');
	log.animate({ scrollTop: log.prop('scrollHeight')}, 1000);
	var userid = jQuery('#getusreid').val();
	jQuery.ajax({
		url : 'https://travel.safari.africa/wp-admin/admin-ajax.php',
		type : 'post',
		data : {action:'add_to_chatting',userid:userid},
		success : function( response ) {        
			jQuery('.msg_history_main').html(response);
			
		}
	});
    setTimeout(automsgload_block, 5000);
}

function automsgload_sideblock(){
	//jQuery(".msg_history").animate({ scrollTop: jQuery(document).height() }, 1000);
    // do whatever you like here
	var userid = jQuery('#getusreid').val();
	jQuery.ajax({
		url : 'https://travel.safari.africa/wp-admin/admin-ajax.php',
		type : 'post',
		data : {action:'add_to_sideblock',userid:userid},
		success : function( response ) {        
			jQuery('.downloads-wrapper1').html(response);
			
		}
	});
    setTimeout(automsgload_sideblock, 5000);
}

function readImage(input) {
    if ( input.files && input.files[0] ) {
		var extension = input.files[0].name.split('.').pop().toLowerCase();
		var FR= new FileReader();
        FR.onload = function(e) {
			if(extension=='docx'){
				 jQuery('#img').html('<i class="fa fa-file-word-o text-primary" style="font-size:40px"></i>');
			}else if(extension=='xlsx'){
				 jQuery('#img').html('<i class="fa fa-file-excel-o text-success" style="font-size:40px"></i>');
			}else if(extension=='pptx'){
				 jQuery('#img').html('<i class="fa fa fa-file-powerpoint-o text-danger" style="font-size:40px"></i>');
			}else if(extension=='pdf'){
				 jQuery('#img').html('<i class="fa fa-file-pdf-o text-danger" style="font-size:40px"></i>');
			}else if(extension=='zip'){
				 jQuery('#img').html('<i class="fa fa-file-archive-o text-muted" style="font-size:40px"></i>');
			}else if(extension=='doc'){
				 jQuery('#img').html('<i class="fa fa-file-word-o text-primary" style="font-size:40px"></i>');
			}else if(extension=='xls'){
				 jQuery('#img').html('<i class="fa fa-file-excel-o text-success" style="font-size:40px"></i>');
			}else if(extension=='csv'){
				 jQuery('#img').html('<i class="fa fa-file-excel-o text-success" style="font-size:40px"></i>');
			}else if(extension=='ppt'){
				 jQuery('#img').html('<i class="fa fa-file-powerpoint-o text-danger" style="font-size:40px"></i>');
			}else if(extension=='htm'){
				 jQuery('#img').html('<i class="fa fa-file-code-o" aria-hidden="true" style="font-size:40px"></i>');
			}else if(extension=='txt'){
				 jQuery('#img').html('<i class="fa fa-file-text-o text-info" style="font-size:40px"></i>');
			}else if(extension=='mov'){
				 jQuery('#img').html('<i class="fa fa-file-movie-o text-warning" style="font-size:40px"></i>');
			}else if(extension=='mp3'){
				 jQuery('#img').html('<i class="fa fa-file-audio-o text-warning" style="font-size:40px"></i>');
			}else if(extension=='mp4'){
				 jQuery('#img').html('<i class="fa fa-file-video-o text-warning" style="font-size:40px"></i>');
			}else{
				  jQuery('#img').html('<img src="'+e.target.result+'" alt="uploadimage" style="width:40px;max-width:40px;"/>');
			}
	    };       
        FR.readAsDataURL( input.files[0] );
    }
}
