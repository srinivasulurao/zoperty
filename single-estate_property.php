<?php
// Index Page
// Wp Estate Pack
echo get_header_image();
error_reporting(0); // remove the errors from there.
get_header();
global $current_user;
global $feature_list_array;
global $propid ;
get_currentuserinfo();
$propid                     =   $post->ID;
$options                    =   wpestate_page_details($post->ID);
$gmap_lat                   =   esc_html( get_post_meta($post->ID, 'property_latitude', true));
$gmap_long                  =   esc_html( get_post_meta($post->ID, 'property_longitude', true));
$unit                       =   esc_html( get_option('wp_estate_measure_sys', '') );
$currency                   =   esc_html( get_option('wp_estate_currency_symbol', '') );
$use_floor_plans            =   intval( get_post_meta($post->ID, 'use_floor_plans', true) );


if (function_exists('icl_translate') ){
    $where_currency             =   icl_translate('wpestate','wp_estate_where_currency_symbol', esc_html( get_option('wp_estate_where_currency_symbol', '') ) );
    $property_description_text  =   icl_translate('wpestate','wp_estate_property_description_text', esc_html( get_option('wp_estate_property_description_text') ) );
    $property_details_text      =   icl_translate('wpestate','wp_estate_property_details_text', esc_html( get_option('wp_estate_property_details_text') ) );
    $property_features_text     =   icl_translate('wpestate','wp_estate_property_features_text', esc_html( get_option('wp_estate_property_features_text') ) );
    $property_adr_text          =   icl_translate('wpestate','wp_estate_property_adr_text', esc_html( get_option('wp_estate_property_adr_text') ) );
}else{
    $where_currency             =   esc_html( get_option('wp_estate_where_currency_symbol', '') );
    $property_description_text  =   esc_html( get_option('wp_estate_property_description_text') );
    $property_details_text      =   esc_html( get_option('wp_estate_property_details_text') );
    $property_features_text     =   esc_html( get_option('wp_estate_property_features_text') );
    $property_adr_text          =   stripslashes ( esc_html( get_option('wp_estate_property_adr_text') ) );
}


$agent_id                   =   '';
$content                    =   '';
$userID                     =   $current_user->ID;
$user_option                =   'favorites'.$userID;
$curent_fav                 =   get_option($user_option);
$favorite_class             =   'isnotfavorite';
$favorite_text              =   __('add to favorites','wpestate');
$feature_list               =   esc_html( get_option('wp_estate_feature_list') );
$feature_list_array         =   explode( ',',$feature_list);
$pinteres                   =   array();
$property_city              =   get_the_term_list($post->ID, 'property_city', '', ', ', '') ;
$property_area              =   get_the_term_list($post->ID, 'property_area', '', ', ', '');
$property_category          =   get_the_term_list($post->ID, 'property_category', '', ', ', '') ;
$property_action            =   get_the_term_list($post->ID, 'property_action_category', '', ', ', '');
$slider_size                =   'small';
$thumb_prop_face            =   wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'property_full');
if($curent_fav){
    if ( in_array ($post->ID,$curent_fav) ){
        $favorite_class =   'isfavorite';
        $favorite_text  =   __('favorite','wpestate');
    }
}

if (has_post_thumbnail()){
    $pinterest = wp_get_attachment_image_src(get_post_thumbnail_id(),'property_full_map');
}


if($options['content_class']=='col-md-12'){
    $slider_size='full';
}

?>



