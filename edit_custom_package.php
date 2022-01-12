<?php 
	require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	require_once(ABSPATH . "wp-admin" . '/includes/file.php');
	require_once(ABSPATH . "wp-admin" . '/includes/media.php');

	global $wpdb;
	
	$query = new WP_Query( array(
		'post_type' => 'package',
	) );
	$package_id = isset($_REQUEST['package_id'])?$_REQUEST['package_id']:'';
	$client_id = isset($_REQUEST['client_id'])?$_REQUEST['client_id']:'';
	$packagearray = array();
	while ( $query->have_posts() ) : $query->the_post(); 
		$packids = get_the_ID();
		if($packids==$package_id){
			$package_name = get_the_title();
			$package_content = get_the_content();
			break;
		}
	endwhile;
	
	/*included*/
	
	$results = $wpdb->get_results("SELECT * FROM table_name where package_id='" . $package_id . "' and client_id='". $client_id ."' and meta_key LIKE '%price_not_include%'");

	$price_not_include_res_array = array();
	$i=0;
	foreach($results as $k => $price_not_includes){
		$price_not_include_res = explode('_',$price_not_includes->meta_key);
		if(count($price_not_include_res)==5){
			$price_not_include_res_array[] = $price_not_includes;
			$i++;
		}
		
	}
	$results = $wpdb->get_results("SELECT * FROM table_name where package_id='" . $package_id . "' and client_id='". $client_id ."' and meta_key LIKE '%price_include%'");

	$price_include_res_array = array();
	$i=0;
	foreach($results as $k => $price_includes){
		$price_include_res = explode('_',$price_includes->meta_key);
		if(count($price_include_res)==4){
			$price_include_res_array[] = $price_includes;
			$i++;
		}
		
	}
	
	/*download*/
	
	$results = $wpdb->get_results("SELECT * FROM table_name where package_id='" . $package_id . "' and client_id='". $client_id ."' and meta_key LIKE '%download%'");
	$download_res_array = array();
	$i=0;
	foreach($results as $k => $download){
		$download_res = explode('_',$download->meta_key);
		if($download_res[0]!=''){
			$download_res_array[] = $download;
		}
	}
	$downloadicon = $downloadtitle = $downloadlink = array();
	foreach($download_res_array as $k => $download){
		$download_res = explode('_',$download->meta_key);
		if(end($download_res)=='icon'){
			$downloadicon[] = $download;
		}
		if(end($download_res)=='title'){
			$downloadtitle[] = $download;
		}
		if(end($download_res)=='link'){
			$downloadlink[] = $download;
		}
	}
	
	/*transport*/
	
	$results = $wpdb->get_results("SELECT * FROM table_name where package_id='" . $package_id . "' and client_id='". $client_id ."' and meta_key LIKE '%your_movements%'");
	$your_movements_res_array = array();
	foreach($results as $k => $transport){
		$transport_res = explode('your_movements_',$transport->meta_key);
		$transport_res2 = explode('_pick_up_',$transport->meta_key);
		if($transport_res[0]==''){
			$transport_res1 = explode('_',$transport_res[1]);
			$your_movements_res_array[] = $transport_res1[0][0];
		}
		if($transport_res2[1]!=''){
			$transport_res3 = explode('_',$transport_res2[1]);
			$your_pick_up_res_array[] = $transport_res3[0][0];
			
		}
	}
	$unique = array_keys(array_flip($your_movements_res_array));
	$unique1 = array_keys(array_flip($your_pick_up_res_array));
	$newarray = array();
	foreach($unique as $j => $val){
		foreach($results as $k => $transport){
			$transport_res = explode('your_movements_',$transport->meta_key);
			if($transport_res[0]==''){
				$transport_res1 = explode('_',$transport_res[1]);
				if($j==$transport_res1[0][0]){
					$pickup = explode('_pick_up_',$transport->meta_key);
					if($pickup[1]!=''){
						$pickup1 = explode('_',$pickup[1]);
						
						foreach($unique1 as $key => $valdata){
							if($key==$pickup1[0]){
								$your_movements_res = explode('_',$transport->meta_key);
								if(end($your_movements_res)=='icon'){
									$newarray[$j]['your_movements']['pickup'][$key]['icon']=$transport;
								}
								if(end($your_movements_res)=='title'){
									$newarray[$j]['your_movements']['pickup'][$key]['title']=$transport;
								}
								if(end($your_movements_res)=='description'){
									$newarray[$j]['your_movements']['pickup'][$key]['description']=$transport;
								}
							}
						}
					}else{
						$your_movements_res = explode('_',$transport->meta_key);
						if(end($your_movements_res)=='label'){
							$reverse = array_reverse( $your_movements_res );
							$z = $reverse[1];
							if($z=='day'){
								$newarray[$j]['your_movements']['day_label'] = $transport;
							}
							if($z=='time'){
								$newarray[$j]['your_movements']['time_label'] = $transport;
							}
						}
					}
					
				}
			}
		}
	}
	
	/*Activities*/
	
	$results = $wpdb->get_results("SELECT * FROM table_name where package_id='" . $package_id . "' and client_id='". $client_id ."' and meta_key LIKE '%your_activities%'");
	foreach($results as $k => $activity){
		$activity_res = explode('your_activities_',$activity->meta_key);
		$activity_res2 = explode('_arrival_data_',$activity->meta_key);
		if($activity_res[0]==''){
			$activity_res1 = explode('_',$activity_res[1]);
			$your_activities__res_array[] = $activity_res1[0][0];
		}
		if($activity_res2[1]!=''){
			$activity_res3 = explode('_',$activity_res2[1]);
			$your_arrival_data_res_array[] = $activity_res3[0][0];
			
		}
	}
	$unique = array_keys(array_flip($your_activities__res_array));
	$unique1 = array_keys(array_flip($your_arrival_data_res_array));
	foreach($unique as $j => $val){
		foreach($results as $k => $activity){
			$activity_res = explode('your_activities_',$activity->meta_key);
			if($activity_res[0]==''){
				$activity_res1 = explode('_',$activity_res[1]);
				if($j==$activity_res1[0][0]){
					$arrivaldata = explode('_arrival_data_',$activity->meta_key);
					if($arrivaldata[1]!=''){
						$arrivaldata1 = explode('_',$arrivaldata[1]);
						
						foreach($unique1 as $key => $valdata){
							if($key==$arrivaldata1[0]){
								$activities_res = explode('_',$activity->meta_key);
								if(end($activities_res)=='title'){
									$newarray1[$j]['your_activities']['arrival_data'][$key]['title']=$activity;
								}
								if(end($activities_res)=='meal'){
									$newarray1[$j]['your_activities']['arrival_data'][$key]['meal']=$activity;
								}
								if(end($activities_res)=='label'){
									$reverse = array_reverse( $activities_res );
									$z = $reverse[1];
									if($z=='button'){
										$newarray1[$j]['your_activities']['arrival_data'][$key]['button']=$activity;
									}
								}
								if(end($activities_res)=='link'){
									$newarray1[$j]['your_activities']['arrival_data'][$key]['link']=$activity;
								}
							}
						}
					}else{
						$activities_res = explode('_',$activity->meta_key);
						if(end($activities_res)=='label'){
							$reverse = array_reverse( $activities_res );
							$z = $reverse[1];
							if($z=='day'){
								$newarray1[$j]['your_activities']['day'] = $activity;
							}
							if($z=='time'){
								$newarray1[$j]['your_activities']['time'] = $activity;
							}
							if($z=='summary'){
								$newarray1[$j]['your_activities']['summary'] = $activity;
							}
						}
						if(end($activities_res)=='gallery'){
							$newarray1[$j]['your_activities']['gallery'] = $activity;
						}
					}
				}
			}
		}
	}
	
	/*itinerary*/
	
	$results = $wpdb->get_results("SELECT * FROM table_name where package_id='" . $package_id . "' and client_id='". $client_id ."' and meta_key LIKE '%day_by_day_itinerary%'");
	foreach($results as $k => $itinerary){
		$itinerary_res = explode('day_by_day_itinerary_',$itinerary->meta_key);
		$itinerary_res2 = explode('_button_block_',$itinerary->meta_key);
		if($itinerary_res[0]==''){
			$itinerary_res1 = explode('_',$itinerary_res[1]);
			$day_by_day_itinerary_res_array[] = $itinerary_res1[0][0];
		}
		if($itinerary_res2[1]!=''){
			$itinerary_res3 = explode('_',$itinerary_res2[1]);
			$button_block_data_res_array[] = $itinerary_res3[0][0];
			
		}
	}
	$unique = array_keys(array_flip($day_by_day_itinerary_res_array));
	$unique1 = array_keys(array_flip($button_block_data_res_array));
	foreach($unique as $j => $val){
		foreach($results as $k => $itinerary){
			$itinerary_res = explode('day_by_day_itinerary_',$itinerary->meta_key);
			if($itinerary_res[0]==''){
				$itinerary_res1 = explode('_',$itinerary_res[1]);
				if($j==$itinerary_res1[0][0]){
					$buttonblock = explode('_button_block_',$itinerary->meta_key);
					if($buttonblock[1]!=''){
						$buttonblock1 = explode('_',$buttonblock[1]);
						
						foreach($unique1 as $key => $valdata){
							if($key==$buttonblock1[0]){
								$activities_res = explode('_',$itinerary->meta_key);
								if(end($activities_res)=='label'){
									$reverse = array_reverse( $activities_res );
									$z = $reverse[1];
									if($z=='button'){
										$newarray2[$j]['day_by_day_itinerary']['buttonblock'][$key]['label'] = $itinerary;
									}
								}
								if(end($activities_res)=='link'){
									$newarray2[$j]['day_by_day_itinerary']['buttonblock'][$key]['link'] = $itinerary;
								}
							}
						} 
					}else{
						$itinerary_res = explode('_',$itinerary->meta_key);
						if(end($itinerary_res)=='label'){
							$reverse = array_reverse( $itinerary_res );
							$z = $reverse[1];
							if($z=='day'){
								$newarray2[$j]['day_by_day_itinerary']['day'] = $itinerary;
							}
							if($z=='time'){
								$newarray2[$j]['day_by_day_itinerary']['time'] = $itinerary;
							}
						}
						if(end($itinerary_res)=='locations'){
							$newarray2[$j]['day_by_day_itinerary']['locations'] = $itinerary;
						}
						if(end($itinerary_res)=='transport'){
							$newarray2[$j]['day_by_day_itinerary']['transport'] = $itinerary;
						}
						if(end($itinerary_res)=='meals'){
							$newarray2[$j]['day_by_day_itinerary']['meals'] = $itinerary;
						}
						if(end($itinerary_res)=='title'){
							$reverse = array_reverse( $itinerary_res );
							$z = $reverse[1];
							if($z=='accommodation'){
								$newarray2[$j]['day_by_day_itinerary']['accommodation']['title'] = $itinerary;
							}else{
								$newarray2[$j]['day_by_day_itinerary']['title'] = $itinerary;
							}
						}
						if(end($itinerary_res)=='room'){
							$reverse = array_reverse( $itinerary_res );
							$z = $reverse[1];
							if($z=='single'){
								$newarray2[$j]['day_by_day_itinerary']['accommodation']['single'] = $itinerary;
							}
							if($z=='double'){
								$newarray2[$j]['day_by_day_itinerary']['accommodation']['double'] = $itinerary;
							}
							if($z=='triple'){
								$newarray2[$j]['day_by_day_itinerary']['accommodation']['triple'] = $itinerary;
							}
						}
						if(end($itinerary_res)=='label'){
							$reverse = array_reverse( $itinerary_res );
							$z = $reverse[2];
							if($z=='accommodation'){
								$newarray2[$j]['day_by_day_itinerary']['accommodation']['button'] = $itinerary;
							}
						}
						if(end($itinerary_res)=='gallery'){
							$newarray2[$j]['day_by_day_itinerary']['gallery'] = $itinerary;
						}
						if(end($itinerary_res)=='description'){
							$newarray2[$j]['day_by_day_itinerary']['description'] = $itinerary;
						}
					}
				}
			}
		}
	}
