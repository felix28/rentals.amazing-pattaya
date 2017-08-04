<?php
// Template Name: Import Property Page
$current_user = wp_get_current_user();
$userID                         =   $current_user->ID;
?>
<h1>Import Property</h1>
<h2 style="color: red">
    Please be patient. Import might take about 2 minutes. Don't refresh the page
</h2>
<form method="post">
    <div>
        <label for="propKey">Put your property key here:</label> 
        <input type="text" name="propKey" id="propKey" />
    </div>
    <div><input type="submit" name="submit" value="Import" /></div>
</form>
<?php 
if (isset($_POST['submit']) && strlen(trim($_POST['propKey'])) > 0) {
    require 'libs/simple_html_dom.php';
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
        //PHP simple HTML DOM parser
        $propID = $property['getProperty'][0]['propId'];
        $html = file_get_html('https://beds24.com/booking2.php?propid='.$propID);
		//description
		$title            = $property['getProperty'][0]['roomTypes'][$r]['name'];
		$category         = intval(5); //apartment
		$allowed_html     = array();
		$roomType         = wp_kses(36,$allowed_html); // private room
		$guestNo          = $property['getProperty'][0]['roomTypes'][$r]['maxPeople'];
		$city             = wp_kses($property['getProperty'][0]['city'],$allowed_html);
		$neighborhood     = wp_kses($property['getProperty'][0]['address'],$allowed_html);
		$country          = wp_kses('Thailand',$allowed_html);
        $property_desc    = $html->find('div.at_offersummary')[$r]->innertext;
		$description      = wp_kses($property_desc, $allowed_html);
		$isInstantBooking = 0;            
		$status           = 'pending';
		//$owner            = 6;//dev
        //$owner            = 8;//felix.labayen
        $owner            = $userID;
		//price
		$price            = floatval($property['getProperty'][0]['roomTypes'][$r]['minPrice']);
        //location
        $latitude         = floatval($property['getProperty'][0]['latitude']);
        $longitude        = floatval($property['getProperty'][0]['longitude']);
        $state            = wp_kses($property['getProperty'][0]['state']);
        //iCalendar
        $iCal             = $property['getProperty'][0]['roomTypes'][$r]['icalExportUrl'];
        
        if ($title != "CANCEL" || $price > 0) {        
            $postID = create_new_property($title, $category, $roomType, $guestNo, $city, $neighborhood,      $country, $description, $isInstantBooking, $price, $latitude, $longitude, $state,      $iCal);               
        }
	}
	header('location: import-property');
}

function create_new_property($title, $category, $roomType, $guestNo, $city, $neighborhood, $country,
	$description, $isInstantBooking, $price, $latitude, $longitude, $state, $iCal)
{
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

function uploadImageToMediaLibrary($postID, $url, $alt = "Imported Property") 
{
    require_once("wp-load.php");
    require_once("wp-admin/includes/image.php");
    require_once("wp-admin/includes/file.php");
    require_once("wp-admin/includes/media.php");

    $tmp = download_url( $url );
    $desc = $alt;
    $file_array = array();

    // Set variables for storage
    // fix file filename for query strings
    preg_match('/[^\?]+\.(jpg|jpe|jpeg|gif|png)/i', $url, $matches);
    $file_array['name'] = basename($matches[0]);
    $file_array['tmp_name'] = $tmp;

    // If error storing temporarily, unlink
    if ( is_wp_error( $tmp ) ) {
        @unlink($file_array['tmp_name']);
        $file_array['tmp_name'] = '';
    }

    // do the validation and storage stuff
    $id = media_handle_sideload( $file_array, $postID, $desc);
    
    //start set as featured image
    $file_loc   =   $file_array['tmp_name'];
    $file_name  =   $file_array['name'];
    $file_type  =   wp_check_filetype($file_name);

    $attachment = array(
        'post_mime_type' => $file_type,
        'post_title' => preg_replace('/\.[^.]+$/', '', basename($file_name)),
        'post_content' => '',
        'post_status' => 'inherit'
    );

    $attach_id      =   wp_insert_attachment($attachment, $file_loc);
    $attach_data    =   wp_generate_attachment_metadata($attach_id, $file_loc);
    wp_update_attachment_metadata($attach_id, $attach_data);
    add_post_meta($postID, '_thumbnail_id', $id); 
    //done set as featured image

    // If error storing permanently, unlink
    if (is_wp_error($id)) {
        @unlink($file_array['tmp_name']);
        return $id;
    }

    return $id;
}
//get_footer(); 
?>