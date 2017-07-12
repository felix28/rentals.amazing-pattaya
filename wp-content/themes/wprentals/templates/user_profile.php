<link rel="stylesheet" href="/wp-content/themes/wprentals/libs/intl/css/intlTelInput.css" />
<link rel="stylesheet" href="/wp-content/themes/wprentals/libs/country-select/css/countrySelect.css" />

<?php
$current_user = wp_get_current_user();
$userID                 =   $current_user->ID;
$user_login             =   $current_user->user_login;
$first_name             =   get_the_author_meta( 'first_name' , $userID );
$last_name              =   get_the_author_meta( 'last_name' , $userID );
$user_email             =   get_the_author_meta( 'user_email' , $userID );
$user_mobile            =   get_the_author_meta( 'mobile' , $userID );
$user_phone             =   get_the_author_meta( 'phone' , $userID );
$description            =   get_the_author_meta( 'description' , $userID );
$facebook               =   get_the_author_meta( 'facebook' , $userID );
$twitter                =   get_the_author_meta( 'twitter' , $userID );
$linkedin               =   get_the_author_meta( 'linkedin' , $userID );
$pinterest              =   get_the_author_meta( 'pinterest' , $userID );
$user_skype             =   get_the_author_meta( 'skype' , $userID );

$user_title             =   get_the_author_meta( 'title' , $userID );
$user_custom_picture    =   get_the_author_meta( 'custom_picture' , $userID );
$user_small_picture     =   get_the_author_meta( 'small_custom_picture' , $userID );
$image_id               =   get_the_author_meta( 'small_custom_picture',$userID); 
$about_me               =   get_the_author_meta( 'description' , $userID );
$live_in                =   get_the_author_meta( 'live_in' , $userID );
$i_speak                =   get_the_author_meta( 'i_speak' , $userID );
$i_speak = ($i_speak == '') ? 'English, Thai' : $i_speak;
$paypal_payments_to     =   get_the_author_meta( 'paypal_payments_to' , $userID );
$payment_info           =   get_the_author_meta( 'payment_info' , $userID );
  
if($user_custom_picture==''){
    $user_custom_picture=get_template_directory_uri().'/img/default_user.png';
}
?>