?>
	<style>
		ul.parsley-errors-list.filled {
			margin: 0px;
			color: red;
		}
		#accordion {
			float: left;
			width: 100%;
			margin: 30px 0px;
		}
		.card2 {
			float: left;
			width: 100%;
			background-color: #fff;
			border: 1px solid #ccd0d4;
			box-shadow: 0 1px 1px rgba(0,0,0,.04);
			padding: 0px 0px;
			margin-bottom: 20px;
		}
		.card2 button {
			float: left;
			width: 100%;
			font-size: 14px;
			text-align: left;
			border: none;
			background-color: transparent;
			padding: 0px 0px;
			outline: none;
		}
		.card2 button h3 {
			font-size: 14px;
			padding: 8px 12px;
			margin: 0;
			line-height: 1.4;
		}
		.collapse.in{
			display: block;
			float: left;
			width: 100%;
			border-top: 1px solid #ccd0d4;
		}
		.card-body{
			padding: 8px 12px;
		}
		.card-body h3 {
			line-height: 1.4;
			font-size: 13px;
			font-weight: bold;
			margin: 0 0 3px;
			padding: 0;
			float: left;
			width: 100%;
		}
		.customtable th ,
		.customtable td {
			padding: 8px;
		}
		
		.customtable {
			border: #DFDFDF solid 1px;
				border-bottom-color: rgb(223, 223, 223);
				border-bottom-style: solid;
				border-bottom-width: 1px;
			border-bottom-color: rgb(223, 223, 223);
			border-bottom-style: solid;
			border-bottom-width: 1px;
			background: #fff;
			border-spacing: 0;
			border-radius: 0;
			table-layout: auto;
			padding: 0;
			margin: 0 0px 15px;
			width: 100%;
			clear: both;
			box-sizing: content-box;
			font-size: 13px;
		}
		.custom_row {
			background: #f4f4f4;
			cursor: move;
			color: #aaa;
			text-shadow: #fff 0 1px 0;
			text-align: center;
			padding: 8px;
			width: 40px;
		}
		.customtable .downloadrowinsert td {
			padding: 0;
		}
		.download_icon_block {
		  float: left;
		  width: 100%;
		  margin: 0;
		  padding: 8px 0px;
		  border-top: #EEEEEE solid 1px;
		}
		.download_icon_first {
		  float: left;
		  width: 20%;
		  display: block;
		  font-weight: bold;
		  margin: 0 0 3px;
		  padding: 0 12px;
		}
		.download_icon_second {
		  float: left;
		  width: 80%;
		  margin: 0px;
		  padding: 0px;
		  position:relative;
		}
		.acf-actions.-hover {
			position: absolute;
			display: none;
			top: 0;
			left: 0;
			padding: 5px;
			width: 55px;
		}
		.download_icon_second:hover > .acf-actions.-hover{
			display:block;
		}
		.download_icon_second .customtable tr td:hover .acf-actions.-hover{
			display:block;
		}
		.img_box_container{
			float: left;
			width: 100%;
			min-height: 300px;
			border: 1px solid;
			border-radius: 5px 5px 0px 0px;
			padding: 8px;
		}
		.add_gallery_single_row {
			float: left;
			margin: 0px;
			padding: 8px;
			width: 100%;
			border: 1px solid;
			border-radius: 0px 0px 5px 5px;
			border-top-width:0px;
		}
		.gallery_area_div {
			float:right;
		  }
		  .image_container {
			float:left!important;
			width: 55px;
			background-repeat: no-repeat;
			background-size: cover;
			border-radius: 3px;
			cursor: pointer;
		  }
		  .image_container img{
			border-radius: 3px;
		  }
		  .clear {
			clear:both;
		  }
		  .dynamic_cont {
			width: 100%;
			height: auto;
			position: relative;
			display: inline-block;
		  }
		  .dynamic_cont input[type=text] {
			width:300px;
		  }
		  .dynamic_cont .gallery_single_row {
			float: left;
			display:inline-block;
			width: 55px;
			position: relative;
			margin-right: 8px;
			margin-bottom: 20px;
		  }
		  .dolu {
			  display: inline-block!important;
		  }
		  .dynamic_cont label {
			padding:0 6px;
		  }
		  .button.remove {
			background: none;
			color: #000;
			position: absolute;
			border: none;
			top: 0px;
			right: 7px;
			font-size: 1.2em;
			padding: 0px;
			box-shadow: none;
			display: none;
		  }
		  .button.remove:hover {
			background: none;
			color: #000;
		  }
		  .gallery_single_row:hover .button.remove {
				display: block;
			}
		  .form-control{
			  font-size:13px;
		  }
		  #poststuff #post-body.columns-2 {
			margin-right: 300px;
		}
		#publishing-action{
			width:100%;
		}
	</style>
	<div class='wrap'>
		<h1 class="wp-heading-inline">Edit Custom Packages</h1>
		<a href="<?php echo admin_url('admin.php?page=add-custom-packages'); ?>" class="page-title-action">Add New</a>
		<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
		<div id="post-body-content" style="position: relative;">
		
		<div id="titlediv">
			<div id="titlewrap">
				<input type="text" name="custom_packages_title" size="30" value="<?php echo $package_name; ?>" id="title" spellcheck="true" autocomplete="off" placeholder="Add title">
			</div>
		</div>
		<div id="postdivrich" class="postarea wp-editor-expand">
			<div id="wp-content-wrap" class="wp-core-ui wp-editor-wrap html-active has-dfw" style="padding-top: 30px;">
				<?php
					wp_editor($package_content, 'custom_packages_description', $settings = array('textarea_rows' => '10'));
				?>
			</div>
		</div>
		
		<div id="accordion">
			<div class="card2">
              <div class="card-header2" id="card1">
                 <button class="" data-toggle="collapse" data-target="#itinerary" aria-expanded="true" aria-controls="collapseOne">
                     <div class="row no-gutters align-items-end">
                        <div class="col-12">
							<h3>Itinerary<i class="fa fa-caret-down" style="float: right;float: right;top: 5px;position: relative;"></i></h3>   
                        </div> 
                     </div>  
                  </button>
              </div>
			  <div id="itinerary" class="collapse <?php if(count($newarray2)>0){ echo 'in'; } ?>" aria-labelledby="itinerary" data-parent="#accordion">
                <div class="card-body">
					<div class="acf-repeater">
						<h3>Day by day itinerary</h3>
						<form method="post" id="tab1data">
						<input type="hidden" name="package_id" value="<?php echo $_REQUEST['package_id']; ?>"/>
						<input type="hidden" name="client_id" value="<?php echo $_REQUEST['client_id']; ?>"/>
						<input type="hidden" name="custom_packages_title" value="<?php echo $package_name; ?>"/>
						<input type="hidden" name="custom_packages_description" value="<?php echo $package_content; ?>"/>
						<table class="customtable">
							<tbody class="itineraryrowinsert ui-sortable">
								<?php foreach($newarray2 as $k => $val){ 
									$d=strtotime($val['day_by_day_itinerary']['time']->meta_value);
								?>
									<tr>
										<td class="custom_row"><?php echo $k+1; ?></td>
										<td>
											<div class="download_icon_block">
												<div class="download_icon_first">Day Label</div>
												<div class="download_icon_second">
													<input type="text" name="day_by_day_itinerary_day_label[]" class="form-control" value="<?php echo $val['day_by_day_itinerary']['day']->meta_value; ?>">
												</div>
											</div>
											<div class="download_icon_block">
												<div class="download_icon_first">Time Label</div>
												<div class="download_icon_second">
													<input type="text" name="day_by_day_itinerary_time_label[]" class="form-control date_start" value="<?php echo date("Y-m-d", $d); ?>">
												</div>
											</div>
											<div class="download_icon_block">
											   <div class="download_icon_first">Locations</div>
											   <div class="download_icon_second"><input type="text" name="day_by_day_itinerary_locations[]" class="form-control" value="<?php echo $val['day_by_day_itinerary']['locations']->meta_value; ?>"></div>
											</div>
											<div class="download_icon_block">
											   <div class="download_icon_first">Transport</div>
											   <div class="download_icon_second"><input type="text" name="day_by_day_itinerary_transport[]" class="form-control" value="<?php echo $val['day_by_day_itinerary']['transport']->meta_value; ?>"></div>
											</div>
											<div class="download_icon_block">
											   <div class="download_icon_first">Meals</div>
											   <div class="download_icon_second"><input type="text" name="day_by_day_itinerary_meals[]" class="form-control" value="<?php echo $val['day_by_day_itinerary']['meals']->meta_value; ?>"></div>
											</div>
											<div class="download_icon_block">
											   <div class="download_icon_first">Accommodation</div>
											   <div class="download_icon_second">
												  <table class="customtable">
													 <tbody class="ui-sortable">
														<tr>
														   <td>
															  <div class="download_icon_block">
																 <div class="download_icon_first">Title</div>
																 <div class="download_icon_second"><input type="text" name="day_by_day_itinerary_accommodation_title[]" class="form-control" value="<?php echo $val['day_by_day_itinerary']['accommodation']['title']->meta_value; ?>"></div>
															  </div>
														   </td>
														</tr>
														<tr>
														   <td>
															  <div class="download_icon_block">
																 <div class="download_icon_first">Single Room</div>
																 <div class="download_icon_second"><input type="text" name="day_by_day_itinerary_accommodation_single_room[]" class="form-control" value="<?php echo $val['day_by_day_itinerary']['accommodation']['single']->meta_value; ?>"></div>
															  </div>
														   </td>
														</tr>
														<tr>
														   <td>
															  <div class="download_icon_block">
																 <div class="download_icon_first">Double Room</div>
																 <div class="download_icon_second"><input type="text" name="day_by_day_itinerary_accommodation_double_room[]" class="form-control" value="<?php echo $val['day_by_day_itinerary']['accommodation']['double']->meta_value; ?>"></div>
															  </div>
														   </td>
														</tr>
														<tr>
														   <td>
															  <div class="download_icon_block">
																 <div class="download_icon_first">Triple Room</div>
																 <div class="download_icon_second"><input type="text" name="day_by_day_itinerary_accommodation_triple_room[]" class="form-control" value="<?php echo $val['day_by_day_itinerary']['accommodation']['triple']->meta_value; ?>"></div>
															  </div>
														   </td>
														</tr>
														<tr>
														   <td>
															  <div class="download_icon_block">
																 <div class="download_icon_first">Button Label</div>
																 <div class="download_icon_second"><input type="text" name="day_by_day_itinerary_accommodation_button_label[]" class="form-control" value="<?php echo $val['day_by_day_itinerary']['accommodation']['button']->meta_value; ?>"></div>
															  </div>
														   </td>
														</tr>
													 </tbody>
												  </table>
											   </div>
											</div>
											<div class="download_icon_block">
											   <div class="download_icon_first">Gallery</div>
											   <div class="download_icon_second">
												  <div class="dynamic_cont">
													 <div id="itineraryimg_box_container<?php echo $k+1; ?>" class="img_box_container">
														<?php $gallery =  unserialize($val['day_by_day_itinerary']['gallery']->meta_value);
															if(!empty($gallery)){
															foreach($gallery as $g => $gimage){ 
															
																$image = wp_get_attachment_image_src($gimage); 
																$pathinfo = pathinfo($image[0]);
																
															?>
																<div class="gallery_single_row dolu">
																   <div class="gallery_area_div image_container">
																   <img class="gallery_img_img" src="<?php echo $image[0]; ?>" alt="<?php echo $pathinfo['filename']; ?>" height="55" width="55"/>
																   <input class="meta_image_url" value="<?php echo $gimage; ?>" type="hidden" name="day_by_day_itinerary_gallery[<?php echo $k; ?>][]"></div>
																   <div class="gallery_area_div"><span class="button remove" onclick="remove_img(this)" title="Remove"><i class="fa fa-trash"></i></span></div>
																   <div class="clear"></div>
																</div>
															<?php }} ?>
													 </div>
													 <div style="display:none" id="itinerary_main_box<?php echo $k+1; ?>">
														<div class="gallery_single_row">
														   <div class="gallery_area_div image_container"><input class="meta_image_url" value="" type="hidden" name="day_by_day_itinerary_gallery[<?php echo $k; ?>][]"></div>
														   <div class="gallery_area_div"><span class="button remove" onclick="remove_img(this)" title="Remove"><i class="fa fa-trash"></i></span></div>
														   <div class="clear"></div>
														</div>
													 </div>
													 <div class="add_gallery_single_row"><input class="button button-primary itinerarygalleryadd" id="<?php echo $k+1; ?>" type="button" value="Add to gallery"></div>
												  </div>
											   </div>
											</div>
											<div class="download_icon_block">
											   <div class="download_icon_first">Title</div>
											   <div class="download_icon_second"><input type="text" name="day_by_day_itinerary_title[]" class="form-control" value="<?php echo $val['day_by_day_itinerary']['title']->meta_value; ?>"></div>
											</div>
											<div class="download_icon_block">
											   <div class="download_icon_first">Description</div>
											   <div class="download_icon_second"><textarea class="form-control" name="day_by_day_itinerary_description[]" rows="5"><?php echo $val['day_by_day_itinerary']['description']->meta_value; ?></textarea></div>
											</div>
											<div class="download_icon_block">
											   <div class="download_icon_first">Button Block</div>
											   <div class="download_icon_second">
												  <table class="customtable">
													 <tbody class="itineraryarrirowinsert<?php echo $k+1; ?> ui-sortable">
														<?php foreach($val['day_by_day_itinerary']['buttonblock'] as $j => $val){ ?>
															<tr>
															   <td class="custom_row"><?php echo $j+1; ?></td>
															   <td>
																  <div class="download_icon_block">
																	 <div class="download_icon_first">Button Label</div>
																	 <div class="download_icon_second"><input class="form-control" name="day_by_day_itinerary_button_block_button_label[<?php echo $k; ?>][]" value="<?php echo $val['label']->meta_value; ?>"></div>
																  </div>
																  <div class="download_icon_block">
																	 <div class="download_icon_first">Button Link</div>
																	 <div class="download_icon_second"><input class="form-control" name="day_by_day_itinerary_button_block_button_link[<?php echo $k; ?>][]" value="<?php echo $val['link']->meta_value; ?>"></div>
																  </div>
															   </td>
															   <td class="custom_row remove"><a class="day_by_day_itinerary_buttonblock acf-icon -plus small acf-js-tooltip" href="javascript:void(0)" data-event="add-row" title="Add row" id="<?php echo $k+1; ?>"></a><a class="row_remove acf-icon -minus small acf-js-tooltip" href="javascript:void(0)" data-event="remove-row" title="Remove row"></a></td>
															</tr>
														<?php } ?>
														<tr id="itineraryarrirowinsert<?php echo $k+1; ?>" style="display:none;">
														   <td class="custom_row"></td>
														   <td></td>
														   <td class="custom_row remove"></td>
														</tr>
													 </tbody>
												  </table>
												  <div class="acf-actions"><a class="day_by_day_itinerary_buttonblock acf-button button button-primary" href="javascript:void(0)" id="<?php echo $k+1; ?>" data-event="add-row">Add Row</a></div>
											   </div>
											</div>


										</td>
										<td class="custom_row remove">
											<a class="day_by_day_itinerary acf-icon -plus small acf-js-tooltip" href="javascript:void(0)" data-event="add-row" title="Add row"></a>
											<a class="row_remove acf-icon -minus small acf-js-tooltip" href="javascript:void(0)" data-event="remove-row" title="Remove row"></a>
										</td>
									</tr>
								<?php } ?>
								<tr id="itineraryrowinsert" style="display:none;">
									<td class="custom_row">0</td>
									<td>
										
									</td>
									<td class="custom_row remove">
									</td>
								</tr>
							</tbody>
						</table>
						<div class="acf-actions">
							<a class="day_by_day_itinerary acf-button button button-primary" id="day_by_day_itinerary" href="javascript:void(0)" data-event="add-row">Add Row</a>
						</div>
						</form>
					</div>
                </div> 
              </div>
          </div>
		  <div class="card2">
              <div class="card-header2" id="card2">
                 <button class="" data-toggle="collapse" data-target="#activities" aria-expanded="true" aria-controls="collapseOne">
                     <div class="row no-gutters align-items-end">
                        <div class="col-12">
							<h3>Activities<i class="fa fa-caret-down" style="float: right;top: 5px;position: relative;"></i></h3>   
                        </div> 
                     </div>  
                  </button>
              </div>
			  <div id="activities" class="collapse <?php if(count($newarray1)>0){ echo 'in'; } ?>" aria-labelledby="activities" data-parent="#accordion">
                <div class="card-body">
					<div class="acf-repeater">
						<h3>Your activities</h3>
						<form method="post" id="tab2data">
						<table class="customtable">
							<tbody class="activitiesrowinsert ui-sortable">
								<?php foreach($newarray1 as $k => $val){ 
									$d=strtotime($val['your_activities']['time']->meta_value);
								?>
									<tr>
										<td class="custom_row"><?php echo $k+1; ?></td>
										<td>
											<div class="download_icon_block">
												<div class="download_icon_first">Day Label</div>
												<div class="download_icon_second">
													<input type="text" name="your_activities_day_label[]" class="form-control" value="<?php echo $val['your_activities']['day']->meta_value; ?>">
												</div>
											</div>
											<div class="download_icon_block">
												<div class="download_icon_first">Time Label</div>
												<div class="download_icon_second">
													<input type="text" name="your_activities_time_label[]" class="form-control date_start" value="<?php echo date("Y-m-d", $d); ?>">
												</div>
											</div>
											<div class="download_icon_block">
												<div class="download_icon_first">Summary Label</div>
												<div class="download_icon_second">
													<input type="text" name="your_activities_summary_label[]" class="form-control" value="<?php echo $val['your_activities']['summary']->meta_value; ?>">
												</div>
											</div>
											<div class="download_icon_block">
												<div class="download_icon_first">Gallery</div>
												<div class="download_icon_second">
													<div class="dynamic_cont">
														<div id="img_box_container<?php echo $k+1; ?>" class="img_box_container">
															<?php $gallery =  unserialize($val['your_activities']['gallery']->meta_value);
															if(!empty($gallery)){
															foreach($gallery as $g => $gimage){ 
															
																$image = wp_get_attachment_image_src($gimage); 
																$pathinfo = pathinfo($image[0]);
																
															?>
																<div class="gallery_single_row dolu">
																   <div class="gallery_area_div image_container">
																   <img class="gallery_img_img" src="<?php echo $image[0]; ?>" alt="<?php echo $pathinfo['filename']; ?>" height="55" width="55"/>
																   <input class="meta_image_url" value="<?php echo $gimage; ?>" type="hidden" name="your_activities_gallery[<?php echo $k; ?>][]"></div>
																   <div class="gallery_area_div"><span class="button remove" onclick="remove_img(this)" title="Remove"><i class="fa fa-trash"></i></span></div>
																   <div class="clear"></div>
																</div>
															<?php }} ?>
														</div>
														<div style="display:none" id="main_box<?php echo $k+1; ?>">
															<div class="gallery_single_row">
																<div class="gallery_area_div image_container">
																	<input class="meta_image_url" value="" type="hidden" name="your_activities_gallery[<?php echo $k; ?>][]">
																</div>
																<div class="gallery_area_div">
																	<span class="button remove" onClick="remove_img(this)" title="Remove">
																		<i class="fa fa-trash"></i>
																	</span>
																</div>
																<div class="clear"></div>
															</div>
														</div>
														<div class="add_gallery_single_row">
															<input class="button button-primary galleryadd" id="<?php echo $k+1; ?>" type="button" value="Add to gallery">
														</div>
													</div>
												</div>
											</div>
											<div class="download_icon_block">
												<div class="download_icon_first">Arrival Data</div>
												<div class="download_icon_second">
													<table class="customtable">
														<tbody class="activitiesarrirowinsert<?php echo $k+1; ?> ui-sortable">
															<?php foreach($val['your_activities']['arrival_data'] as $j => $val){ ?>
																<tr>
																	<td class="custom_row"><?php echo $j+1; ?></td>
																	<td>
																		<div class="download_icon_block">
																			<div class="download_icon_first">Title</div>
																			<div class="download_icon_second">
																				<input type="text" name="your_activities_arrival_data_title[<?php echo $k; ?>][]" class="form-control" value="<?php echo $val['title']->meta_value; ?>">
																			</div>
																		</div>
																		<div class="download_icon_block">
																			<div class="download_icon_first">Meal</div>
																			<div class="download_icon_second">
																				<input type="text" name="your_activities_arrival_data_meal[<?php echo $k; ?>][]" class="form-control" value="<?php echo $val['meal']->meta_value; ?>">
																			</div>
																		</div>
																		<div class="download_icon_block">
																			<div class="download_icon_first">Button Label</div>
																			<div class="download_icon_second">
																				<input type="text" name="your_activities_arrival_data_button_label[<?php echo $k; ?>][]" class="form-control" value="<?php echo $val['button']->meta_value; ?>">
																			</div>
																		</div>
																		<div class="download_icon_block">
																			<div class="download_icon_first">Button Link</div>
																			<div class="download_icon_second">
																				<input type="text" name="your_activities_arrival_data_button_link[<?php echo $k; ?>][]" class="form-control" value="<?php echo $val['link']->meta_value; ?>">
																			</div>
																		</div>
																	</td>
																	<td class="custom_row">
																		<a class="your_activities_arrival_data acf-icon -plus small acf-js-tooltip" href="javascript:void(0)" data-event="add-row" title="Add row" id="<?php echo $k+1; ?>"></a>
																		<a class="row_remove acf-icon -minus small acf-js-tooltip" href="javascript:void(0)" data-event="remove-row" title="Remove row"></a>
																	</td>
																</tr>
															<?php } ?>
															<tr id="activitiesarrirowinsert<?php echo $k+1; ?>" style="display:none;">
																<td class="custom_row">0</td>
																<td></td>
																<td class="custom_row remove"></td>
															</tr>
														</tbody>
													</table>
													<div class="acf-actions">
														<a class="your_activities_arrival_data acf-button button button-primary" href="javascript:void(0)" data-event="add-row" id="<?php echo $k+1; ?>">Add Row</a>
													</div>
												</div>
											</div>
										</td>
										<td class="custom_row remove">
											<a class="your_activities acf-icon -plus small acf-js-tooltip" href="javascript:void(0)" data-event="add-row" title="Add row"></a>
											<a class="row_remove acf-icon -minus small acf-js-tooltip" href="javascript:void(0)" data-event="remove-row" title="Remove row"></a>
										</td>
									</tr>
								
								<?php } ?>
								
								<tr id="activitiesrowinsert" style="display:none;">
									<td class="custom_row">0</td>
									<td></td>
									<td class="custom_row remove"></td>
								</tr>
							</tbody>
						</table>
						<div class="acf-actions">
							<a class="your_activities acf-button button button-primary" id="your_activities" href="javascript:void(0)" data-event="add-row">Add Row</a>
						</div>
						</form>
					</div>
                </div> 
              </div>
          </div>
		  <div class="card2">
              <div class="card-header2" id="card2">
                 <button class="" data-toggle="collapse" data-target="#transport" aria-expanded="true" aria-controls="collapseOne">
                     <div class="row no-gutters align-items-end">
                        <div class="col-12">
							<h3>Transport<i class="fa fa-caret-down" style="float: right;top: 5px;position: relative;"></i></h3>   
                        </div> 
                     </div>  
                  </button>
              </div>
			  <div id="transport" class="collapse  <?php if(count($newarray)>0){ echo 'in'; } ?>" aria-labelledby="transport" data-parent="#accordion">
                <div class="card-body">
					<div class="acf-repeater">
						<h3>Your movements</h3>
						<form method="post" id="tab3data">
						<table class="customtable">
							<tbody class="transportrowinsert ui-sortable">
								<?php foreach($newarray as $k => $val){ 
									$d=strtotime($val['your_movements']['time_label']->meta_value);
								?>
									<tr>
										<td class="custom_row"><?php echo $k+1; ?></td>
										<td>
											<div class="download_icon_block">
												<div class="download_icon_first">Day Label</div>
												<div class="download_icon_second">
													<input type="text" name="transport_day_label[]" class="form-control" value="<?php echo $val['your_movements']['day_label']->meta_value; ?>">
												</div>
											</div>
											<div class="download_icon_block">
												<div class="download_icon_first">Time Label</div>
												<div class="download_icon_second">
													<input type="text" name="transport_time_label[]" class="form-control date_start" value="<?php echo date("Y-m-d", $d); ?>">
												</div>
											</div>
											<div class="download_icon_block">
												<div class="download_icon_first">Pick Up</div>
												<div class="download_icon_second">
													<table class="customtable">
														<thead>
															<th class="custom_row"></th>
															<th>Icon</th>
															<th>Title</th>
															<th>Description</th>
															<th class="custom_row"></th>
														</thead>
														<tbody class="transportpickuprowinsert<?php echo $k+1; ?> ui-sortable">
															<?php foreach($val['your_movements']['pickup'] as $j => $val) { ?>
																<tr>
																	<td class="custom_row"><?php echo $j+1; ?></td>
																	<td style="position:relative;">
																		<div class="dynamic_cont">
																			 <div id="your_movements_box_container<?php echo $k; ?><?php echo $j+1; ?>">
																				<?php $gallery =  $val['icon']->meta_value;
																					if($gallery!=''){
																						$image = wp_get_attachment_image_src($gallery); 
																						$pathinfo = pathinfo($image[0]);
																					?>
																						<div class="gallery_single_row dolu">
																						   <div class="gallery_area_div image_container">
																						   <img class="gallery_img_img" src="<?php echo $image[0]; ?>" alt="<?php echo $pathinfo['filename']; ?>" width="24"/>
																						   <input class="meta_image_url" value="<?php echo $gallery; ?>" type="hidden" name="your_movements_pick_up_icon[<?php echo $k; ?>][]"></div>
																						   <div class="gallery_area_div"><span class="button remove" onclick="single_remove_img(this)" title="Remove"><i class="fa fa-trash"></i></span></div>
																						   <div class="clear"></div>
																						</div>
																					<?php } ?>
																			 </div>
																			 <div style="display:none" id="your_movements_main_box<?php echo $k; ?><?php echo $j+1; ?>">
																				<div class="gallery_single_row">
																				   <div class="gallery_area_div image_container"><input class="meta_image_url" value="" type="hidden" name="your_movements_pick_up_icon[<?php echo $k; ?>][]"></div>
																				   <div class="gallery_area_div"><span class="button remove" onclick="single_remove_img(this)" title="Remove"><i class="fa fa-trash"></i></span></div>
																				   <div class="clear"></div>
																				</div>
																			 </div>
																			 <div class=""><input class="button button-primary transportsingleimageadd single_add_img" id="<?php echo $k; ?><?php echo $j+1; ?>" type="button" value=" Add Icon" style="<?php if($gallery!=''){ echo 'display:none;';} ?>">
																		</div>
																	</td>
																	<td><input type="text" name="transport_pickup_title[<?php echo $k; ?>][]" class="form-control" value="<?php echo $val['title']->meta_value; ?>"></td>
																	<td><textarea  class="form-control" name="transport_pickup_description[<?php echo $k; ?>][]" rows="5"><?php echo $val['description']->meta_value; ?></textarea></td>
																	<td class="custom_row">
																		<a class="transportpickup acf-icon -plus small acf-js-tooltip" href="javascript:void(0)" data-event="add-row" title="Add row" id="<?php echo $k+1; ?> "></a>
																		<a class="row_remove acf-icon -minus small acf-js-tooltip" href="javascript:void(0)" data-event="remove-row" title="Remove row"></a>
																	</td>
																</tr>
															<?php } ?>
															<tr id="transportpickuprowinsert<?php echo $k+1; ?>" style="display:none;">
																<td class="custom_row">1</td>
																<td></td>
																<td></td>
																<td></td>
																<td class="custom_row"></td>
															</tr>
														</tbody>
													</table>
													<div class="acf-actions">
														<a class="transportpickup acf-button button button-primary" id="<?php echo $k+1; ?>" href="javascript:void(0)" data-event="add-row">Add Row</a>
													</div>
												</div>
											</div>
										</td>
										<td class="custom_row remove">
											<a class="transport acf-icon -plus small acf-js-tooltip" href="javascript:void(0)" data-event="add-row" title="Add row"></a>
											<a class="row_remove acf-icon -minus small acf-js-tooltip" href="javascript:void(0)" data-event="remove-row" title="Remove row"></a>
										</td>
									</tr>
								<?php } ?>
								<tr id="transportrowinsert" style="display:none;">
									<td class="custom_row"></td>
									<td></td>
									<td class="custom_row remove"></td>
								</tr>
							</tbody>
						</table>
						<div class="acf-actions">
							<a class="transport acf-button button button-primary" id="transport" href="javascript:void(0)" data-event="add-row">Add Row</a>
						</div>
						</form>
					</div>
                </div> 
              </div>
          </div> 
		  <div class="card2">
              <div class="card-header2" id="card2">
                 <button class="" data-toggle="collapse" data-target="#download" aria-expanded="true" aria-controls="collapseOne">
                     <div class="row no-gutters align-items-end">
                        <div class="col-12">
							<h3>Download<i class="fa fa-caret-down" style="float: right;top: 5px;position: relative;"></i></h3>   
                        </div> 
                     </div>  
                  </button>
              </div>
			  <div id="download" class="collapse <?php if(count($downloadicon)>0){ echo 'in'; } ?>" aria-labelledby="download" data-parent="#accordion">
                <div class="card-body">
					<div class="acf-repeater">
						<h3>Download</h3>
						<form method="post" id="tab4data">
						<table class="customtable">
							<tbody class="downloadrowinsert ui-sortable">
								<?php foreach($downloadicon as $k => $val){ ?>
									<tr>
										<td class="custom_row"><?php echo $k+1; ?></td>
										<td>
											<div class="download_icon_block">
												<div class="download_icon_first">Icon</div>
												<div class="download_icon_second">
													<div class="dynamic_cont">
													 <div id="download_box_container<?php echo $k+1; ?>">
														<?php $gallery =  $val->meta_value;
															if($gallery!=''){
																$image = wp_get_attachment_image_src($gallery); 
																$pathinfo = pathinfo($image[0]);
															?>
																<div class="gallery_single_row dolu">
																   <div class="gallery_area_div image_container">
																   <img class="gallery_img_img" src="<?php echo $image[0]; ?>" alt="<?php echo $pathinfo['filename']; ?>" width="55"/>
																   <input class="meta_image_url" value="<?php echo $gallery; ?>" type="hidden" name="download_icon[]"></div>
																   <div class="gallery_area_div"><span class="button remove" onclick="single_remove_img(this)" title="Remove"><i class="fa fa-trash"></i></span></div>
																   <div class="clear"></div>
																</div>
															<?php } ?>
													 </div>
													 <div style="display:none" id="download_main_box<?php echo $k+1; ?>">
														<div class="gallery_single_row">
														   <div class="gallery_area_div image_container"><input class="meta_image_url" value="" type="hidden" name="download_icon[]"></div>
														   <div class="gallery_area_div"><span class="button remove" onclick="single_remove_img(this)" title="Remove"><i class="fa fa-trash"></i></span></div>
														   <div class="clear"></div>
														</div>
													 </div>
													 <div class=""><input class="button button-primary downloadsingleimageadd single_add_img" id="<?php echo $k+1; ?>" type="button" value="Add Icon" style="<?php if($gallery!=''){ echo 'display:none;';} ?>"></div>
													</div>
												</div>
											</div>
											<div class="download_icon_block">
												<div class="download_icon_first">PDF Title</div>
												<div class="download_icon_second"><input type="text" name="download_pdf_title[]" class="form-control" value="<?php echo $downloadtitle[$k]->meta_value; ?>"></div>
											</div>
											<div class="download_icon_block">
												<div class="download_icon_first">PDF Link</div>
												<div class="download_icon_second"><input type="text" name="download_pdf_link[]" class="form-control" value="<?php echo $downloadlink[$k]->meta_value; ?>"></div>
											</div>
										</td>
										<td class="custom_row remove">
											<a class="download acf-icon -plus small acf-js-tooltip" href="javascript:void(0)" data-event="add-row" title="Add row"></a>
											<a class="row_remove acf-icon -minus small acf-js-tooltip" href="javascript:void(0)" data-event="remove-row" title="Remove row"></a>
										</td>
									</tr>	
								<?php } ?>
								<tr id="downloadrowinsert" style="display:none;">
									<td class="custom_row"></td>
									<td></td>
									<td class="custom_row remove"></td>
								</tr>
							</tbody>
						</table>
						<div class="acf-actions">
							<a class="download acf-button button button-primary" id="download" href="javascript:void(0)" data-event="add-row">Add Row</a>
						</div>
						</form>
					</div>
                </div> 
              </div>
          </div>
		  <div class="card2">
              <div class="card-header2" id="card2">
                 <button class="" data-toggle="collapse" data-target="#included" aria-expanded="true" aria-controls="collapseOne">
                     <div class="row no-gutters align-items-end">
                        <div class="col-12">
							<h3>Included<i class="fa fa-caret-down" style="float: right;top: 5px;position: relative;"></i></h3>   
                        </div> 
                     </div>  
                  </button>
              </div>
			  <div id="included" class="collapse <?php if(count($price_include_res_array)>0){ echo 'in'; } ?>" aria-labelledby="included" data-parent="#accordion">
			  <form method="post" id="tab5data">
                <div class="card-body">
					<div class="acf-repeater">
						<h3>Price includes</h3>
						<table class="customtable">
							<thead>
								<tr>
									<th class="custom_row"></th>
									<th>Title</th>
									<th class="custom_row"></th>
								</tr>
							</thead>
							<tbody class="priceincluderowinsert ui-sortable">
								<?php foreach($price_include_res_array as $k => $val){ ?>
									<tr>
										<td class="custom_row"><?php echo $k+1; ?></td>
										<td><input type="text" name="price_include[]" class="form-control" value="<?php echo $val->meta_value; ?>"></td>
										<td class="custom_row remove">
											<a class="price_include acf-icon -plus small acf-js-tooltip" href="javascript:void(0)" data-event="add-row" title="Add row"></a>
											<a class="row_remove acf-icon -minus small acf-js-tooltip" href="javascript:void(0)" data-event="remove-row" title="Remove row"></a>
										</td>
									</tr>
								<?php } ?>
								<tr id="priceincluderowinsert" style="display:none;">
									<td class="custom_row"></td>
									<td></td>
									<td class="custom_row"></td>
								</tr>
							</tbody>
						</table>
						<div class="acf-actions">
							<a class="price_include acf-button button button-primary" id="price_include" href="javascript:void(0)" data-event="add-row">Add Row</a>
						</div>
					</div>
					<div class="acf-repeater">
						<h3>Price not include</h3>
						<table class="customtable">
							<thead>
								<tr>
									<th class="custom_row"></th>
									<th>Title</th>
									<th class="custom_row"></th>
								</tr>
							</thead>
							<tbody class="pricenotincluderowinsert ui-sortable">
								<?php foreach($price_not_include_res_array as $k => $val){ ?>
									<tr>
										<td class="custom_row"><?php echo $k+1; ?></td>
										<td><input type="text" name="price_not_include[]" class="form-control" value="<?php echo $val->meta_value; ?>"></td>
										<td class="custom_row remove">
											<a class="price_not_include acf-icon -plus small acf-js-tooltip" href="javascript:void(0)" data-event="add-row" title="Add row"></a>
											<a class="row_remove acf-icon -minus small acf-js-tooltip" href="javascript:void(0)" data-event="remove-row" title="Remove row"></a>
										</td>
									</tr>
								<?php } ?>
								<tr id="pricenotincluderowinsert" style="display:none;">
									<td class="custom_row"></td>
									<td></td>
									<td class="custom_row"></td>
								</tr>
							</tbody>
						</table>
						<div class="acf-actions">
							<a class="price_not_include acf-button button button-primary" id="price_not_include" href="javascript:void(0)" data-event="add-row">Add Row</a>
						</div>
					</div>
                </div> 
				</form>
              </div>
          </div> 
       </div>
	   
	</div>
	

