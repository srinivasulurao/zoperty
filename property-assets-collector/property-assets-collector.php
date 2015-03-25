<?php
/*
Plugin Name: Property Assets Collector
Plugin URI: http://www.omkarsoft.com/
Description: Send Property based notification and configure the notification.
Author: N.Srinivasulu Rao
Version: 4.6.3
Author URI: http://github.com/srinivasulurao
*/


add_action('init','sendIamInterestedMailToAssignedUser');
add_action('init','contactAgentPropertyForm');

function sendIamInterestedMailToAssignedUser(){
    
    //This will work on the i'm interested form.
    $propid=$_POST['property-id']; // if we are recieving the propid through post then execute this code.
    if($propid):
    $propertyAdmin=get_post_meta($propid,'property_user',true);
    $user=get_user_by('id',$propertyAdmin);
    $message="";
    $message.="<div style='font-family:lucida sans unicode;line-height:20px;font-size:12px'>";
    $message.="Hello {$user->data->user_nicename},<br>";
    $message.="A new user has shown interest on the property published by you, following are the details of the client.";
    $message.="<br><label style='display:inline-block;width:120px;'>Client Name</label>: ".$_POST['your-name'];
    $message.="<br><label style='display:inline-block;width:120px;'>Client Email</label>: ".$_POST['your-email'];
    $message.="<br><label style='display:inline-block;width:120px;'>Client Phone</label>: ".$_POST['tel-914'];
    $message.="<br><label style='display:inline-block;width:120px;'>Client Message</label>: ".$_POST['your-message'];
    $message.="<br><label style='display:inline-block;width:120px;'>Propery Link</label>: ".get_permalink($propid);
    $message.="<br><br>";
    $message.="Thank You<br>";
    $message.="</div>";
    //debug($user);
    $propertyValue=get_post_meta($propid,'zoperty_property_value',true);
    $to=$user->data->user_email;
    // it depends on the property value, based on that the mail will go to the user.
    $to=($to && $propertyValue=="low")?$to:get_option('admin_email'); // The admin mail has to be a legitimate email
    sendMail($to,"Zoperty Property Notification",$message);
    endif;
    
}

function contactAgentPropertyForm(){
    //This will work on the i'm interested form.
    $propid=$_POST['propid']; // if we are recieving the propid through post then execute this code.
    if($propid and $_POST['action']=='wpestate_ajax_agent_contact_form'):
        $propertyAdmin=get_post_meta($propid,'property_user',true);
        $user=get_user_by('id',$propertyAdmin);
        $message="";
        $message.="<div style='font-family:lucida sans unicode;line-height:20px;font-size:12px'>";
        $message.="Hello {$user->data->user_nicename},<br>";
        $message.="A new user has shown interest on the property published by you, following are the details of the client.";
        $message.="<br><label style='display:inline-block;width:120px;'>Client Name</label>: ".$_POST['name'];
        $message.="<br><label style='display:inline-block;width:120px;'>Client Email</label>: ".$_POST['email'];
        $message.="<br><label style='display:inline-block;width:120px;'>Client Phone</label>: ".$_POST['phone'];
        $message.="<br><label style='display:inline-block;width:120px;'>Client Message</label>: ".$_POST['comment'];
        $message.="<br><label style='display:inline-block;width:120px;'>Propery Link</label>: ".get_permalink($propid);
        $message.="<br><br>";
        $message.="Thank You<br>";
        $message.="</div>";
        //debug($user);
        $propertyValue=get_post_meta($propid,'zoperty_property_value',true);
        $to=$user->data->user_email;
        // it depends on the property value, based on that the mail will go to the user.
        $to=($to && $propertyValue=="low")?$to:get_option('admin_email'); // The admin mail has to be a legitimate email
        sendMail($to,"Zoperty Property Notification",$message);
    endif;
}


function sendMail($to,$subject,$message,$attachments=''){  
$headers = 'From: Zoperty <'.get_option('admin_email').'>' . "\r\n";
add_filter('wp_mail_content_type','set_html_content_type');
return wp_mail( $to, $subject, $message, $headers, $attachments );
}



function set_html_content_type() {
return 'text/html';
}

?>