<div class="row">
    <?php get_template_part('templates/breadcrumbs'); ?>
    <div class=" <?php print $options['content_class'];?> ">
        <?php get_template_part('templates/ajax_container'); ?>
        <?php
        while (have_posts()) : the_post();
            $price          =   intval   ( get_post_meta($post->ID, 'property_price', true) );
            $price_label    =   esc_html ( get_post_meta($post->ID, 'property_label', true) );
            $image_id       =   get_post_thumbnail_id();
            $image_url      =   wp_get_attachment_image_src($image_id, 'property_full_map');
            $full_img       =   wp_get_attachment_image_src($image_id, 'full');
            $image_url      =   $image_url[0];
            $full_img       =   $full_img [0];
            if ($price != 0) {
               $price = number_format($price);
               if ($where_currency == 'before') {
                   $price = $currency . ' ' . $price;
               } else {
                   $price = $price . ' ' . $currency;
               }
           }else{
               $price='';
           }
        ?>

        <h1 class="entry-title entry-prop"><?php the_title(); ?></h1>
        <span class="price_area"><?php print $price; ?><?php print ' '.$price_label; ?></span>
        <div class="single-content listing-content">



        <?php


        $status = esc_html( get_post_meta($post->ID, 'property_status', true) );
        if (function_exists('icl_translate') ){
            $status     =   icl_translate('wpestate','wp_estate_property_status_'.$status, $status ) ;
        }

        ?>


        <div class="notice_area">

            <div class="property_categs">
                <?php print $property_category .' '.__('in','wpestate').' '.$property_action?>

            </div>
            <span class="adres_area"><?php
            print esc_html( get_post_meta($post->ID, 'property_address', true) ). ', ' . $property_city.', '.$property_area; ?></span>

           <div id="add_favorites" class="<?php print $favorite_class;?>" data-postid="<?php the_ID();?>"><?php echo $favorite_text;?></div>

           <div class="download_pdf">

           </div>

            <div class="prop_social">
                  <a href="http://www.facebook.com/sharer.php?u=<?php the_permalink(); ?>&amp;t=<?php echo urlencode(get_the_title()); ?>" target="_blank" class="share_facebook"><i class="fa fa-facebook fa-2"></i></a>

                <a href="http://twitter.com/home?status=<?php echo urlencode(get_the_title() .' '. get_permalink()); ?>" class="share_tweet" target="_blank"><i class="fa fa-twitter fa-2"></i></a>
                <a href="https://plus.google.com/share?url=<?php the_permalink(); ?>" onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" target="_blank" class="share_google"><i class="fa fa-google-plus fa-2"></i></a>
                <?php if (isset($pinterest[0])){ ?>
                   <a href="http://pinterest.com/pin/create/button/?url=<?php the_permalink(); ?>&amp;media=<?php echo $pinterest[0];?>&amp;description=<?php echo urlencode(get_the_title()); ?>" target="_blank" class="share_pinterest"> <i class="fa fa-pinterest fa-2"></i> </a>
                <?php } ?>
                <i class="fa fa-print" id="print_page" data-propid="<?php print $post->ID;?>"></i>
            </div>
        </div>

        <?php //print 'Status:'.$status.'</br>'; ?>

        <?php get_template_part('templates/listingslider'); ?>



        <?php
            $content = get_the_content();
            $content = apply_filters('the_content', $content);
            $content = str_replace(']]>', ']]&gt;', $content);

            if($content!=''){
                print $content;
            }

            //get_template_part ('/templates/download_pdf');

        ?>
            <div style='clear:both'></div>

            <!-- Address starts from here--->
            <div class="panel-group property-panel" id="accordion_prop_addr">
                <div class="panel panel-default">
                   <div class="panel-heading">
                       <a data-toggle="collapse" data-parent="#accordion_prop_addr" href="#collapseTwo">
                         <h4 class="panel-title">
                         <?php if($property_adr_text!=''){
                             //echo $property_adr_text;
							 echo "Address";
                         } else{
                             _e('Property Address','wpestate');
                         }
                         ?>
                         </h4>
                       </a>
                   </div>
                   <div id="collapseTwo" class="panel-collapse collapse in">
                     <div class="panel-body">

                     <?php print estate_listing_address($post->ID); ?>

                     </div>
                   </div>
                </div>
            </div>
            <!-- Address Ends Here --->

            <!-- Location Starts Here--> <!-- Clients Don't Wants to show this -->
<!--
            <div class="panel-group property-panel" id="accordion_prop_addr">
                <div class="panel panel-default">
                   <div class="panel-heading">
                       <a data-toggle="collapse" data-parent="#accordion_prop_addr" href="#collapseLocation">
                         <h4 class="panel-title">
                         Location
                         </h4>
                       </a>
                   </div>
                   <div id="collapseLocation" class="panel-collapse collapse in">
                     <div class="panel-body" >

					 <div id='googleDynamicMap' style='height:400px;box-shadow:0 0 10px grey'>
            The google Map Will Come here.the javascript is present at the bottom,Don't write Anything inside this 
                     </div>

					 </div>
                   </div>
                </div>
            </div>
