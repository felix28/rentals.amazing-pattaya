<?php
// Template Name: Import Property Page
?>
<h1>Import Property</h1>
<h2>Please be patient. Import might take about 1 minute.</h2>
<form method="post">
    <div>Put your property key here: <input type="text" name="propKey" /></div>
    <div><input type="submit" name="submit" value="Import" /></div>
</form>
<?php 
if (isset($_POST['submit']) && strlen(trim($_POST['propKey'])) > 0) {
	//API
	$authentication = array();
	$authentication['apiKey'] = 'y[JKp"r:>QxE3,QH';
	$authentication['propKey'] = trim($_POST['propKey']);
	//$authentication['propKey'] = 'galaetongtowerbakuri';
	//$authentication['propKey'] = 'acquajomtienbakuri';
	//$authentication['propKey'] = 'treeboutiquebakuri';
	//$authentication['propKey'] = 'atlantisreortbakuri';
	//$authentication['propKey'] = 'namtalayjomtienbakuri';
	//$authentication['propKey'] = 'viengpingmansionbakuri';
	//$authentication['propKey'] = 'grandecaribbeanresortbakuri';
	//$authentication['propKey'] = 'venetiansignaturebakuri';
	$data = array();
	$data['authentication'] = $authentication;
	$json = json_encode($data);

	$url = "https://api.beds24.com/json/getProperty";

	$ch=curl_init();
	curl_setopt($ch, CURLOPT_POST, 1) ;
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
	$result = curl_exec($ch);
	curl_close ($ch);
	//echo $result;


	$property = json_decode($result, true);
	//for ($r = 0; $r < 1; $r++) {
    for ($r = 0; $r < count($property['getProperty'][0]['roomTypes']); $r++) {
		//description
		$title            = $property['getProperty'][0]['roomTypes'][$r]['name'];
		$category         = intval(5); //apartment
		$allowed_html     = array();
		$roomType         = wp_kses(36,$allowed_html); // private room
		$guestNo          = $property['getProperty'][0]['roomTypes'][$r]['maxPeople'];
		$city             = wp_kses($property['getProperty'][0]['city'],$allowed_html);
		$neighborhood     = wp_kses($property['getProperty'][0]['address'],$allowed_html);
		$country          = wp_kses('Thailand',$allowed_html);
		$description      = '';
		$isInstantBooking = 0;            
		$status           = 'pending';
		$owner            = 6;
		//price
		$price            = floatval($property['getProperty'][0]['roomTypes'][$r]['minPrice']);
        //location
        $latitude         = floatval($property['getProperty'][0]['latitude']);
        $longitude        = floatval($property['getProperty'][0]['longitude']);
        $state            = wp_kses($property['getProperty'][0]['state']);
        //iCalendar
        $iCal             = $property['getProperty'][0]['roomTypes'][$r]['icalExportUrl'];

	    $post = create_new_property($title, $category, $roomType, $guestNo, $city, $neighborhood, $country,
		$description, $isInstantBooking, $price, $latitude, $longitude, $state, $iCal);
        /*echo 'post id is: '.$post;
        echo "<br />";*/
	    /*echo $property['getProperty'][0]['roomTypes'][$r]['name'];
	    echo '<br />';	*/
	}
	header('location: import-property');
}
function create_new_property($title, $category, $roomType, $guestNo, $city, $neighborhood, $country,
	$description, $isInstantBooking, $price, $latitude, $longitude, $state, $iCal){
	$post = array(
	    'post_title'	=> $title,
	    'post_status'	=> $status, 
	    'post_type'     => 'estate_property' ,
	    'post_author'   => $owner ,
	    'post_content'  => $description
	);

	$post_id =  wp_insert_post($post);
	if ($post_id) {
	//insert Description
        $category = get_term($category, 'property_category');

        if(isset($category->term_id)){
            $category_selected         =   $category->term_id;
        }

        $roomType           =   get_term( $roomType, 'property_action_category');  
        if(isset($roomType->term_id)){
             $roomType_selected  =   $roomType->term_id;
        }
        
        $api_prop_category_name =   '';
        if( isset($category->name) ){
            $api_prop_category_name=$category->name;
            wp_set_object_terms($post_id,$category->name,'property_category'); 
        }  
        
        $api_prop_action_category_name  = '';
        if ( isset ($roomType->name) ){
            $api_prop_action_category_name  =   $roomType->name;
            wp_set_object_terms($post_id,$roomType->name,'property_action_category'); 
        }
        if( isset($city) && $city!='none' ){
            wp_set_object_terms($post_id,$city,'property_city'); 
        }
        if( isset($neighborhood) && $neighborhood!='none' ){
            $neighborhood= wpestate_double_tax_cover($neighborhood,$city,$post_id);
           // wp_set_object_terms($post_id,$neighborhood,'property_area'); 
        }
        if( isset($neighborhood) && $neighborhood!='none' && $neighborhood!=''){
            $neighborhood_obj=   get_term_by('name', $neighborhood, 'property_area'); 
       
                $t_id = $neighborhood_obj->term_id ;
                $term_meta = get_option( "taxonomy_$t_id");
                $allowed_html   =   array();
                $term_meta['cityparent'] =  wp_kses( $city,$allowed_html);
                update_option( "taxonomy_$t_id", $term_meta );
           
        }
        update_post_meta($post_id, 'prop_featured', 0);
        update_post_meta($post_id, 'guest_no', $guestNo);
        update_post_meta($post_id,'instant_booking',$isInstantBooking);
        update_post_meta($post_id, 'property_country', $country);            
        update_post_meta($post_id, 'pay_status', 'not paid');
        update_post_meta($post_id, 'page_custom_zoom', 16);
        $sidebar =  get_option( 'wp_estate_blog_sidebar', true); 
        update_post_meta($post_id, 'sidebar_option', $sidebar);
        $sidebar_name   = get_option( 'wp_estate_blog_sidebar_name', true); 
        update_post_meta($post_id, 'sidebar_select', $sidebar_name);

		$property_admin_area    =   '';
        rcapi_create_new_listing($owner,$post_id,$title,$description,$status,$api_prop_category_name,$api_prop_action_category_name,$city,$neighborhood,$guestNo,$property_admin_area,$country,$isInstantBooking);
	//insert Price
        update_post_meta($post_id, 'property_price', $price);
        $price_week = $price * 7;
        update_post_meta($post_id, 'property_price_per_week', $price_week);
        $api_update_details['property_price'] = $price;
        $api_update_details['property_price_per_week'] = $price_week;
        rcapi_update_listing($post_id,$api_update_details);
    //insert location
        update_post_meta($post_id, 'property_latitude', $latitude);
        update_post_meta($post_id, 'property_longitude', $longitude);
        update_post_meta($post_id, 'property_address', $neighborhood);
        update_post_meta($post_id, 'property_state', $state);
        update_post_meta($post_id, 'property_county', $country);        
        $api_update_details['property_latitude']        =     $latitude;
        $api_update_details['property_longitude']       =     $longitude;
        $api_update_details['property_address']         =     $neighborhood;
        $api_update_details['property_state']           =     $state; 
        $api_update_details['property_county']          =     $country;            
        rcapi_update_listing($post_id,$api_update_details);
    //insert calendar
        $tmp_feed_array =   array();
        $all_feeds      =   array();

        if($iCal != '') {
            $tmp_feed_array['feed'] =   esc_url_raw($iCal);
            $tmp_feed_array['name'] =   esc_html('Unavailable');
            $all_feeds[]            =   $tmp_feed_array;
        }
                                        
        if(!empty($all_feeds)) {
            update_post_meta($post_id, 'property_icalendar_import_multi', $all_feeds);
            wpestate_import_calendar_feed_listing_global($post_id);
        }
    return $post_id;
	}
}
//get_footer(); 
?>