<div class="user_profile_div"> 
     
       
            
        <div class=" row">  
              
                <div class="col-md-12">
             
                <?php
                
                $sms_verification =esc_html( get_option('wp_estate_sms_verification',''));
                if($sms_verification==='yes'){
                    $check_phone = get_the_author_meta( 'check_phone_valid' , $userID);
                  
                    if($check_phone!='yes'){
                
                    ?>
                    <div class="sms_wrapper">
                        <h4 class="user_dashboard_panel_title"><?php esc_html_e(' Validate your Mobile Phone Number to receive SMS Notifications','wpestate');?></h4>
                        <div class="col-md-12" id="sms_profile_message"></div>
                        <div class="col-md-9">
                            <?php //echo get_user_meta( $userID, 'validation_pin',true). '</br>';
                                esc_html_e('1. Add your Mobile no in Your Details section. Make sure you add it with country code.','wpestate');echo '</br>';
                                esc_html_e('2. Click on the button "Send me validation code".','wpestate');echo '</br>';
                                esc_html_e('3. You will get a 4 digit code number via sms at','wpestate');echo ' '.$user_mobile.'.</br> ';
                                esc_html_e('4. Add the 4 digit code in the form below and click "Validate Mobile Phone Number"','wpestate');
                                
                            ?>
                            <input type="text" style="max-width:250px;" id="validate_phoneno" class="form-control" value=""  name="validate_phoneno">
                            <button class="wpb_btn-info wpb_btn-small wpestate_vc_button  vc_button" id="send_sms_pin"><?php esc_html_e('Send me validation code','wpestate');?></button>
                            <button class="wpb_btn-info wpb_btn-small wpestate_vc_button  vc_button" id="validate_phone"><?php esc_html_e('Validate Mobile Phone Number','wpestate');?></button>
                            <?php  echo '</br>'; esc_html_e('*** If you don\'t receive the SMS, please check that your mobile phone number has the proper format (use the country code ex: +1 3232 232)','wpestate');echo '</br>';?>
                        </div>
                        
                        
                          <div class="col-md-6"></div>
                    </div>
                    <?php    
                    }
                }
                
                
                ?>

                
                <div class="user_dashboard_panel">
                <h4 class="user_dashboard_panel_title"><?php esc_html_e('Your details','wpestate');?></h4>
                
                <div class="col-md-12" id="profile_message"></div>
                
                <div class="col-md-4">
                    <p>
                        <label for="firstname"><?php esc_html_e('First Name','wpestate');?></label>
                        <input type="text" id="firstname" class="form-control" value="<?php echo $first_name;?>"  name="firstname">
                    </p>

                    <p>
                        <label for="secondname"><?php esc_html_e('Last Name','wpestate');?></label>
                        <input type="text" id="secondname" class="form-control" value="<?php echo $last_name;?>"  name="firstname">
                    </p>

                    <p>
                        <label for="useremail"><?php esc_html_e('Email','wpestate');?></label>
                        <input type="text" id="useremail"  class="form-control" value="<?php echo $user_email;?>"  name="useremail">
                    </p>
                    
                    <p>
                        <label for="about_me"><?php esc_html_e('About Me','wpestate');?></label>
                        <textarea id="about_me" class="form-control about_me_profile" name="about_me"><?php echo $about_me;?></textarea>
                    </p>
                       
                    <p>
                    <label for="live_in"><?php esc_html_e('I live in','wpestate');?></label>
                       <input type="text" id="live_in"  class="form-control intl-country" value="<?php echo $live_in;?>"  name="live_in">
                    </p>
                       
                    <p>
                        <label for="i_speak"><?php esc_html_e('I speak','wpestate');?></label>
                        <input type="text" id="i_speak"  class="form-control" value="<?php echo $i_speak;?>"  name="i_speak">
                    </p>
                     <p>
                        <label for="payment_info"><?php esc_html_e('Payment Info/Hidden Field','wpestate');?></label>
                        <textarea id="payment_info" class="form-control" name="payment_info" cols="70" rows="3"><?php echo $payment_info;?></textarea>
                    </p>
                    
                    <p>
                        <label for="paypal_payments_to"><?php esc_html_e('Email for receiving Paypal Payments (booking fees, refund security fees, etc)','wpestate');?></label>
                        <input type="text" id="paypal_payments_to"  class="form-control" value="<?php echo $paypal_payments_to;?>"  name="paypal_payments_to">
                    </p>
                </div>
                
                <div class="col-md-4">
                    <p>
                        <label for="userphone"><?php esc_html_e('Phone','wpestate');?></label>
                        <input type="text" id="userphone" class="form-control" value="<?php echo $user_phone;?>"  name="userphone">
                    </p>
                    <p>
                        <label for="usermobile"><?php esc_html_e('Mobile','wpestate');?></label>
                        <input type="text" id="usermobile" class="form-control intl-phone" value="<?php echo $user_mobile;?>"  name="usermobile">
                    </p>

                    <p>
                        <label for="userskype"><?php esc_html_e('Skype','wpestate');?></label>
                        <input type="text" id="userskype" class="form-control" value="<?php echo $user_skype;?>"  name="userskype">
                    </p>
                    
                    <p>
                        <label for="userfacebook"><?php esc_html_e('Facebook Url','wpestate');?></label>
                        <input type="text" id="userfacebook" class="form-control" value="<?php echo $facebook;?>"  name="userfacebook">
                    </p>

                     <p>
                        <label for="usertwitter"><?php esc_html_e('Twitter Url','wpestate');?></label>
                        <input type="text" id="usertwitter" class="form-control" value="<?php echo $twitter;?>"  name="usertwitter">
                    </p>

                     <p>
                        <label for="userlinkedin"><?php esc_html_e('Linkedin Url','wpestate');?></label>
                        <input type="text" id="userlinkedin" class="form-control"  value="<?php echo $linkedin;?>"  name="userlinkedin">
                    </p>

                     <p>
                        <label for="userpinterest"><?php esc_html_e('Pinterest Url','wpestate');?></label>
                        <input type="text" id="userpinterest" class="form-control"  height="100" value="<?php echo $pinterest;?>"  name="userpinterest">
                    </p>
                </div>
                <?php   wp_nonce_field( 'profile_ajax_nonce', 'security-profile' );   ?>
            
                
                <div class="col-md-4">
                     <div  id="profile-div">
                        
                           <?php print '<img id="profile-image" src="'.$user_custom_picture.'" alt="user image" data-profileurl="'.$user_custom_picture.'" data-smallprofileurl="'.$image_id.'" >';?>

                            <div id="upload-container">                 
                                <div id="aaiu-upload-container">                 

                                    <button id="aaiu-uploader" class="wpb_btn-info wpb_btn-small wpestate_vc_button  vc_button"><?php esc_html_e('Upload Image','wpestate');?></button>
                                    <div id="aaiu-upload-imagelist">
                                        <ul id="aaiu-ul-list" class="aaiu-upload-list"></ul>
                                    </div>
                                </div>  
                            </div>
                            <span class="upload_explain"><?php esc_html_e('* recommended size: minimum 550px ','wpestate');?></span>
                       
                    </div>
                    
                   
                </div>
                
            
                <p class="fullp-button">
                    <button class="wpb_btn-info wpb_btn-small wpestate_vc_button  vc_button" id="update_profile"><?php esc_html_e('Update profile','wpestate');?></button>
                </p>
               
                
            </div>
        </div>
            
            
            

    
    
 
    
    
      
    
   
        <div class="col-md-12">  
            <div class="user_dashboard_panel">
                   <h4 class="user_dashboard_panel_title"><?php esc_html_e('Change Password','wpestate');?></h4>


               <div class="col-md-12" id="profile_pass">
               </div> 

               <p  class="col-md-4">
                   <label for="oldpass"><?php esc_html_e('Old Password','wpestate');?></label>
                   <input  id="oldpass" value=""  class="form-control" name="oldpass" type="password">
               </p>

               <p  class="col-md-4">
                   <label for="newpass"><?php esc_html_e('New Password ','wpestate');?></label>
                   <input  id="newpass" value="" class="form-control" name="newpass" type="password">
               </p>
               <p  class="col-md-4">
                   <label for="renewpass"><?php esc_html_e('Confirm New Password','wpestate');?></label>
                   <input id="renewpass" value=""  class="form-control" name="renewpass"type="password">
               </p>

               <?php   wp_nonce_field( 'pass_ajax_nonce', 'security-pass' );   ?>
               <p class="fullp-button">
                   <button class="wpb_btn-info wpb_btn-small wpestate_vc_button  vc_button" id="change_pass"><?php esc_html_e('Reset Password','wpestate');?></button>

               </p>
           </div>
        </div>


 </div>
 <script src="/wp-content/themes/wprentals/libs/intl/js/intlTelInput.js"></script>
 <script src="/wp-content/themes/wprentals/libs/intl/js/intlmain.js"></script>
 <script src="/wp-content/themes/wprentals/libs/intl/js/utils.js"></script>
 <script src="/wp-content/themes/wprentals/libs/country-select/js/countrySelect.js"></script>
  <script src="/wp-content/themes/wprentals/libs/country-select/js/countrySelectMain.js"></script>