-->
			<!-- Location ends here-->

			<!--Google Street View Starts from here, Google Map Embedding is present here.-->
			<?php if(get_post_meta($post->ID,"property_google_view",true)): ?>
			<div class="panel-group property-panel" id="accordion_prop_addr">
                <div class="panel panel-default">
                   <div class="panel-heading">
                       <a data-toggle="collapse" data-parent="#accordion_prop_addr" href="#collapseStreetView">
                         <h4 class="panel-title">
                         Google Street View
                         </h4>
                       </a>
                   </div>
                   <div id="collapseStreetView" class="panel-collapse collapse in">
                     <div class="panel-body" style='text-align:center'>
					    <?php
                        echo"<img style='width:100%;box-shadow:0 0 10px grey' src='https://maps.googleapis.com/maps/api/streetview?size=900x400&location=$gmap_lat,$gmap_long&key=AIzaSyAqSEzakD0cgi4_Wo_bVt3_---Ayh2mLHo'>";
						?>
                     </div>
                   </div>
                </div>
            </div>
          <?php endif; ?>
		  <!--Google Street View ends here-->

           <!-- About Project -->
            <div class="panel-group property-panel" id="accordion_prop_details">
                <div class="panel panel-default">
                    <div class="panel-heading">
                    <a data-toggle="collapse" data-parent="#accordion_prop_details" href="#collapseOne"><h4 class="panel-title" id="prop_det">About Project</h4></a>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse in">
                      <div class="panel-body">
                      <?php //print estate_listing_details($post->ID);?>
                      <?php print zopertyAboutProperty($post->ID); ?>
                      </div>
                    </div>
                </div>
            </div>
			<!-- About Project -->

            <!-- Floor plans Starts from here-->
            <?php // floor plans
            $floorPlans=get_post_meta($post->ID,'plan_image',true);
            if ($floorPlans[0])//if atleast one is there then show.
            {
            ?>

            <div class="panel-group property-panel" id="accordion_prop_features">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <a data-toggle="collapse" data-parent="#accordion_prop_features" href="#collapseFour">
                            <?php
                                print '<h4 class="panel-title" id="prop_ame">'.__('Floor Plans', 'wpestate').'</h4>';
                            ?>
                        </a>
                    </div>

                    <div id="collapseFour" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <?php //print estate_floor_plan($post->ID); ?>
                            <?php echo getZopertyFloorPlan($post->ID); ?>
                        </div>
                    </div>
                </div>
            </div>


            <?php
            }
            ?>
			<!-- Floor plans ends here -->


			<!--Specification to be kept here -->
            <?php if(get_post_meta($post->ID,'zoperty_specification',true)): ?>
			<div class="panel-group property-panel" id="accordion_prop_details">
                <div class="panel panel-default">
                    <div class="panel-heading">
					<a data-toggle="collapse" data-parent="#accordion_prop_features" href="#collapseSpecification">
                       <h4 class="panel-title" id="prop_ame">Specifications</h4>
					   </a>
                    </div>
                    <div id="collapseSpecification" class="panel-collapse collapse in">
                      <div class="panel-body">
                      <?php
					  $specification=get_post_meta($post->ID,'zoperty_specification',true);
                      echo apply_filters('zoperty_specification',$specification);
					  ?>
                      </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <!--specification ends  here -->



            <!-- Features and Ammenties -->
            <?php
            if ( count( $feature_list_array )!= 0 && count($feature_list_array)!=1 ){ //  if are features and ammenties
            ?>
            <div class="panel-group property-panel" id="accordion_prop_features">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <a data-toggle="collapse" data-parent="#accordion_prop_features" href="#collapseThree">
                          <?php
                            print '<h4 class="panel-title" id="prop_ame">'.__('Amenities', 'wpestate').'</h4>';
                          ?>
                        </a>
                    </div>
					<!-- The amenities with cross mark are disabled using jquery in the bottom. -->
                    <div id="collapseThree" class="panel-collapse collapse in">
                      <div class="panel-body">
                      <?php print estate_listing_features($post->ID); ?>
                      </div>
                    </div>
                </div>
            </div>
            <?php
            } // end if are features and ammenties
            ?>
            <!-- END Features and Ammenties -->

			<!--Video To be embed here -->
            <?php if(get_post_meta($post->ID,'embed_video_id',true)): ?>
			<div class="panel-group property-panel" id="accordion_prop_details">
                <div class="panel panel-default">
                    <div class="panel-heading">
					<a data-toggle="collapse" data-parent="#accordion_prop_features" href="#collapseVideo">
                       <h4 class="panel-title" id="prop_ame">Video</h4>
					   </a>
                    </div>
                    <div id="collapseVideo" class="panel-collapse collapse in">
                      <div class="panel-body">
                      <?php
					  $videoUrl=get_post_meta($post->ID,'embed_video_id',true);
					  echo"<iframe src='$videoUrl' width='100%' height='400' frameborder='0' allowfullscreen></iframe>";
					  ?>
                      </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <!--Video to be embed here -->

			<!--Downloads to be present here -->
            <?php
            $downloads=unserialize(base64_decode(get_post_meta($post->ID,'propertyPDF',true)));
            foreach($downloads as $d_yes):
            endforeach;
            if($d_yes): ?>
			<div class="panel-group property-panel" id="accordion_prop_details">
                <div class="panel panel-default">
                    <div class="panel-heading" >
					<a data-toggle="collapse" data-parent="#accordion_prop_features" href="#collapseDownloads">
                        <h4 class="panel-title" id="prop_ame">Downloads</h4>
                    </div>
                    <div id="collapseDownloads" class="panel-collapse collapse in">
                      <div class="panel-body">
                      <?php

					  $downloads=$downloads[$post->ID];
					  foreach($downloads as $md5=>$doc):
					  $docUrl=get_option('siteurl')."/wp-content/uploads".$doc;
					  $doc=explode("||",$doc);
					  $docName=$doc[1];
					  echo "<a href='$docUrl' target='_blank'><i class='fa fa-download'></i> $docName</a><br>";
					  endforeach;
					  ?>
                      </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <!--Downloads ends here -->

            <!--About Builder to be kept here -->
            <?php if(get_post_meta($post->ID,'zoperty_about_builder',true)): ?>
			<div class="panel-group property-panel" id="accordion_prop_details">
                <div class="panel panel-default">
                    <div class="panel-heading">
					<a data-toggle="collapse" data-parent="#accordion_prop_features" href="#collapseAboutBuilder" >
                       <h4 class="panel-title" id="prop_ame">About Builder</h4>
					   </a>
                    </div>
                    <div id="collapseAboutBuilder" class="panel-collapse collapse in">
                      <div class="panel-body">
                      <?php
					  $aboutBuilder=get_post_meta($post->ID,'zoperty_about_builder',true);
					  echo $aboutBuilder;
					  ?>
                      </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <!--About Builder ends  here -->


           <!--About Location to be kept here -->
            <?php if(get_post_meta($post->ID,'zoperty_about_location',true)): ?>
			<div class="panel-group property-panel" id="accordion_prop_details">
                <div class="panel panel-default">
                    <div class="panel-heading">
					<a data-toggle="collapse" data-parent="#accordion_prop_features" href="#collapseAboutLocation" >
                       <h4 class="panel-title" id="prop_ame">About Location</h4>
					   </a>
                    </div>
                    <div id="collapseAboutLocation" class="panel-collapse collapse in">
                      <div class="panel-body">
                      <?php
					  $aboutLocation=get_post_meta($post->ID,'zoperty_about_location',true);
					  echo $aboutLocation;
					  ?>
                      </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <!--About Location ends  here -->


			<hr>





        <?php
        wp_reset_query();
        ?>




        <?php
        endwhile; // end of the loop
        get_template_part ('/templates/agent_contact');  // It has to be there.
