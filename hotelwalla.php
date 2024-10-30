<?php
/*
Plugin Name: Hotelwalla
Plugin URI: http://www.hotelwalla.com/
Description: HotelWalla the first hotel widget for event organizers. Help people to find great hotels close to your venue. Is quickly and easy to add an unique experience to your attendees. The big deal here is that people can easily complete their hotel reservation in your website. <a href="options-general.php?page=hw_options">"Configuration options are here"</a>.
Version: 1.0
Author: LastRoom
Author URI: http://www.hotelwalla.com
License: GPLv2
*/
 
class Hotel_Walla extends WP_Widget {
 
	//process the new widget
	public function __construct() {
	$option = array(
	'classname' => 'hotel_walla',
	'description' => 'This Hotelwalla Booking System widgets - hotelwalla.com'
	);
	$this->WP_Widget('hotel_walla', 'Hotel walla', $option);
	} 
	
	
	//display the widget
	function widget($args, $instance) {
		extract($args);
		$title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
		
		$hw_name = get_option('hw_name');
		$hw_email = get_option('hw_email');
		$hw_event_name = get_option('hw_event_name');
		$hw_venues = get_option('hw_venues');
		
		$address = str_replace(" ","+",$hw_venues);
		$url = "http://maps.google.com/maps/api/geocode/json?address=".$address."&sensor=false";
		$url = file_get_contents($url);
		$response = json_decode($url);
		
		$arrDate = date("m-d-Y",strtotime("+1 day"));
		$depDate = date("m-d-Y",strtotime("+4 week"));
		
		$hw_lat = $response->results[0]->geometry->location->lat;
		$hw_lng = $response->results[0]->geometry->location->lng;
	
		$hw_alignment = get_option('hw_alignment');

		$lang = substr(get_bloginfo('language'), 0, 2);
		if(empty($lang)) {
			$lang = 'en';
		}
		
		$text =  '<div id="lr-hotels-box" data-name="'.$hw_event_name.'" data-lat="'.$hw_lat.'" data-lng="'.$hw_lng.'" data-alignment="'.$hw_alignment.'" data-arrivalDate="'.$arrDate.'" data-departureDate="'.$depDate.'"> </div>
	<script>  (function(d) {   var j, lh = d.getElementsByTagName(\'body\')[0];j = d.createElement(\'script\');   j.id = \'lr-sdk\';j.src = \'http://hotelwalla.com/libraries/lastroom-sdk.js\';   lh.appendChild(j); })(document) </script>';
		echo $before_widget;
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; } ?>
			<div class="textwidget"><?php echo $instance['filter'] ? wpautop($text) : $text; ?></div>
		<?php
		echo $after_widget;
	}
	
	
	//save the widget settings
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}
	 
	 
	//build the widget settings form
	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
		$title = strip_tags($instance['title']);
		
		 echo '<p><label for="'.$this->get_field_id('title').'">'._e('Title:').'</label>';
		 echo '<input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$title.'" /></p>';
	}
 
}
 
	add_action('widgets_init', 'hotel_walla_register');
 
	/**
	* Register the widget
	*
	* @since 1.0
	*/
	function hotel_walla_register() {
		register_widget('Hotel_Walla');
	}
	
	
	function do_post_request($url, $data, $optional_headers = null)
	{
	  $params = array('http' => array(
				  'method' => 'POST',
				  'content' => $data
				));
	  if ($optional_headers !== null) {
		$params['http']['header'] = $optional_headers;
	  }
	  $ctx = stream_context_create($params);
	  $fp = @fopen($url, 'rb', false, $ctx);
	  if (!$fp) {
		throw new Exception("Problem with $url, $php_errormsg");
	  }
	  $response = @stream_get_contents($fp);
	  if ($response === false) {
		throw new Exception("Problem reading data from $url, $php_errormsg");
	  }
	  return $response;
	}
	
	
	function hotelwalla_admin() {  
	
		$updateMsg ='';
	
		if($_POST['submit']) {
		
			if(!empty($_POST['hw_name'])) {
				$hw_name = $_POST['hw_name'];
				update_option('hw_name', $hw_name); 
			}
			else {
				$updateMsg .= "Name, ";
			}		
			
			if(!empty($_POST['hw_email'])) {
				$hw_email = $_POST['hw_email'];
				update_option('hw_email', $hw_email); 
			}
			else {
				echo $updateMsg .= "Email, ";
			}	
			
			if(!empty($_POST['hw_event_name'])) {
				$hw_event_name = $_POST['hw_event_name'];
				update_option('hw_event_name', $hw_event_name); 
			}
			else {
				$updateMsg .= "Event Name, ";
			}	
			
			if(!empty($_POST['hw_event_st_date'])) {
				$hw_event_st_date = $_POST['hw_event_st_date'];
				update_option('hw_event_st_date', $hw_event_st_date);
			}
			else {
				$updateMsg .= "Event start Date, ";
			}
			
			if(!empty($_POST['hw_event_st_date'])) {
				$hw_event_ed_date = $_POST['hw_event_ed_date'];
				update_option('hw_event_ed_date', $hw_event_ed_date);
			}
			else {
				$updateMsg .= "Event end Date, ";
			}	
			
			if(!empty($_POST['hw_venues'])) {
				$hw_venues = $_POST['hw_venues'];
				update_option('hw_venues', $hw_venues);
			}
			else {
				$updateMsg .= "Event Venue, ";
			}
			
			$address = str_replace(" ","+",$hw_venues);
			$url = "http://maps.google.com/maps/api/geocode/json?address=".$address."&sensor=false";
			$url = file_get_contents($url);
			$response = json_decode($url);
			
			$hw_lat = $response->results[0]->geometry->location->lat;
			update_option('hw_lat', $hw_lat);
			
			$hw_lng = $response->results[0]->geometry->location->lng;
			update_option('hw_lng', $hw_lng);
			
			$hw_alignment = $_POST['hw_alignment'];
			update_option('hw_alignment', $hw_alignment);
			
			$lang = substr(get_bloginfo('language'), 0, 2);
			
			if(empty($lang)) {
				$lang = 'en';
			}
			
			if(!empty($updateMsg)) {
				$errMsg = '<div id="message" class="error"><b>Please Fill '.$updateMsg.' field(s)</b></div>';
			}
			else {
				?>
                	
				<script type="text/javascript">
					jQuery.post('https://api.lastroom.mx/widgets.json', {
						  fullName: "<?php echo $hw_name;?>",
						  email: "<?php echo $hw_email;?>",
						  name: "<?php echo $hw_event_name;?>",
						  latitude: "<?php echo $hw_lat;?>",
						  longitude: "<?php echo $hw_lng;?>",
						  arrivalDate: "<?php echo $hw_event_st_date;?>",
						  departureDate: "<?php echo $hw_event_ed_date;?>",
						  alignment: "<?php echo $hw_alignment;?>",
						  lang: "<?php echo $lang;?>"
						}).done(function(response) {
						 if (response.hasOwnProperty("status")) {
						   // TODO
						 }
					});
				</script>

                	
			<?php		
				$errMsg = '<div id="message" class="updated fade"><b>Congratulations! All record updated.</b></div>';
			}
			
			
			
		}
	
		$hw_name = get_option('hw_name');
		$hw_email = get_option('hw_email');
		$hw_event_name = get_option('hw_event_name');
		$hw_event_st_date = get_option('hw_event_st_date');
		$hw_event_ed_date = get_option('hw_event_ed_date');
		$hw_venues = get_option('hw_venues');
		
		$hw_lat = get_option('hw_lat');
		$hw_lng = get_option('hw_lng');
		
		$hw_horizontal = (get_option('hw_alignment') =='horizontal') ? ' selected=selected' : '';
		$hw_vertical = (get_option('hw_alignment') =='vertical') ? ' selected=selected' : '';
		
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script( 'google-map-script' , 'http://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places');
		wp_enqueue_script( 'google-map-script' , 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js');
		wp_enqueue_script( 'hotelwalla-geo-script', plugins_url('/js/jquery.geocomplete.js', __FILE__) );
		wp_enqueue_style( 'JqueryUidate-css', plugins_url( '/css/jquery-ui.css', __FILE__ ) );
		
	
		$html = '<div class="wrap">
				<form action="'.str_replace( '%7E', '~', $_SERVER['REQUEST_URI']).'" method="post" name="hw_options">
					'.screen_icon('options-general').'
					<h2>Select Your HotelWalla Settings</h2>
					'.$errMsg.'
					<h3 class="title">1. Creator info</h3>
					<table class="form-table" width="100%" cellpadding="10">
					<tbody>
					<tr>
					<th><label>Your Name</label></th>
					<td scope="row" align="left">
					<input type="text" name="hw_name" value="'.$hw_name.'" />
					<p class="description">Example: Enter \'Juan Perez\' for your name</p>
					</td>
					</tr>
					<tr>
					<th><label>Your Email</label></th>
					<td scope="row" align="left">
					<input type="text" name="hw_email" value="'.$hw_email.'" />
					<p class="description">Example: Enter \'example@mail.com\' for your Email</p>
					</td>
					</tr>
					</tbody>
					</table>
					<h3 class="title">2. Event Details</h3>
					<table class="form-table" width="100%" cellpadding="10">
					<tbody>
					<tr>
					<th><label>Event Name</label></th>
					<td scope="row" align="left">
					<input type="text" name="hw_event_name" value="'.$hw_event_name.'" />
					<p class="description">Example: Enter \'My Event\' for your Event name</p>
					</td>
					</tr>
					<tr>
					<th><label>Event Start Date</label></th>
					<td scope="row" align="left">
					<input type="text" name="hw_event_st_date" id="hw_event_st_date" value="'.$hw_event_st_date.'" />
					<p class="description">Example: Enter \'Event Start Date\' this format MM-DD-YYYY</p>
					</td>
					</tr>
					<tr>
					<th><label>Event End Date</label></th>
					<td scope="row" align="left">
					<input type="text" name="hw_event_ed_date" id="hw_event_ed_date" value="'.$hw_event_ed_date.'" />
					<p class="description">Example: Enter \'Event End Date\' this format MM-DD-YYYY</p>
					</td>
					</tr>
					<tr>
					<th><label>Venue</label></th>
					<td scope="row" align="left">
					<input type="text" name="hw_venues" id="geocomplete" value="'.$hw_venues.'" />
					<input type="hidden" name="hw_lat" value="'.$hw_lat.'" />
					<input type="hidden" name="hw_lng" value="'.$hw_lng.'" />
					<p class="description">Example: Enter \'Your venue address or venue name\' for your venues</p>
					</td>
					</tr>
					<tr>
					<th><label>Select your format</label></th>
					<td scope="row" align="left">
					<select name="hw_alignment">
						<option value="vertical"'.$hw_vertical.'>Vertical</option>
						<option value="horizontal"'.$hw_horizontal.'>Horizontal</option>
					</select>
					<p class="description">
					Vertical - W: 570px, H: 250px <br />
					Horizontal - W: 250px, H: 570px 
					</p>
					</td>
					</tr>
					</tbody>
					</table>
					 <!--<input type="hidden" name="action" value="update" />
					 <input type="hidden" name="page_options" value="hw_name,hw_email,hw_event_name,hw_venues,hw_lat,hw_lng,hw_alignment" />-->
					 <p class="submit"><input type="submit" value="Save Changes" class="button button-primary" id="submit" name="submit"></p>
					 <br />
					 <p><center>Powered By hotelwalla.com</center></p>
				</form>
				</div>
				<script type="text/javascript">
					 jQuery(function(){
						jQuery("#geocomplete").geocomplete()
						  .bind("geocode:result", function(event, result){
							//$.log("Result: " + result.formatted_address);
						  })
						  .bind("geocode:error", function(event, status){
							//$.log("ERROR: " + status);
						  })
						  .bind("geocode:multiple", function(event, results){
							//$.log("Multiple: " + results.length + " results found");
						  });
						
						jQuery("#submit").click(function(){
						  jQuery("#geocomplete").trigger("geocode");
						});
						
					  });
					  
					jQuery(document).ready(function() {
						jQuery(\'#hw_event_ed_date, #hw_event_st_date\').datepicker({
							minDate	: 1,
							dateFormat : \'mm-dd-yy\'
						});
					});
				</script>
				
				';

    echo $html;
		
		
	}
	
	// add admin menu on settings section
	function hotelwalla_admin_actions() {
		add_options_page("HoteWalla", "HoteWalla", 1, "hw_options", "hotelwalla_admin");
	}
	
	add_action('admin_menu', 'hotelwalla_admin_actions');

?>