<div id="postbox-container-1" class="postbox-container">
   <div id="side-sortables" class="meta-box-sortables ui-sortable" style="">
      <div id="submitdiv" class="postbox ">
         <h2 class="hndle ui-sortable-handle"><span>Publish</span></h2>
         <div class="inside">
            <div class="submitbox" id="submitpost">
               
               <div id="major-publishing-actions">
                 
                  <div id="publishing-action" style="width:100%;">
                     <input name="original_publish" type="hidden" id="original_publish" value="Update">
                     <input name="package_id" type="hidden" id="package_id" value="<?php echo $_REQUEST['package_id']; ?>">
                     <input name="client_id" type="hidden" id="client_id" value="<?php echo $_REQUEST['client_id']; ?>">
					 <input type="button" name="custompreviewpackage" id="custompreviewpackage" class="button" value="Preview package" style="float:left;">
                     <input name="save" type="button" class="button button-primary button-large" id="publish" value="Update">
                  </div>
                  <div class="clear"></div>
               </div>
            </div>
         </div>
      </div>
     
   </div>
</div>


	
	</div>
	</div>
	</div>
	<script>
		jQuery(document).ready(function(){
			// Add minus icon for collapse element which is open by default
			jQuery(".collapse.in").each(function(){
				jQuery(this).prev(".card-header2").find(".fa").addClass("fa-caret-up").removeClass("fa-caret-down");
			});
			
			// Toggle plus minus icon on show hide of collapse element
			jQuery(".collapse").on('show.bs.collapse', function(){
				jQuery(this).prev(".card-header2").find(".fa").removeClass("fa-caret-down").addClass("fa-caret-up");
				
			}).on('hide.bs.collapse', function(){
				jQuery(this).prev(".card-header2").find(".fa").removeClass("fa-caret-up").addClass("fa-caret-down");
			});
		});
		jQuery(document).on('click','.price_not_include',function(){
		   var count = 0;
		   jQuery('.pricenotincluderowinsert tr').each(function(index){
			  count = index*1 + 1*1;
		   })
		   jQuery('#pricenotincluderowinsert').before('<tr><td class="custom_row">'+count+'</td><td><input type="text" name="price_not_include[]" class="form-control" value=""></td><td class="custom_row remove"><a class="price_not_include acf-icon -plus small acf-js-tooltip" href="javascript:void(0)" data-event="add-row" title="Add row"></a><a class="row_remove acf-icon -minus small acf-js-tooltip" href="javascript:void(0)" data-event="remove-row" title="Remove row"></a></td></tr>');
		})
		jQuery(document).on('click','.price_include',function(){
		   var count = 0;
		   jQuery('.priceincluderowinsert tr').each(function(index){
			  count = index*1 + 1*1;
		   })
		   jQuery('#priceincluderowinsert').before('<tr><td class="custom_row">'+count+'</td><td><input type="text" name="price_include[]" class="form-control" value=""></td><td class="custom_row remove"><a class="price_include acf-icon -plus small acf-js-tooltip" href="javascript:void(0)" data-event="add-row" title="Add row"></a><a class="row_remove acf-icon -minus small acf-js-tooltip" href="javascript:void(0)" data-event="remove-row" title="Remove row"></a></td></tr>');
		})
		jQuery(document).on('click','.download',function(){
		   var count = 0;
		   jQuery('.downloadrowinsert tr').each(function(index){
			  count = index*1 + 1*1;
		   })
		   jQuery('#downloadrowinsert').before('<tr><td class="custom_row">'+count+'</td><td><div class="download_icon_block"><div class="download_icon_first">Icon</div><div class="download_icon_second"><div class="dynamic_cont"><div id="download_box_container'+count+'" class=""></div><div style="display:none" id="download_main_box'+count+'"><div class="gallery_single_row"><div class="gallery_area_div image_container"><input class="meta_image_url" value="" type="hidden" name="download_icon[]"></div><div class="gallery_area_div"><span class="button remove" onclick="single_remove_img(this)" title="Remove"><i class="fa fa-trash"></i></span></div><div class="clear"></div></div></div> <div class=""><input class="button button-primary downloadsingleimageadd single_add_img" id="'+count+'" type="button" value="Add Icon"></div></div></div></div><div class="download_icon_block"><div class="download_icon_first">PDF Title</div><div class="download_icon_second"><input type="text" name="download_pdf_title[]" class="form-control" value=""></div></div><div class="download_icon_block"><div class="download_icon_first">PDF Link</div><div class="download_icon_second"><input type="text" name="download_pdf_link[]" class="form-control" value=""></div></div></td><td class="custom_row remove"><a class="download acf-icon -plus small acf-js-tooltip" href="javascript:void(0)" data-event="add-row" title="Add row"></a><a class="row_remove acf-icon -minus small acf-js-tooltip" href="javascript:void(0)" data-event="remove-row" title="Remove row"></a></td></tr>');
		})
		jQuery(document).on('click','.transport',function(){
		   var count = 0;
		   var count1 = 0;
		   jQuery('.transportrowinsert > tr').each(function(index){
			  count = index*1 + 1*1;
		   })
		   jQuery('.transportpickuprowinsert > tr').each(function(index){
			  count1 = index*1 + 1*1;
		   })
		   jQuery('#transportrowinsert').before('<tr><td class="custom_row">'+count+'</td><td><div class="download_icon_block"><div class="download_icon_first">Day Label</div><div class="download_icon_second"><input type="text" name="your_movements_day_label[]" class="form-control" value=""></div></div><div class="download_icon_block"><div class="download_icon_first">Time Label</div><div class="download_icon_second"><input type="text" name="your_movements_time_label[]" class="form-control date_start" value=""></div></div><div class="download_icon_block"><div class="download_icon_first">Pick Up</div><div class="download_icon_second"><table class="customtable"><thead><th class="custom_row"></th><th>Icon</th><th>Title</th><th>Description</th><th class="custom_row"></th></thead><tbody class="transportpickuprowinsert'+count+' ui-sortable"><tr id="transportpickuprowinsert'+count+'" style="display:none;"><td class="custom_row"></td><td></td><td></td><td></td><td class="custom_row"></td></tr></tbody></table><div class="acf-actions"><a class="transportpickup acf-button button button-primary" id="'+count+'" href="javascript:void(0)" data-event="add-row">Add Row</a></div></div></div></td><td class="custom_row remove"><a class="transport acf-icon -plus small acf-js-tooltip" href="javascript:void(0)" data-event="add-row" title="Add row"></a><a class="row_remove acf-icon -minus small acf-js-tooltip" href="javascript:void(0)" data-event="remove-row" title="Remove row"></a></td></tr>');
		   jQuery(".date_start").each(function(){jQuery(this).datepicker({dateFormat:"yy-mm-dd"});});
		})
		jQuery(document).on('click','.transportpickup',function(){
			var count1 = 0;
		   var count = jQuery(this).attr('id');
		   var newcount = count*1 - 1*1;
		   jQuery('.transportpickuprowinsert'+count+' > tr').each(function(index){
			  count1 = index*1 + 1*1;
		   })
		  // alert('.transportpickuprowinsert'+count+' > tr');
		   jQuery('#transportpickuprowinsert'+count).before('<tr><td class="custom_row">'+count1+'</td><td><div class="dynamic_cont"><div id="your_movements_box_container'+newcount+count1+'" class=""></div><div style="display:none" id="your_movements_main_box'+newcount+count1+'"><div class="gallery_single_row"><div class="gallery_area_div image_container"><input class="meta_image_url" value="" type="hidden" name="your_movements_pick_up_icon['+newcount+'][]"></div><div class="gallery_area_div"><span class="button remove" onclick="single_remove_img(this)" title="Remove"><i class="fa fa-trash"></i></span></div><div class="clear"></div></div></div> <div class=""><input class="button button-primary transportsingleimageadd single_add_img" id="'+newcount+count1+'" type="button" value="Add Icon"></div></div></td><td><input type="text" name="your_movements_pick_up_title['+newcount+'][]" class="form-control" value=""></td><td><textarea class="form-control" name="your_movements_pick_up_description['+newcount+'][]" rows="5"></textarea></td><td class="custom_row"><a class="transportpickup acf-icon -plus small acf-js-tooltip" id="'+count+'" href="javascript:void(0)" data-event="add-row" title="Add row"></a><a class="row_remove acf-icon -minus small acf-js-tooltip" href="javascript:void(0)" data-event="remove-row" title="Remove row"></a></td></tr>');
		})
		jQuery(document).on('click','.your_activities',function(){
		   var count = 0;
		   var count1 = 0;
		   jQuery('.activitiesrowinsert > tr').each(function(index){
			  count = index*1 + 1*1;
		   })
		   jQuery('.activitiesarrirowinsert > tr').each(function(index){
			  count1 = index*1 + 1*1;
		   })
		   jQuery('#activitiesrowinsert').before('<tr><td class="custom_row">'+count+'</td><td><div class="download_icon_block"><div class="download_icon_first">Day Label</div><div class="download_icon_second"><input type="text" name="your_activities_day_label[]" class="form-control" value=""></div></div><div class="download_icon_block"><div class="download_icon_first">Time Label</div><div class="download_icon_second"><input type="text" name="your_activities_time_label[]" class="form-control date_start" value=""></div></div><div class="download_icon_block"><div class="download_icon_first">Summary Label</div><div class="download_icon_second"><input type="text" name="your_activities_summary_label[]" class="form-control" value=""></div></div><div class="download_icon_block"><div class="download_icon_first">Gallery</div><div class="download_icon_second"><div class="dynamic_cont"><div id="img_box_container'+count+'" class="img_box_container"></div><div style="display:none" id="main_box'+count+'"><div class="gallery_single_row"><div class="gallery_area_div image_container"><input class="meta_image_url" value="" type="hidden" name="your_activities_gallery['+count+'][]" /></div><div class="gallery_area_div"><span class="button remove" onclick="remove_img(this)" title="Remove"><i class="fa fa-trash"></i></span></div><div class="clear"></div></div></div><div class="add_gallery_single_row"><input class="button button-primary galleryadd" id="'+count+'" type="button" value="Add to gallery"/></div></div></div></div><div class="download_icon_block"><div class="download_icon_first">Arrival Data</div><div class="download_icon_second"><table class="customtable"><tbody class="activitiesarrirowinsert'+count+' ui-sortable"><tr id="activitiesarrirowinsert'+count+'" style="display:none;"><td class="custom_row"></td><td></td><td class="custom_row remove"></td></tr></tbody></table><div class="acf-actions"><a class="your_activities_arrival_data acf-button button button-primary"  href="javascript:void(0)" data-event="add-row" id="'+count+'">Add Row</a></div></div></div></td><td class="custom_row remove"><a class="your_activities acf-icon -plus small acf-js-tooltip" href="javascript:void(0)" data-event="add-row" title="Add row"></a><a class="row_remove acf-icon -minus small acf-js-tooltip" href="javascript:void(0)" data-event="remove-row" title="Remove row"></a></td></tr>');
		   jQuery(".date_start").each(function(){jQuery(this).datepicker({dateFormat:"yy-mm-dd"});});
		})
		jQuery(document).on('click','.your_activities_arrival_data',function(){
			var count1 = 0;
		   var count = jQuery(this).attr('id');
		   jQuery('.activitiesarrirowinsert'+count+' > tr').each(function(index){
			  count1 = index*1 + 1*1;
		   })
		   
		   jQuery('#activitiesarrirowinsert'+count).before('<tr><td class="custom_row">'+count1+'</td><td><div class="download_icon_block"><div class="download_icon_first">Title</div><div class="download_icon_second"><input type="text" name="your_activities_arrival_data_title['+count+'][]" class="form-control" value=""></div></div><div class="download_icon_block"><div class="download_icon_first">Meal</div><div class="download_icon_second"><input type="text" name="your_activities_arrival_data_meal['+count+'][]" class="form-control" value=""></div></div><div class="download_icon_block"><div class="download_icon_first">Button Label</div><div class="download_icon_second"><input type="text" name="your_activities_arrival_data_button_label['+count+'][]" class="form-control" value=""></div></div><div class="download_icon_block"><div class="download_icon_first">Button Link</div><div class="download_icon_second"><input type="text" name="your_activities_arrival_data_button_link['+count+'][]" class="form-control" value=""></div></div></td><td class="custom_row remove"><a class="your_activities_arrival_data acf-icon -plus small acf-js-tooltip" href="javascript:void(0)" data-event="add-row" title="Add row" id="'+count+'"></a><a class="row_remove acf-icon -minus small acf-js-tooltip" href="javascript:void(0)" data-event="remove-row" title="Remove row"></a></td></tr>');
		})
		jQuery(document).on('click','.day_by_day_itinerary',function(){
		   var count = 0;
		   var count1 = 0;
		   jQuery('.itineraryrowinsert > tr').each(function(index){
			  count = index*1 + 1*1;
		   })
		    jQuery('.itineraryarrirowinsert > tr').each(function(index){
			  count1 = index*1 + 1*1;
		   })
		   jQuery('#itineraryrowinsert').before('<tr><td class="custom_row">'+count+'</td><td><div class="download_icon_block"><div class="download_icon_first">Day Label</div><div class="download_icon_second"><input type="text" name="day_by_day_itinerary_day_label[]" class="form-control" value=""></div></div><div class="download_icon_block"><div class="download_icon_first">Time Label</div><div class="download_icon_second"><input type="text" name="day_by_day_itinerary_time_label[]" class="form-control date_start" value=""></div></div><div class="download_icon_block"><div class="download_icon_first">Locations</div><div class="download_icon_second"><input type="text" name="day_by_day_itinerary_locations[]" class="form-control" value=""></div></div><div class="download_icon_block"><div class="download_icon_first">Transport</div><div class="download_icon_second"><input type="text" name="day_by_day_itinerary_transport[]" class="form-control" value=""></div></div><div class="download_icon_block"><div class="download_icon_first">Meals</div><div class="download_icon_second"><input type="text" name="day_by_day_itinerary_meals[]" class="form-control" value=""></div></div><div class="download_icon_block"><div class="download_icon_first">Accommodation</div><div class="download_icon_second"><table class="customtable"><tbody class="ui-sortable"><tr><td><div class="download_icon_block"><div class="download_icon_first">Title</div><div class="download_icon_second"><input type="text" name="day_by_day_itinerary_accommodation_title[]" class="form-control" value=""></div></div></td></tr><tr><td><div class="download_icon_block"><div class="download_icon_first">Single Room</div><div class="download_icon_second"><input type="text" name="day_by_day_itinerary_accommodation_single_room[]" class="form-control" value=""></div></div></td></tr><tr><td><div class="download_icon_block"><div class="download_icon_first">Double Room</div><div class="download_icon_second"><input type="text" name="day_by_day_itinerary_accommodation_double_room[]" class="form-control" value=""></div></div></td></tr><tr><td><div class="download_icon_block"><div class="download_icon_first">Triple Room</div><div class="download_icon_second"><input type="text" name="day_by_day_itinerary_accommodation_triple_room[]" class="form-control" value=""></div></div></td></tr><tr><td><div class="download_icon_block"><div class="download_icon_first">Button Label</div><div class="download_icon_second"><input type="text" name="day_by_day_itinerary_accommodation_button_label[]" class="form-control" value=""></div></div></td></tr></tbody></table></div></div><div class="download_icon_block"><div class="download_icon_first">Gallery</div><div class="download_icon_second"><div class="dynamic_cont"><div id="itineraryimg_box_container'+count+'" class="img_box_container"></div><div style="display:none" id="itinerary_main_box'+count+'"><div class="gallery_single_row"><div class="gallery_area_div image_container"><input class="meta_image_url" value="" type="hidden" name="day_by_day_itinerary_gallery['+count+'][]"></div><div class="gallery_area_div"><span class="button remove" onclick="remove_img(this)" title="Remove"><i class="fa fa-trash"></i></span></div><div class="clear"></div></div></div><div class="add_gallery_single_row"><input class="button button-primary itinerarygalleryadd" id="'+count+'" type="button" value="Add to gallery"></div></div></div></div><div class="download_icon_block"><div class="download_icon_first">Title</div><div class="download_icon_second"><input type="text" name="day_by_day_itinerary_title[]" class="form-control" value=""></div></div><div class="download_icon_block"><div class="download_icon_first">Description</div><div class="download_icon_second"><textarea class="form-control" name="day_by_day_itinerary_description[]" rows="5"></textarea></div></div><div class="download_icon_block"><div class="download_icon_first">Button Block</div><div class="download_icon_second"><table class="customtable"><tbody class="itineraryarrirowinsert'+count+' ui-sortable"><tr id="itineraryarrirowinsert'+count+'" style="display:none;"><td class="custom_row"></td><td></td><td class="custom_row remove"></td></tr></tbody></table><div class="acf-actions"><a class="day_by_day_itinerary_buttonblock acf-button button button-primary" href="javascript:void(0)"  id="'+count+'" data-event="add-row">Add Row</a></div></div></div></td><td class="custom_row remove"><a class="day_by_day_itinerary acf-icon -plus small acf-js-tooltip" href="javascript:void(0)" data-event="add-row" title="Add row" id="1"></a><a class="row_remove acf-icon -minus small acf-js-tooltip" href="javascript:void(0)" data-event="remove-row" title="Remove row"></a></td></tr>');
		   jQuery(".date_start").each(function(){jQuery(this).datepicker({dateFormat:"yy-mm-dd"});});
		})
		jQuery(document).on('click','.day_by_day_itinerary_buttonblock',function(){
			var count1 = 0;
		   var count = jQuery(this).attr('id');
		   jQuery('.itineraryarrirowinsert'+count+' > tr').each(function(index){
			  count1 = index*1 + 1*1;
		   })
		   
		   jQuery('#itineraryarrirowinsert'+count).before('<tr><td class="custom_row">'+count1+'</td><td><div class="download_icon_block"><div class="download_icon_first">Button Label</div><div class="download_icon_second"><input class="form-control" name="day_by_day_itinerary_button_block_button_label['+count+'][]" value=""></div></div><div class="download_icon_block"><div class="download_icon_first">Button Link</div><div class="download_icon_second"><input class="form-control" name="day_by_day_itinerary_button_block_button_link['+count+'][]" value=""></div></div></td><td class="custom_row remove"><a class="day_by_day_itinerary_buttonblock acf-icon -plus small acf-js-tooltip" href="javascript:void(0)" data-event="add-row" title="Add row"  id="'+count+'"></a><a class="row_remove acf-icon -minus small acf-js-tooltip" href="javascript:void(0)" data-event="remove-row" title="Remove row"></a></td></tr>');
		})
		jQuery(document).on('click','.row_remove',function(){
			jQuery(this).parent().parent().remove();
		})
		jQuery(document).on('click','.icon_remove',function(){
			jQuery(this).parent().parent().find('img').remove();
			jQuery(this).parent().parent().find('.iconblock').show();
			jQuery(this).parent().parent().find('.acf-actions').hide();
			
		})
		function remove_img(value) {
            var parent=jQuery(value).parent().parent();
            parent.remove();
        }
		jQuery(document).on('click','.galleryadd',function(){
			var count = jQuery(this).attr('id');
			var media_uploader = null;
			media_uploader = wp.media({
				frame:    "post", 
				state:    "insert", 
				multiple: true 
			});
			media_uploader.on("insert", function(){

				var length = media_uploader.state().get("selection").length;
				var images = media_uploader.state().get("selection").models

				for(var iii = 0; iii < length; iii++){
					var image_url = images[iii].changed.url;
					console.log(images[iii].id);
					var box = jQuery('#main_box'+count).html();
					jQuery(box).appendTo('#img_box_container'+count);
					var element = jQuery('#img_box_container'+count+' .gallery_single_row:last-child').find('.image_container');
					var html = '<img class="gallery_img_img" src="'+image_url+'" height="55" width="55"/>';
					element.append(html);
					element.find('.meta_image_url').val(images[iii].id);
					console.log(image_url);		
				}
			});
			media_uploader.open();
		})
		jQuery(document).on('click','.itinerarygalleryadd',function(){
			var count = jQuery(this).attr('id');
			var media_uploader = null;
			media_uploader = wp.media({
				frame:    "post", 
				state:    "insert", 
				multiple: true 
			});
			media_uploader.on("insert", function(){

				var length = media_uploader.state().get("selection").length;
				var images = media_uploader.state().get("selection").models

				for(var iii = 0; iii < length; iii++){
					var image_url = images[iii].changed.url;
					console.log(images[iii].id);
					var box = jQuery('#itinerary_main_box'+count).html();
					jQuery(box).appendTo('#itineraryimg_box_container'+count);
					var element = jQuery('#itineraryimg_box_container'+count+' .gallery_single_row:last-child').find('.image_container');
					var html = '<img class="gallery_img_img" src="'+image_url+'" height="55" width="55"/>';
					element.append(html);
					element.find('.meta_image_url').val(images[iii].id);
					console.log(image_url);		
				}
			});
			media_uploader.open();
		})
		jQuery(document).on('click','.downloadsingleimageadd',function(){
			jQuery(this).hide();
			var count = jQuery(this).attr('id');
			var media_uploader = null;
			media_uploader = wp.media({
				frame:    "post", 
				state:    "insert", 
				multiple: false 
			});
			media_uploader.on("insert", function(){

				var length = media_uploader.state().get("selection").length;
				var images = media_uploader.state().get("selection").models

				for(var iii = 0; iii < length; iii++){
					var image_url = images[iii].changed.url;
					console.log(images[iii].id);
					var box = jQuery('#download_main_box'+count).html();
					jQuery(box).appendTo('#download_box_container'+count);
					var element = jQuery('#download_box_container'+count+' .gallery_single_row:last-child').find('.image_container');
					var html = '<img class="gallery_img_img" src="'+image_url+'" width="55"/>';
					element.append(html);
					element.find('.meta_image_url').val(images[iii].id);
					console.log(image_url);		
				}
			});
			media_uploader.open();
		})
		jQuery(document).on('click','.transportsingleimageadd',function(){
			jQuery(this).hide();
			var count = jQuery(this).attr('id');
			var media_uploader = null;
			media_uploader = wp.media({
				frame:    "post", 
				state:    "insert", 
				multiple: false 
			});
			media_uploader.on("insert", function(){

				var length = media_uploader.state().get("selection").length;
				var images = media_uploader.state().get("selection").models

				for(var iii = 0; iii < length; iii++){
					var image_url = images[iii].changed.url;
					console.log(images[iii].id);
					console.log(count);
					var box = jQuery('#your_movements_main_box'+count).html();
					jQuery(box).appendTo('#your_movements_box_container'+count);
					var element = jQuery('#your_movements_box_container'+count+' .gallery_single_row:last-child').find('.image_container');
					var html = '<img class="gallery_img_img" src="'+image_url+'" width="24"/>';
					element.append(html);
					element.find('.meta_image_url').val(images[iii].id);
					console.log(image_url);		
				}
			});
			media_uploader.open();
		})
		function single_remove_img(value) {
            var parent=jQuery(value).parent().parent();
			var parent1=jQuery(value).parent().parent().parent().parent();
            parent.remove();
			parent1.find('.single_add_img').show();
        }
	
</script>