//        $show_compare=1;
//        $sidebar_agent_option_value=    get_post_meta($post->ID, 'sidebar_agent_option', true);
//
//        if ( $sidebar_agent_option_value !='yes'){
//            get_template_part ('/templates/agent_area');
//        }

        get_template_part ('/templates/similar_listings');

        ?>
        </div><!-- end single content -->
    </div><!-- end 9col container-->

<?php  include(locate_template('sidebar.php')); ?>
</div>

<?php get_footer(); ?>




<!-------------------------Settings related to the Maps-------------------------------------------->

<!--Client Don't want to show the google map anymore.
<script src="http://maps.googleapis.com/maps/api/js"></script>
<script>
var myCenter=new google.maps.LatLng(<?php echo $gmap_lat; ?>,<?php echo $gmap_long; ?>);

function initialize()
{
var mapProp = {
  center:myCenter,
  zoom: <?php echo (int)get_post_meta($post->ID, 'page_custom_zoom', true); ?>,
  mapTypeId:google.maps.MapTypeId.ROADMAP
  };

var map=new google.maps.Map(document.getElementById("googleDynamicMap"),mapProp);

var marker=new google.maps.Marker({
  position:myCenter,
  icon:'<?php echo get_option('siteurl'); ?>/wp-content/themes/wpresidence/img/properties.png'
  });

marker.setMap(map);
}

google.maps.event.addDomListener(window, 'load', initialize);
</script>
-->


<script>
jQuery(".fa-times").parent().css('display','none');
</script>

<script src='http://malsup.github.io/jquery.cycle.all.js'></script>
<script>
    jQuery('.floorPlan').cycle({
    fx:    'scrollLeft',
    prev:'#prev',
    next:'#next',
    timeout:0
});
// We have add one more detail of  iam interested thing in the form.
var propertyId="<?php echo $propid; ?>";

jQuery('form .wpcf7-response-output').after("<input type='hidden' name='property-id' value='"+propertyId+"' >");

function mapSlideUp(){
    
        if( jQuery("#openmap").find('i').hasClass('fa-angle-down') ){
    
            jQuery("#openmap").empty().append('<i class="fa fa-angle-up"></i>'+control_vars.close_map);
            
            if (control_vars.show_adv_search_map_close === 'no') {
                jQuery('.search_wrapper').addClass('adv1_close');
                adv_search_click();
            }
            
        }else{
            jQuery("#openmap").empty().append('<i class="fa fa-angle-down"></i>'+control_vars.open_map);
        }
        new_open_close_map(2);
    }
    

   setTimeout( function(){ 
    mapSlideUp(); 
  }
 ,1500 );
</script>
