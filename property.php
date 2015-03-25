<?php

// register the custom post type

add_action('init', 'wpestate_create_property_type');

add_action('admin_init','zoperyFormSettings');

add_action('save_post','zoperty_update_post',1,2);





function zoperyFormSettings(){

    add_action('post_edit_form_tag', 'zoperty_add_edit_form_multipart_encoding');

    $post_id=$_REQUEST['post'];    	

	//add_action('save_post', 'save_joe_details')



}





//This is the correct way to save the post after using a hook.



    function zoperty_update_post($post_id,$post){

	//For saving the specification.
    //$_POST=array_map('addslashes',$_POST);
	
	if($_POST['zoperty_specification'] or $_POST['zoperty_about_location'] or $_POST['zoperty_about_builder']):

    update_post_meta($post_id,"zoperty_specification",$_POST['zoperty_specification']);
    update_post_meta($post_id,"zoperty_about_location",$_POST['zoperty_about_location']);
	update_post_meta($post_id,"zoperty_about_builder",$_POST['zoperty_about_builder']);
    endif;



	//for saving the About Project Details.

    

    update_post_meta($post_id,"zoperty_project_details",$_POST['zoperty_project_details']);        
    update_post_meta($post_id,"zoperty_property_address",$_POST['zoperty_property_address']);

    update_post_meta($post_id,"zoperty_land_extent",$_POST['zoperty_land_extent']);

    update_post_meta($post_id,"zoperty_builtup_area",$_POST['zoperty_builtup_area']);

    update_post_meta($post_id,"zoperty_apartment_types",$_POST['zoperty_apartment_types']);

    update_post_meta($post_id,"zoperty_starting_price",$_POST['zoperty_starting_price']);

    update_post_meta($post_id,"zoperty_no_of_towers",$_POST['zoperty_no_of_towers']);

    update_post_meta($post_id,"zoperty_apartment_size",$_POST['zoperty_apartment_size']);

    update_post_meta($post_id,"zoperty_possession_date",$_POST['zoperty_possession_date']);

    

	//debug($_FILES);

	$pdfError=@array_sum($_FILES['propertyPDF']['error']);

	//exit;

	$upload_dir=wp_upload_dir();

	

	if(!$pdfError): //if pdf are uploaded, that means there are no error

	$PDFDownloads=unserialize(base64_decode(get_post_meta($post_id,'propertyPDF',true)));

	for($key=0;$key<sizeof($_FILES['propertyPDF']['name']);$key++):

	//Should be from /uploads.

	$filename=time()."||".str_replace(" ","-",$_FILES['propertyPDF']['name'][$key]);

	$goFile=$upload_dir['basedir']."/zoperty-pdfs/".$filename;

	//@chmod($upload_dir['basedir']."/zoperty-pdfs",777);
        //echo $upload_dir['basedir']."/zoperty-pdfs/".$_FILES['propertyPDF']['name'][$key];


	@move_uploaded_file($_FILES['propertyPDF']['tmp_name'][$key],$upload_dir['basedir']."/zoperty-pdfs/".$filename);

	$uniqueIdentifier=md5($goFile);

	$PDFDownloads[$post_id][$uniqueIdentifier]="/zoperty-pdfs/".$filename;

	endfor;

	//Now store that in db.

	update_post_meta($post_id,"propertyPDF",base64_encode(serialize($PDFDownloads)));

	endif;

	

	if(sizeof($_POST['propertyPDFRemove'])){

	$PDFDownloads=unserialize(base64_decode(get_post_meta($post_id,'propertyPDF',true)));	

	foreach($_POST['propertyPDFRemove'] as $key=>$value):

	//We are not deleting the files from the server, just deleting them from the variable.

	unset($PDFDownloads[$post_id][$value]);

	endforeach;

	//debug($PDFDownloads);

	//exit;

	update_post_meta($post_id,"propertyPDF",base64_encode(serialize($PDFDownloads)));

	}

	



}







if( !function_exists('wpestate_create_property_type') ):

function wpestate_create_property_type() {

    register_post_type('estate_property', array(

        'labels' => array(

            'name'                  => __('Properties','wpestate'),

            'singular_name'         => __('Property','wpestate'),

            'add_new'               => __('Add New Property','wpestate'),

            'add_new_item'          => __('Add Property','wpestate'),

            'edit'                  => __('Edit','wpestate'),

            'edit_item'             => __('Edit Property','wpestate'),

            'new_item'              => __('New Property','wpestate'),

            'view'                  => __('View','wpestate'),

            'view_item'             => __('View Property','wpestate'),

            'search_items'          => __('Search Property','wpestate'),

            'not_found'             => __('No Properties found','wpestate'),

            'not_found_in_trash'    => __('No Properties found in Trash','wpestate'),

            'parent'                => __('Parent Property','wpestate')

        ),

        'public' => true,

        'has_archive' => true,

        'rewrite' => array('slug' => 'properties'),

        'supports' => array('title', 'editor', 'thumbnail', 'comments','excerpt'),

        'can_export' => true,

        'register_meta_box_cb' => 'wpestate_add_property_metaboxes',

        'menu_icon'=>get_template_directory_uri().'/img/properties.png'

         )

    );



    

    

////////////////////////////////////////////////////////////////////////////////////////////////

// Add custom taxomies

////////////////////////////////////////////////////////////////////////////////////////////////

    register_taxonomy('property_category', 'estate_property', array(

        'labels' => array(

            'name'              => __('Categories','wpestate'),

            'add_new_item'      => __('Add New Property Category','wpestate'),

            'new_item_name'     => __('New Property Category','wpestate')

        ),

        'hierarchical'  => true,

        'query_var'     => true,

        'rewrite'       => array( 'slug' => 'listings' )

        )

    );







// add custom taxonomy

register_taxonomy('property_action_category', 'estate_property', array(

    'labels' => array(

        'name'              => __('Action','wpestate'),

        'add_new_item'      => __('Add New Action','wpestate'),

        'new_item_name'     => __('New Action','wpestate')

    ),

    'hierarchical'  => true,

    'query_var'     => true,

    'rewrite'       => array( 'slug' => 'action' )

   )      

);







// add custom taxonomy

register_taxonomy('property_city', 'estate_property', array(

    'labels' => array(

        'name'              => __('City','wpestate'),

        'add_new_item'      => __('Add New City','wpestate'),

        'new_item_name'     => __('New City','wpestate')

    ),

    'hierarchical'  => true,

    'query_var'     => true,

    'rewrite'       => array( 'slug' => 'city' )

    )

);









// add custom taxonomy

register_taxonomy('property_area', 'estate_property', array(

    'labels' => array(

        'name'              => __('Neighborhood','wpestate'),

        'add_new_item'      => __('Add New Neighborhood','wpestate'),

        'new_item_name'     => __('New Neighborhood','wpestate')

    ),

    'hierarchical'  => true,

    'query_var'     => true,

    'rewrite'       => array( 'slug' => 'area' )



    )

);



// add custom taxonomy

register_taxonomy('property_county_state', 'estate_property', array(

    'labels' => array(

        'name'              => __('County / State','wpestate'),

        'add_new_item'      => __('Add New County / State','wpestate'),

        'new_item_name'     => __('New County / State','wpestate')

    ),

    'hierarchical'  => true,

    'query_var'     => true,

    'rewrite'       => array( 'slug' => 'state' )



    )

);



}// end create property type

endif; // end   wpestate_create_property_type      







///////////////////////////////////////////////////////////////////////////////////////////////////////////

///  Add metaboxes for Property

///////////////////////////////////////////////////////////////////////////////////////////////////////////

if( !function_exists('wpestate_add_property_metaboxes') ):

function wpestate_add_property_metaboxes() {

    add_meta_box('estate_property-sectionid',       __('Address', 'wpestate'),      'estate_box', 'estate_property', 'normal', 'default');

    add_meta_box('estate_property-googlemap',       __('Location', 'wpestate'),    'map_estate_box', 'estate_property', 'normal', 'default');

    //add_meta_box('estate_property-custom',          __('About Project2', 'wpestate'),        'custom_details_box', 'estate_property', 'normal', 'default');

    add_meta_box('estate_property-googleStreetView',__('Google Street View', 'wpestate'),    'googleStreetViewBox', 'estate_property', 'normal', 'default');

    add_meta_box('estate_property-features',        __('Amenities', 'wpestate'), 'amenities_estate_box', 'estate_property', 'normal', 'default' );

    add_meta_box('wpestate-paid-submission',        __('Paid Submission',   'wpestate'),      'estate_paid_submission', 'estate_property', 'side', 'high' );

    add_meta_box('floorplan_property-about-project',         __('About Project', 'wpestate'),             'aboutProjectBox', 'estate_property', 'normal', 'default' );

	add_meta_box('floorplan_property-user',         __('Floor Plans', 'wpestate'),             'floorplan_box', 'estate_property', 'normal', 'default' );

    add_meta_box('estate_property-videoBox',        __('Video', 'wpestate'),    'videoBox', 'estate_property', 'normal', 'default');

    add_meta_box('estate_property-downloadsBox',    __('Downloads', 'wpestate'),    'downloadsBox', 'estate_property', 'normal', 'default');

    add_meta_box('estate_property-specificationBox',__('Specifications', 'wpestate'),    'specificationBox', 'estate_property', 'normal', 'default');

    add_meta_box('estate_property-aboutBuilderBox',__('About Builder', 'wpestate'),    'aboutBuilderBox', 'estate_property', 'normal', 'default');

    add_meta_box('estate_property-aboutLocationBox',__('About Location', 'wpestate'),    'aboutLocationBox', 'estate_property', 'normal', 'default');

    

    //add_meta_box('estate_property-propdetails',     __('Property Details', 'wpestate'),       'details_estate_box', 'estate_property', 'normal', 'default');

	//add_meta_box('estate_property-agent',           __('Agent responsible', 'wpestate'),      'agentestate_box', 'estate_property', 'normal', 'default' );

	add_meta_box('estate_property-user',            __('Assign property to user', 'wpestate'), 'userestate_box', 'estate_property', 'normal', 'default' );

}



endif; // end   wpestate_add_property_metaboxes  



function debug($arrayObject){

   echo"<pre>";

    print_r($arrayObject);

   echo"</pre>";

}



function aboutProjectBox($post){



$mypost=$post->ID;

$about_project_details=get_post_meta($post->ID,'zoperty_project_details',true);

$addr=get_post_meta($post->ID, 'address', true);

$land_extent=get_post_meta($post->ID,'zoperty_land_extent',true);

$built_in_area=get_post_meta($post->ID,'zoperty_builtup_area',true);

$apartment_type=get_post_meta($post->ID,'zoperty_apartment_types',true);

$apartment_size=get_post_meta($post->ID,'zoperty_apartment_size',true);

$startingPrice=get_post_meta($post->ID,'zoperty_starting_price',true);

$possessionDate=get_post_meta($post->ID,'zoperty_possession_date',true);

$noOfTowers=get_post_meta($post->ID,'zoperty_no_of_towers',true);

$bathrooms=get_post_meta($post->ID,'property_bathrooms',true);    

$bedrooms=get_post_meta($post->ID,'property_bedrooms',true);

$price=get_post_meta($post->ID,'property_price',true);    

$lowSelected=(get_post_meta($post->ID,'zoperty_property_value',true)=="low" || get_post_meta($post->ID,'zoperty_property_value',true)=="")?"checked='checked'":"";

$highSelected=(get_post_meta($post->ID,'zoperty_property_value',true)=="high")?"checked='checked'":""; 



echo "<label>Details Of Project:</label>";

print wp_editor($about_project_details,"zoperty_project_details");

echo"<br><br>";    



echo"<label style='width:150px;display:inline-block'>Property Price:</label>

<input type='text' name='property_price' value='$price' style='margin-right:20px'>";       

echo"<label style='width:150px;display:inline-block'>Land Extent:</label>

<input type='text' name='zoperty_land_extent' value='$land_extent'><br>";    

echo"<label style='width:150px;display:inline-block'>Bathrooms:</label>

<input type='text' name='property_bathrooms' value='$bathrooms' style='margin-right:20px'>";      

echo"<label style='width:150px;display:inline-block'>Bedrooms:</label>

<input type='text' name='property_bedrooms' value='$bedrooms'><br>";    

echo"<label style='width:150px;display:inline-block'>Built Area of Project:</label>

<input type='text' name='zoperty_builtup_area' style='margin-right:20px' value='$built_in_area'>";

echo"<label style='width:150px;display:inline-block'>Types:</label>

<input type='text' name='zoperty_apartment_types' value='$apartment_type'><br>";

echo"<label style='width:150px;display:inline-block'>Size:</label>

<input type='text' name='zoperty_apartment_size' style='margin-right:20px' value='$apartment_size'>";

echo"<label style='width:150px;display:inline-block'>Starting Price:</label>

<input type='text' name='zoperty_starting_price' value='{$startingPrice}'><br>";

echo"<label style='width:150px;display:inline-block'>Possession Date:</label>

<input type='text' name='zoperty_possession_date' style='margin-right:20px' value='$possessionDate'>";

echo"<label style='width:150px;display:inline-block'>No Of Towers:</label>

<input type='text' name='zoperty_no_of_towers' value='$noOfTowers'><br>";

echo"<label style='width:150px;display:inline-block;'>Property Value:</label>

<input type='radio' name='zoperty_property_value' value='low' $lowSelected>Low <input type='radio' name='zoperty_property_value' value='high' $highSelected>High Value<br>";     





$no_of_balconies=get_post_meta($post->ID, 'no_of_balconies', true);

$total_units=get_post_meta($post->ID, 'total_units', true);

$no_of_floors_per_block=get_post_meta($post->ID, 'no_of_floors_per_block', true);

$vasthu=get_post_meta($post->ID, 'vasthu', true);

$utility_area_yes=(get_post_meta($post->ID, 'utility_area', true)=="Yes")?"selected='selected'":"";

$utility_area_no=(get_post_meta($post->ID, 'utility_area', true)=="No")?"selected='selected'":"";



    echo"<label style='width:150px;display:inline-block'>No. of balconies:</label>

<input type='text' name='no_of_balconies' style='margin-right:20px' value='$no_of_balconies'>";

    echo"<label style='width:150px;display:inline-block'>Total No. of Units:</label>

<input type='text' name='total_units' value='$total_units'><br>";

    echo"<label style='width:150px;display:inline-block'>No. of floors per block:</label>

<input type='text' name='no_of_floors_per_block' style='margin-right:20px' value='$no_of_floors_per_block'>";

    echo"<label style='width:150px;display:inline-block'>Vasthu:</label>

<input type='text' name='vasthu' value='{$vasthu}'><br>";

    echo"<label style='width:150px;display:inline-block'>Utility Area:</label>

<select name='utility_area'>

<option value='Yes' $utility_area_yes>Yes</option>

<option value='No' $utility_area_no>No</option>

</select>";



echo"<br><label style='width:150px;display:inline-block;vertical-align:top;'>Address:</label>

<textarea name='address' style='margin-right:20px;width:512px'>$addr</textarea>";



$no_of_car_parks=get_post_meta($post->ID,"no_of_car_parks",true);

$property_age=get_post_meta($post->ID,"property_age",true);

$no_of_floors=get_post_meta($post->ID,"no_of_floors",true);

$floor_no=get_post_meta($post->ID,"floor_no",true);



echo"<h3>Rent Related</h3>";

echo"<label style='width:150px;display:inline-block'>No of car parks:</label>

<input type='text' name='no_of_car_parks' value='$no_of_car_parks' style='margin-right:20px'>";

echo"<label style='width:150px;display:inline-block'>No. of Floors:</label>

<input type='text' name='no_of_floors' value='$no_of_floors'><br>";

echo"<label style='width:150px;display:inline-block'>Property Age:</label>

<input type='text' name='property_age' value='$property_age' style='margin-right:20px'>";

echo"<label style='width:150px;display:inline-block'>Floor No:</label>

<input type='text' name='floor_no' value='$floor_no'><br>";



}



function specificationBox($post){

$mypost=$post->ID;

$value=get_post_meta($mypost,'zoperty_specification');



print wp_editor($value[0],"zoperty_specification");

}





function aboutBuilderbox($post){

$mypost=$post->ID;

$value=get_post_meta($mypost,'zoperty_about_builder');

print wp_editor($value[0],"zoperty_about_builder");

}





function aboutLocationBox($post){

$mypost=$post->ID;

$value=get_post_meta($mypost,'zoperty_about_location'); 

print wp_editor($value[0],"zoperty_about_location");    

}



function zopertyAboutProperty($postId){

    $about=array();

    if(get_post_meta($postId,'zoperty_project_details',true))

    echo "<div class='listing_detail col-md-12'><strong>Details Of Project :</strong> <br>". get_post_meta($postId,'zoperty_project_details',true) . "</div>";



    if(get_post_meta($postId,'address',true))

    $about['Address']=get_post_meta($postId,'address',true);

    if(get_post_meta($postId,'zoperty_land_extent',true))

    $about['Project Land Extent']=get_post_meta($postId,'zoperty_land_extent',true);

    if(get_post_meta($postId,'zoperty_builtup_area',true))

    $about['Built Area of Project']=get_post_meta($postId,'zoperty_builtup_area',true);

    if(get_post_meta($postId,'zoperty_apartment_types',true))

    $about['Types']=get_post_meta($postId,'zoperty_apartment_types',true);

    if(get_post_meta($postId,'zoperty_apartment_size',true))

    $about['Size']=get_post_meta($postId,'zoperty_apartment_size',true);

    if(get_post_meta($postId,'property_price',true))

    $about['Starting Price']="&#8377; ".get_post_meta($postId,'property_price',true);

    if(get_post_meta($postId,'zoperty_possession_date',true))

    $about['Possession Date']=get_post_meta($postId,'zoperty_possession_date',true);

    if(get_post_meta($postId,'zoperty_no_of_towers',true))

    $about['No Of Towers']=get_post_meta($postId,'zoperty_no_of_towers',true);



    if(get_post_meta($postId,'total_units',true))

        $about['Total No. of Units']=get_post_meta($postId,'total_units',true);

    if(get_post_meta($postId,'no_of_floors_per_block',true))

        $about['No. of floors per block']="&#8377; ".get_post_meta($postId,'no_of_floors_per_block',true);

    if(get_post_meta($postId,'vasthu',true))

        $about['Vasthu']=get_post_meta($postId,'vasthu',true);

    if(get_post_meta($postId,'no_of_balconies',true))

        $about['No Of Balconies']=get_post_meta($postId,'no_of_balconies',true);

    if(get_post_meta($postId,'utility_area',true))

        $about['Utility Area']=get_post_meta($postId,'utility_area',true);



    //Show Rent Related data here.

    if(get_post_meta($postId,"no_of_car_parks",true))

        $about['No. of car parks']=get_post_meta($postId,"no_of_car_parks",true);

    if(get_post_meta($postId,"no_of_floors",true))

        $about['No. of floors']=get_post_meta($postId,"no_of_floors",true);

    if(get_post_meta($postId,"property_age",true))

        $about['Property Age']=get_post_meta($postId,"property_age",true);

    if(get_post_meta($postId,"floor_no",true))

        $about['Floor No.']=get_post_meta($postId,"floor_no",true);



    

    foreach($about as $k=>$v):

    if($v)

    echo"<div class='listing_detail col-md-4'><strong>$k : </strong>$v</div>";

    endforeach;

    

}



function getZopertyFloorPlan($postId){

    $floorPlans=get_post_meta($postId,'plan_image',true);

    echo "<ul class='floorPlan' style='box-shadow:0 0 4px grey !important;right:25px;border-radius:5px;border:5px solid white'>";

    foreach($floorPlans as $key=>$value):

    echo"<li style='width:100% !important'><a href='$value' class='prettyphoto2'><img src='$value' style='height:400px;width:100%;'></a></li>";

    endforeach;

    echo"</ul>";

    echo"<div style='text-align:center;margin:auto;width:300px;margin-bottom:-125px'>

        <a href='javascript:void(0)'><span id='prev' style='font-size: 70px; position: relative; z-index: 501; bottom:275px; right: 335px;'><i class='fa fa-angle-left'></i></span></a>

        <a href='javascript:void(0)'><span id='next'  style='font-size: 70px; position: relative; z-index: 501; bottom:275px; left: 350px;'><i class='fa fa-angle-right'></i></span></a>

        <ul id='nav'></ul>

    </div>";

}



function downloadsBox($post){

	error_reporting(0);

$mypost=$post->ID;

$value=get_post_meta($mypost,'propertyPDF');

$pdfs=unserialize(base64_decode($value[0]));

//debug($pdfs);

print"<label>Upload Your PDF doc files here.(You can select multiple files over here.)</label><br>";

print"<input type='file'  id='propertyPDF' name='propertyPDF[]'  multiple='multiple'><br>";

echo"<hr><label>Documents Uploaded</label><hr>";

$counter=1;

foreach($pdfs[$mypost] as $key=>$value):

$bg=($counter%2==0)?"lightgrey":"white";

$value_show=explode("||",$value);

$value_show=str_replace("-"," ",$value_show[1]);

echo "<div style='background:$bg;padding:1px'><label style='width:70%;display:inline-block'>".basename($value_show)."</label><input type='checkbox' value='{$key}' name='propertyPDFRemove[]'>Remove</div>";

$counter++;

endforeach;

if($counter==1)

	echo"No Documents uploaded so far.";



}



function zoperty_add_edit_form_multipart_encoding() {



    echo ' enctype="multipart/form-data"';



}









function videoBox($post){



    $option_video='';

    $video_values = array('vimeo', 'youtube');

    $video_type = get_post_meta($mypost, 'embed_video_type', true);



    foreach ($video_values as $value) {

        $option_video.='<option value="' . $value . '"';

        if ($value == $video_type) {

            $option_video.='selected="selected"';

        }

        $option_video.='>' . $value . '</option>';

    }



    $mypost=$post->ID;

    print'<table><tr><td valign="top" align="left">

        <p class="meta-options">

        <label for="embed_video_type">'.__('Video from ','wpestate').'</label><br />

        <select id="embed_video_type" name="embed_video_type" style="width: 237px;">

        ' . $option_video . '

        </select>

        </p>

    </td>';





    print'

    <td valign="top" align="left">

      <p class="meta-options">

      <label for="embed_video_id">'.__('Embed Video id: ','wpestate').'</label> <br />

        <input type="text" id="embed_video_id" name="embed_video_id" size="40" value="'.esc_html( get_post_meta($mypost, 'embed_video_id', true) ).'">

      </p>

    </td></tr></table>';

}



function googleStreetViewBox($post){

    $mypost=$post->ID;

    print '<input type="checkbox"  id="property_google_view" name="property_google_view" value="1" ';

    if (esc_html(get_post_meta($mypost, 'property_google_view', true)) == 1) {

        print'checked="checked"';

    }

    print' /><label for="property_google_view">'.__('Enable Google Street View','wpestate').'</label>';

}



if( !function_exists('custom_details_box') ):

function floorplan_box(){

    global $post;

    $plan_title         =   '';

    $plan_image         =   '';

    $plan_description   =   '';

    $plan_bath=$plan_rooms=$plan_size=$plan_price='';

    $use_floor_plans   = get_post_meta($post->ID, 'use_floor_plans', true);

    print '<p class="meta-options"> 

              <input type="hidden" name="use_floor_plans" value="0">

              <input type="checkbox" id="use_floor_plans" name="use_floor_plans" value="1"'; 

            if($use_floor_plans==1){

                print ' checked="checked" ';

            }

    print' >

              <label for="use_floor_plans">Use Floor Plans</label>

          </p>';

    

    print '<div id="plan_wrapper">';

    

    $plan_title_array           = get_post_meta($post->ID, 'plan_title', true);

    $plan_desc_array            = get_post_meta($post->ID, 'plan_description', true) ;

    $plan_image_array           = get_post_meta($post->ID, 'plan_image', true) ;

    $plan_image_attach_array    = get_post_meta($post->ID, 'plan_image_attach', true) ;

    $plan_size_array            = get_post_meta($post->ID, 'plan_size', true) ;

    $plan_rooms_array           = get_post_meta($post->ID, 'plan_rooms', true) ;

    $plan_bath_array            = get_post_meta($post->ID, 'plan_bath', true);

    $plan_price_array           = get_post_meta($post->ID, 'plan_price', true) ;



  

    

    if(is_array($plan_title_array)){

        foreach ($plan_title_array as $key=> $plan_name) {



            if ( isset($plan_desc_array[$key])){

                $plan_desc=$plan_desc_array[$key];

            }else{

                $plan_desc='';

            }

            

            if ( isset($plan_image_attach_array[$key])){

                $plan_image_attach=$plan_image_attach_array[$key];

            }else{

                $plan_image_attach='';

            }

                

                

            if ( isset($plan_image_array[$key])){

                $plan_img=$plan_image_array[$key];

            }else{

                $plan_img='';

            }



            if ( isset($plan_size_array[$key])){

                $plan_size=$plan_size_array[$key];

            }else{

                $plan_size='';

            }



            if ( isset($plan_rooms_array[$key])){

                $plan_rooms=$plan_rooms_array[$key];

            }else{

                $plan_rooms='';

            }



            if ( isset($plan_bath_array[$key])){

                $plan_bath=$plan_bath_array[$key];

            }else{

                $plan_bath='';

            }



            if ( isset($plan_price_array[$key])){

                $plan_price=$plan_price_array[$key];

            }else{

                $plan_price='';

            }





            print '



            <div class="plan_row">  

            <i class="fa deleter_floor fa-trash-o"></i>



            <p class="meta-options floor_p plan_title">

                <label for="plan_title">'.__('Plan Title','wpestate').'</label><br />

                <input id="plan_title" type="text" size="36" name="plan_title[]" value="'.$plan_name.'" />

           </p>



            <p class="meta-options floor_p plan_desc">

                <label for="plan_description">'.__('Plan Description','wpestate').'</label><br />

                <textarea class="plan_description" type="text" size="36" name="plan_description[]" >'.$plan_desc.'</textarea>

            </p>



            <p class="meta-options floor_p">

                <label for="plan_image">'.__('Plan Image','wpestate').'</label><br />

                <input id="plan_image" type="text" size="36" name="plan_image[]" value="'.$plan_img.'" /> '

                    . '<input type="hidden" id="plan_image_attach" name="plan_image_attach[]" value="'.$plan_image_attach.'"/>   

                <input id="plan_image_button" type="button"   size="40" class="upload_button button floorbuttons" value="'.__('Upload Image','wpestate').'" />

              

               

            </p>



            <p class="meta-options floor_p plan_size">

                <label for="plan_size">'.__('Plan Size','wpestate').'</label><br />

                <input id="plan_size" type="text" size="36" name="plan_size[]" value="'.$plan_size.'" />

            </p>



            <p class="meta-options floor_p plan_rooms">

                <label for="plan_rooms">'.__('Plan Rooms','wpestate').'</label><br />

                <input id="plan_rooms" type="text" size="36" name="plan_rooms[]" value="'.$plan_rooms.'" />

            </p>



            <p class="meta-options floor_p plan_bathrooms">

                <label for="plan_bath">'.__('Plan Bathrooms','wpestate').'</label><br />

                <input id="plan_bath" type="text" size="36" name="plan_bath[]" value="'.$plan_bath.'" />

            </p>



            <p class="meta-options floor_p plan_price">

                <label for="plan_price">'.__('Plan Price','wpestate').'</label><br />

                <input id="plan_price" type="text" size="36" name="plan_price[]" value="'.$plan_price.'" />

            </p>



            </div>';

        }

    }

  

    

  

 

    print '

    </div>    

    <span id="add_new_plan">'.__('Add new plan','wpestate').'</span>

    ';

    

    print"<style>

    .plan_title,.plan_desc,.plan_size,.plan_rooms,.plan_bathrooms,.plan_price{

    display:none;

    }

    

    #plan_image_button {

    bottom: 14px;

    float: right;

    position: absolute;

    right: 50px;

    width: 100px !important;  

    }

    

    #plan_image{

    width:225% !important;

    }

    

    </style>";

   

    

    

}

endif;





///////////////////////////////////////////////////////////////////////////////////////////////////////////

///  Property Custom details  function

///////////////////////////////////////////////////////////////////////////////////////////////////////////

if( !function_exists('custom_details_box') ):

function custom_details_box(){

     global $post;

    wp_nonce_field(plugin_basename(__FILE__), 'estate_property_noncename');

    

    $mypost             =   $post->ID;

    print'            

    <table width="100%" border="0" cellspacing="0" cellpadding="0">

    <tr >

    <td width="33%" valign="top" align="left">

        <p class="meta-options">

        <label for="property_price">'.__('Price: ','wpestate').'</label><br />

        <input type="text" id="property_price" size="40" name="property_price" value="' . esc_html(get_post_meta($mypost, 'property_price', true)) . '">

        </p>

    </td>

    

   <td width="33%" valign="top" align="left">

        <p class="meta-options">

        <label for="property_label">'.__('After Price Label(*for example "per month"): ','wpestate').'</label><br />

        <input type="text" id="property_label" size="40" name="property_label" value="' . esc_html(get_post_meta($mypost, 'property_label', true)) . '">

        </p>

    </td>

    

    </tr>

    <tr>

    

    <td width="33%" valign="top" align="left">

        <p class="meta-options">

        <label for="property_size">'.__('Size: ','wpestate').'</label><br />

        <input type="text" id="property_size" size="40" name="property_size" value="' . esc_html(get_post_meta($mypost, 'property_size', true)) . '">

        </p>

    </td>

    

    <td width="33%" valign="top" align="left">

        <p class="meta-options">

        <label for="property_lot_size">'.__('Lot Size: ','wpestate').'</label><br />

        <input type="text" id="property_lot_size" size="40" name="property_lot_size" value="' . esc_html(get_post_meta($mypost, 'property_lot_size', true)) . '">

        </p>

    </td>   

    </tr>

    

    <tr>      

    <td valign="top" align="left">

        <p class="meta-options">

        <label for="property_rooms">'.__('Rooms: ','wpestate').'</label><br />

        <input type="text" id="property_rooms" size="40" name="property_rooms" value="' . esc_html(get_post_meta($mypost, 'property_rooms', true)) . '">

        </p>

    </td>

    

    <td valign="top" align="left">

        <p class="meta-options">

        <label for="property_bedrooms">'.__('Bedrooms: ','wpestate').'</label><br />

        <input type="text" id="property_bedrooms" size="40" name="property_bedrooms" value="' . esc_html(get_post_meta($mypost, 'property_bedrooms', true)) . '">

        </p>

    </td>

    </tr>



    <tr>

    <td valign="top" align="left">  

        <p class="meta-options">

        <label for="property_bedrooms">'.__('Bathrooms: ','wpestate').'</label><br />

        <input type="text" id="property_bathrooms" size="40" name="property_bathrooms" value="' . esc_html(get_post_meta($mypost, 'property_bathrooms', true)) . '">

        </p>

    </td>

  

    </tr>

    <tr>';

     

     $option_video='';

     $video_values = array('vimeo', 'youtube');

     $video_type = get_post_meta($mypost, 'embed_video_type', true);



     foreach ($video_values as $value) {

         $option_video.='<option value="' . $value . '"';

         if ($value == $video_type) {

             $option_video.='selected="selected"';

         }

         $option_video.='>' . $value . '</option>';

     }

    print'</tr></table>';

	

	

     $i=0;

     $custom_fields = get_option( 'wp_estate_custom_fields', true);    

     if( !empty($custom_fields)){  

        while($i< count($custom_fields) ){     

            $name =   $custom_fields[$i][0]; 

            $label =   $custom_fields[$i][1];

            $type =   $custom_fields[$i][2];

            // $slug =   sanitize_key ( str_replace(' ','_',$name));

            $slug         =   wpestate_limit45(sanitize_title( $name )); 

            $slug         =   sanitize_key($slug); 

        

             print '<div class="metacustom"> ';

             if ( $type =='long text' ){

                 print '<label for="'.$slug.'">'.$label.' (*text) </label>';

                 print '<textarea type="text" id="'.$slug.'"  size="0" name="'.$slug.'" rows="3" cols="42">' . esc_html(get_post_meta($post->ID, $slug, true)) . '</textarea>'; 

             }else if( $type =='short text' ){

                 print '<label for="'.$slug.'">'.$label.' (*text) </label>';

                 print '<input type="text" id="'.$slug.'" size="40" name="'.$slug.'" value="' . esc_html(get_post_meta($post->ID,$slug, true)) . '">';

             }else if( $type =='numeric'  ){

                 print '<label for="'.$slug.'">'.$label.' (*numeric) </label>';

                 $numeric_value=get_post_meta($post->ID,$slug, true);

                 if($numeric_value!=''){

                     $numeric_value=  floatval($numeric_value);

                 }

                 print '<input type="text" id="'.$slug.'" size="40" name="'.$slug.'" value="' . $numeric_value . '">';

             }else if( $type =='date' ){

                 print '<label for="'.$slug.'">'.$label.' (*date) </label>';

                 print '<input type="text" id="'.$slug.'" size="40" name="'.$slug.'" value="' . esc_html(get_post_meta($post->ID,$slug, true)) . '">';

                 print '<script type="text/javascript">

                       //<![CDATA[

                       jQuery(document).ready(function(){

                               jQuery("#'.$slug.'").datepicker({

                                       dateFormat : "yy-mm-dd"

                               });

                       });

                       //]]>

                       </script>';



             }

             print '</div>';  

             $i++;        

       }

    }

    print '<div style="clear:both"></div>';

     

}

endif; // end   custom_details_box  









///////////////////////////////////////////////////////////////////////////////////////////////////////////

///  Agent box function

///////////////////////////////////////////////////////////////////////////////////////////////////////////

if( !function_exists('userestate_box') ):

function userestate_box($post) {

    global  $post;

    $mypost         =   $post->ID;

    $originalpost   =   $post;

    $blog_list      =   '';

    $original_user  =   wpsestate_get_author();





    

    $blogusers = get_users( 'blog_id=1&orderby=nicename&role=subscriber' );



    foreach ( $blogusers as $user ) {

 

        $the_id=$user->ID;

        $blog_list  .=  '<option value="' . $the_id . '"  ';

            if ($the_id == $original_user) {

                $blog_list.=' selected="selected" ';

            }

        $blog_list.= '>' .$user->user_login . '</option>';

    }





    

      

      print '

      <label for="property_user">'.__('Users: ','wpestate').'</label><br />

      <select id="property_user" style="width: 237px;" name="property_user">

            <option value="1">admin</option>

            <option value=""></option>

            '. $blog_list .'

      </select>';  



}

endif;





///////////////////////////////////////////////////////////////////////////////////////////////////////////

///  Property Pay Submission  function

///////////////////////////////////////////////////////////////////////////////////////////////////////////

if( !function_exists('estate_paid_submission') ):



function estate_paid_submission($post){

  global $post;

  $paid_submission_status= esc_html ( get_option('wp_estate_paid_submission','') );

  if($paid_submission_status=='no'){

     _e('Paid Submission is disabled','wpestate');  

  }

  

  if($paid_submission_status=='per listing'){

     _e('Pay Status: ','wpestate');

     $pay_status           = get_post_meta($post->ID, 'pay_status', true);

     if($pay_status=='paid'){

        _e('PAID','wpestate');

     }

     else{

        _e('Not Paid','wpestate');

     }

  }

    

}

endif; // end   estate_paid_submission  









///////////////////////////////////////////////////////////////////////////////////////////////////////////

///  Property details  function

///////////////////////////////////////////////////////////////////////////////////////////////////////////

if( !function_exists('details_estate_box') ):



function details_estate_box($post) {

    global $post;

    wp_nonce_field(plugin_basename(__FILE__), 'estate_property_noncename');

    

    $mypost             =   $post->ID;

    print'            

    <table width="100%" border="0" cellspacing="0" cellpadding="0">

    <tr >

    <td width="33%" valign="top" align="left">

        <p class="meta-options">

        <label for="property_price">'.__('Price: ','wpestate').'</label><br />

        <input type="text" id="property_price" size="40" name="property_price" value="' . esc_html(get_post_meta($mypost, 'property_price', true)) . '">

        </p>

    </td>

    

   <td width="33%" valign="top" align="left">

        <p class="meta-options">

        <label for="property_label">'.__('After Price Label(*for example "per month"): ','wpestate').'</label><br />

        <input type="text" id="property_label" size="40" name="property_label" value="' . esc_html(get_post_meta($mypost, 'property_label', true)) . '">

        </p>

    </td>

    

    </tr>

    <tr>

    

    <td width="33%" valign="top" align="left">

        <p class="meta-options">

        <label for="property_size">'.__('Size: ','wpestate').'</label><br />

        <input type="text" id="property_size" size="40" name="property_size" value="' . esc_html(get_post_meta($mypost, 'property_size', true)) . '">

        </p>

    </td>

    

    <td width="33%" valign="top" align="left">

        <p class="meta-options">

        <label for="property_lot_size">'.__('Lot Size: ','wpestate').'</label><br />

        <input type="text" id="property_lot_size" size="40" name="property_lot_size" value="' . esc_html(get_post_meta($mypost, 'property_lot_size', true)) . '">

        </p>

    </td>   

    </tr>

    

    <tr>      

    <td valign="top" align="left">

        <p class="meta-options">

        <label for="property_rooms">'.__('Rooms: ','wpestate').'</label><br />

        <input type="text" id="property_rooms" size="40" name="property_rooms" value="' . esc_html(get_post_meta($mypost, 'property_rooms', true)) . '">

        </p>

    </td>

    

    <td valign="top" align="left">

        <p class="meta-options">

        <label for="property_bedrooms">'.__('Bedrooms: ','wpestate').'</label><br />

        <input type="text" id="property_bedrooms" size="40" name="property_bedrooms" value="' . esc_html(get_post_meta($mypost, 'property_bedrooms', true)) . '">

        </p>

    </td>

    </tr>



    <tr>

    <td valign="top" align="left">  

        <p class="meta-options">

        <label for="property_bedrooms">'.__('Bathrooms: ','wpestate').'</label><br />

        <input type="text" id="property_bathrooms" size="40" name="property_bathrooms" value="' . esc_html(get_post_meta($mypost, 'property_bathrooms', true)) . '">

        </p>

    </td>

  

    </tr>

    <tr>';

     

     $option_video='';

     $video_values = array('vimeo', 'youtube');

     $video_type = get_post_meta($mypost, 'embed_video_type', true);



     foreach ($video_values as $value) {

         $option_video.='<option value="' . $value . '"';

         if ($value == $video_type) {

             $option_video.='selected="selected"';

         }

         $option_video.='>' . $value . '</option>';

     }

     

     

//    print'

//    <td valign="top" align="left">

//        <p class="meta-options">

//        <label for="embed_video_type">'.__('Video from ','wpestate').'</label><br />

//        <select id="embed_video_type" name="embed_video_type" style="width: 237px;">

//                ' . $option_video . '

//        </select>

//        </p>

//    </td>';

//

//

//    print'

//    <td valign="top" align="left">

//      <p class="meta-options">

//      <label for="embed_video_id">'.__('Embed Video id: ','wpestate').'</label> <br />

//        <input type="text" id="embed_video_id" name="embed_video_id" size="40" value="'.esc_html( get_post_meta($mypost, 'embed_video_id', true) ).'">

//      </p>

//    </td>

    print'</tr></table>';

}

endif; // end   details_estate_box  







///////////////////////////////////////////////////////////////////////////////////////////////////////////

///  Google map function

///////////////////////////////////////////////////////////////////////////////////////////////////////////

if( !function_exists('map_estate_box') ):

 

function map_estate_box($post) {

    wp_nonce_field(plugin_basename(__FILE__), 'estate_property_noncename');

    global $post;

    

    $mypost                 =   $post->ID;

    $gmap_lat               =   esc_html(get_post_meta($mypost, 'property_latitude', true));

    $gmap_long              =   esc_html(get_post_meta($mypost, 'property_longitude', true));

    $google_camera_angle    =   intval( esc_html(get_post_meta($mypost, 'google_camera_angle', true)) );

    $cache_array            =   array('yes','no');

    $keep_min_symbol        =   '';

    $keep_min_status        =   esc_html ( get_post_meta($post->ID, 'keep_min', true) );



    foreach($cache_array as $value){

            $keep_min_symbol.='<option value="'.$value.'"';

            if ($keep_min_status==$value){

                    $keep_min_symbol.=' selected="selected" ';

            }

            $keep_min_symbol.='>'.$value.'</option>';

    }

    

    print '<script type="text/javascript">

    //<![CDATA[

    jQuery(document).ready(function(){

            jQuery("#property_date").datepicker({

                    dateFormat : "yy-mm-dd"

            });

    });

    //]]>

    </script>

    <p class="meta-options"> 

    <div id="googleMap" style="width:100%;height:380px;margin-bottom:30px;"></div>    

    <p class="meta-options"> 

        <a class="button" href="#" id="admin_place_pin">'.__('Place Pin with Property Address','wpestate').'</a>

    </p>

    '.__('Latitude:','wpestate').'  <input type="text" id="property_latitude" style="margin-right:20px;" size="40" name="property_latitude" value="' . $gmap_lat . '">

    '.__('Longitude:','wpestate').' <input type="text" id="property_longitude" style="margin-right:20px;" size="40" name="property_longitude" value="' . $gmap_long . '">

    <p>

    <p class="meta-options"> 

    <input type="hidden" name="property_google_view" value="">';

//    print'<input type="checkbox"  id="property_google_view" name="property_google_view" value="1" ';

//        if (esc_html(get_post_meta($mypost, 'property_google_view', true)) == 1) {

//            print'checked="checked"';

//        }

//        print' />

//    <label for="property_google_view">'.__('Enable Google Street View','wpestate').'</label>

//

//

//    <label for="google_camera_angle" style="margin-left:50px;">'.__('Google View Camera Angle','wpestate').'</label>

//    <input type="text" id="google_camera_angle" style="margin-right:0px;" size="5" name="google_camera_angle" value="'.$google_camera_angle.'">

//

//    </p>';

        

    $page_custom_zoom  = get_post_meta($mypost, 'page_custom_zoom', true);

    if ($page_custom_zoom==''){

        $page_custom_zoom=16;

    }

    

    print '

     <p class="meta-options">

       <label for="page_custom_zoom">'.__('Zoom Level for map (1-20)','wpestate').'</label>

       <select name="page_custom_zoom" id="page_custom_zoom">';

      

      for ($i=1;$i<21;$i++){

           print '<option value="'.$i.'"';

           if($page_custom_zoom==$i){

               print ' selected="selected" ';

           }

           print '>'.$i.'</option>';

       }

        

     print'

       </select>

    ';     

}

endif; // end   map_estate_box 













///////////////////////////////////////////////////////////////////////////////////////////////////////////

///  Agent box function

///////////////////////////////////////////////////////////////////////////////////////////////////////////

if( !function_exists('agentestate_box') ):

function agentestate_box($post) {

    global $post;

    wp_nonce_field(plugin_basename(__FILE__), 'estate_property_noncename');

   

    $mypost         =   $post->ID;

    $originalpost   =   $post;

    $agent_list     =   '';

    $picked_agent   =   (get_post_meta($mypost, 'property_agent', true));



    $args = array(

       'post_type'      => 'estate_agent',

       'post_status'    => 'publish',

       'posts_per_page' => -1

       );

    

     $agent_selection  =  new WP_Query($args);



     while ($agent_selection->have_posts()){

           $agent_selection->the_post();  

           $the_id       =  get_the_ID();

           

           $agent_list  .=  '<option value="' . $the_id . '"  ';

           if ($the_id == $picked_agent) {

               $agent_list.=' selected="selected" ';

           }

           $agent_list.= '>' . get_the_title() . '</option>';

      }

      

      wp_reset_postdata();

      $post = $originalpost;

      

      print '

      <label for="property_zip">'.__('Agent Responsible: ','wpestate').'</label><br />

      <select id="property_agent" style="width: 237px;" name="property_agent">

            <option value="">none</option>

            <option value=""></option>

            '. $agent_list .'

      </select>';  

}

endif; // end   agentestate_box  











///////////////////////////////////////////////////////////////////////////////////////////////////////////

///  Features And Amenties function

///////////////////////////////////////////////////////////////////////////////////////////////////////////

if( !function_exists('amenities_estate_box') ):

function amenities_estate_box($post) {

    wp_nonce_field(plugin_basename(__FILE__), 'estate_property_noncename');

    global $post;

    $mypost             =   $post->ID;

    $feature_list_array =   array();

    $feature_list       =   esc_html( get_option('wp_estate_feature_list') );

    $feature_list_array =   explode( ',',$feature_list);

    $counter            =   0;

    

    print ' <table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>';

    foreach($feature_list_array as $key => $value){

        $counter++;

        $post_var_name=  str_replace(' ','_', trim($value) );

      

        if( ($counter-1) % 3 == 0){

            print'<tr>';

        }

        $input_name =   wpestate_limit45(sanitize_title( $post_var_name ));

        $input_name =   sanitize_key($input_name);

      

      

        print '     

        <td width="33%" valign="top" align="left">

            <p class="meta-options"> 

            <input type="hidden"    name="'.$input_name.'" value="">

            <input type="checkbox"  name="'.$input_name.'" value="1" ';

        

        if (esc_html(get_post_meta($mypost, $input_name, true)) == 1) {

            print' checked="checked" ';

        }

        print' />

            <label for="'.$input_name.'">'.$value.'</label>

            </p>

        </td>';

        if($counter % 3 == 0){

            print'</tr>';

        }

    }

    

    print '</table>';

}

endif; // end   amenities_estate_box  











///////////////////////////////////////////////////////////////////////////////////////////////////////////

/// Property custom fields

///////////////////////////////////////////////////////////////////////////////////////////////////////////

if( !function_exists('estate_box') ): 

function estate_box($post) {

    global $post;

    wp_nonce_field(plugin_basename(__FILE__), 'estate_property_noncename');

    $mypost = $post->ID;

    

    print' 

    <table width="100%" border="0" cellspacing="0" cellpadding="0" >

    <tr>

        <td width="33%" align="left" valign="top">

            <p class="meta-options">

            <label for="property_address">'.__('Address: ','wpestate').'</label><br />

            <textarea type="text" id="property_address"  size="40" name="property_address" rows="3" cols="42">' . esc_html(get_post_meta($mypost, 'property_address', true)) . '</textarea>

            </p>

        </td>

      

   

        <td align="left" valign="top">   

            <p class="meta-options">

            <label for="property_zip">'.__('Zip: ','wpestate').'</label><br />

            <input type="text" id="property_zip" size="40" name="property_zip" value="' . esc_html(get_post_meta($mypost, 'property_zip', true)) . '">

            </p>

        </td>



        <td align="left" valign="top">

            <p class="meta-options">

            <label for="property_country">'.__('Country: ','wpestate').'</label><br />



            ';

        print wpestate_country_list(esc_html(get_post_meta($mypost, 'property_country', true)));

        print '     

            </p>

        </td>



    

    </tr>



     <tr>';

      $status_values          =   esc_html( get_option('wp_estate_status_list') );

      $status_values_array    =   explode(",",$status_values);

      $prop_stat              =   get_post_meta($mypost, 'property_status', true);

      $property_status        =   '';





      foreach ($status_values_array as $key=>$value) {

          if (function_exists('icl_translate') ){

            $value     =   icl_translate('wpestate','wp_estate_property_status_'.$value, $value ) ;                                      

          }

          

          $value = trim($value);

          $property_status.='<option value="' . $value . '"';

          if ($value == $prop_stat) {

              $property_status.='selected="selected"';

          }

          $property_status.='>' . $value . '</option>';

      }





      print'

      <td align="left" valign="top">

           <p class="meta-options">

              <label for="property_status">'.__('Property Status:','wpestate').'</label><br />

              <select id="property_status" style="width: 237px;" name="property_status">

              <option value="normal">normal</option>

              ' . $property_status . '

              </select>

          </p>

      </td>



      <td align="left" valign="top">  

           <p class="meta-options"> 

              <input type="hidden" name="prop_featured" value="0">

              <input type="checkbox"  id="prop_featured" name="prop_featured" value="1" ';

              if (intval(get_post_meta($mypost, 'prop_featured', true)) == 1) {

                  print'checked="checked"';

              }

              print' />

              <label for="prop_featured">'.__('Make it Featured Property','wpestate').'</label>

          </p>

     </td>



      <td align="left" valign="top">          

      </td>

    </tr>

    </table>



    ';

}

endif; // end   estate_box 

















///////////////////////////////////////////////////////////////////////////////////////////////////////////

///  Country list function

///////////////////////////////////////////////////////////////////////////////////////////////////////////

if( !function_exists('wpestate_country_list') ): 

function wpestate_country_list($selected,$class='') {

    $countries = array("Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique","Montenegro", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles","Serbia", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Zambia", "Zimbabwe");

    $country_select = '<select id="property_country"  name="property_country" class="'.$class.'">';



    if ($selected == '') {

        $selected = get_option('wp_estate_general_country');

    }

    foreach ($countries as $country) {

        $country_select.='<option value="' . $country . '"';

        if ($selected == $country) {

            $country_select.='selected="selected"';

        }

        $country_select.='>' . $country . '</option>';

    }



    $country_select.='</select>';

    return $country_select;

}

endif; // end   wpestate_country_list 







if( !function_exists('wpestate_agent_list') ):

function wpestate_agent_list($mypost) {

    return $agent_list;

}

endif; // end   wpestate_agent_list







///////////////////////////////////////////////////////////////////////////////////////////////////////////

///  Manage property lists

///////////////////////////////////////////////////////////////////////////////////////////////////////////

add_filter( 'manage_edit-estate_property_columns', 'wpestate_my_columns' );



if( !function_exists('wpestate_my_columns') ):

function wpestate_my_columns( $columns ) {

    $slice=array_slice($columns,2,2);

    unset( $columns['comments'] );

    unset( $slice['comments'] );

    $splice=array_splice($columns, 2);   

    $columns['estate_action']   = __('Action','wpestate');

    $columns['estate_category'] = __( 'Category','wpestate');

    $columns['estate_autor']    = __('User','wpestate');

    $columns['estate_status']   = __('Status','wpestate');

    $columns['estate_price']    = __('Price','wpestate');

    $columns['estate_featured'] = __('Featured','wpestate');

    return  array_merge($columns,array_reverse($slice));

}

endif; // end   wpestate_my_columns  





add_action( 'manage_posts_custom_column', 'wpestate_populate_columns' );

if( !function_exists('wpestate_populate_columns') ):

function wpestate_populate_columns( $column ) {

    

     if ( 'estate_status' == $column ) {

        $estate_status = get_post_status(get_the_ID()); 

        if($estate_status=='publish'){

            echo __('published','wpestate');

        }else{

            echo $estate_status;

        }

        

        $pay_status    = get_post_meta(get_the_ID(), 'pay_status', true);

        if($pay_status!=''){

            echo " | ".$pay_status;

        }

        

    } 

    

    if ( 'estate_autor' == $column ) {

        $estate_autor = get_the_author_meta('display_name');; 

        echo $estate_autor;

    } 

    

    if ( 'estate_action' == $column ) {

        $estate_action = get_the_term_list( get_the_ID(), 'property_action_category', '', ', ', '');

        echo $estate_action;

    }

    elseif ( 'estate_category' == $column ) {

        $estate_category =  get_the_term_list( get_the_ID(), 'property_category', '', ', ', '');

        echo $estate_category ;

    }

    

    

    

    

    

    if ( 'estate_price' == $column ) {

        $currency                   =   esc_html( get_option('wp_estate_currency_symbol', '') );

        $where_currency             =   esc_html( get_option('wp_estate_where_currency_symbol', '') );

        

        $price = intval( get_post_meta(get_the_ID(), 'property_price', true) );

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

        

        echo $price.' '. get_post_meta(get_the_ID(), 'property_label', true);

    }

    

    

    

    

    if ( 'estate_featured' == $column ) {

        $estate_featured = get_post_meta(get_the_ID(), 'prop_featured', true); 

        if($estate_featured==1){

            $estate_featured=__('Yes','wpestate');

        }else{

            $estate_featured=__('No','wpestate'); 

        }

        echo $estate_featured;

    }

}

endif; // end   wpestate_populate_columns 











            //'manage_edit-estate_property_columns

add_filter( 'manage_edit-estate_property_sortable_columns', 'wpestate_sort_me' );

if( !function_exists('wpestate_sort_me') ):

function wpestate_sort_me( $columns ) {

    $columns['estate_action']       = 'estate_action';

    $columns['estate_category']     = 'estate_category';

    $columns['estate_autor']        = 'estate_autor';

    $columns['estate_status']       = 'estate_status';

    $columns['estate_featured']       = 'estate_featured';

    $columns['estate_price']       = 'estate_price';

    return $columns;

}

endif; // end   wpestate_sort_me 















///////////////////////////////////////////////////////////////////////////////////////////////////////////

// Tie area with city

///////////////////////////////////////////////////////////////////////////////////////////////////////////

add_action( 'property_area_edit_form_fields',   'property_area_callback_function', 10, 2);

add_action( 'property_area_add_form_fields',    'property_area_callback_add_function', 10, 2 );  

add_action( 'created_property_area',            'property_area_save_extra_fields_callback', 10, 2);

add_action( 'edited_property_area',             'property_area_save_extra_fields_callback', 10, 2);

add_filter('manage_edit-property_area_columns', 'ST4_columns_head');  

add_filter('manage_property_area_custom_column','ST4_columns_content_taxonomy', 10, 3); 





if( !function_exists('ST4_columns_head') ):

function ST4_columns_head($new_columns) {   

 

    $new_columns = array(

        'cb'            => '<input type="checkbox" />',

        'name'          => __('Name','wpestate'),

        'city'          => __('City','wpestate'),

        'header_icon'   => '',

        'slug'          => __('Slug','wpestate'),

        'posts'         => __('Posts','wpestate')

        );

    

    

    return $new_columns;

} 

endif; // end   ST4_columns_head  





if( !function_exists('ST4_columns_content_taxonomy') ):

function ST4_columns_content_taxonomy($out, $column_name, $term_id) {  

    if ($column_name == 'city') {    

        $term_meta= get_option( "taxonomy_$term_id");

        print $term_meta['cityparent'] ;

    }  

}  

endif; // end   ST4_columns_content_taxonomy  









if( !function_exists('property_area_callback_add_function') ):

function property_area_callback_add_function($tag){

    if(is_object ($tag)){

        $t_id = $tag->term_id;

        $term_meta = get_option( "taxonomy_$t_id");

        $cityparent=$term_meta['cityparent'] ? $term_meta['cityparent'] : ''; 

        $cityparent=wpestate_get_all_cities($cityparent);

    }else{

        $cityparent=wpestate_get_all_cities();

    }

   

    print'

        <div class="form-field">

	<label for="term_meta[cityparent]">'. __('Which city has this area','wpestate').'</label>

            <select name="term_meta[cityparent]" class="postform">  

                '.$cityparent.'

            </select>

	</div>

	';

}

endif; // end   property_area_callback_add_function  









if( !function_exists('property_area_callback_function') ):



function property_area_callback_function($tag){

    if(is_object ($tag)){

        $t_id       =   $tag->term_id;

        $term_meta  =   get_option( "taxonomy_$t_id");

        $cityparent =   $term_meta['cityparent'] ? $term_meta['cityparent'] : ''; 

        $cityparent =   wpestate_get_all_cities($cityparent);

    }else{

        $cityparent =   wpestate_get_all_cities();

    }

   

    print'

        <table class="form-table">

        <tbody>

                <tr class="form-field">

			<th scope="row" valign="top"><label for="term_meta[cityparent]">'. __('Which city has this area','wpestate').'</label></th>

                        <td> 

                            <select name="term_meta[cityparent]" class="postform">  

                             '.$cityparent.'

                                </select>

                            <p class="description">'.__('City that has this area','wpestate').'</p>

                        </td>

		</tr>

          </tbody>

         </table>';

}

endif; // end   property_area_callback_function  







if( !function_exists('wpestate_get_all_cities') ): 

function wpestate_get_all_cities($selected=''){

    $taxonomy       =   'property_city';

    $args = array(

        'hide_empty'    => false

    );

    $tax_terms      =   get_terms($taxonomy,$args);

    $select_city    =   '';

    

    foreach ($tax_terms as $tax_term) {             

        $select_city.= '<option value="' . $tax_term->name.'" ';

        if($tax_term->name == $selected){

            $select_city.= ' selected="selected" ';

        }

        $select_city.= ' >' . $tax_term->name . '</option>'; 

    }

    return $select_city;

}

endif; // end   wpestate_get_all_cities 









if( !function_exists('property_area_save_extra_fields_callback') ):

function property_area_save_extra_fields_callback($term_id ){

      if ( isset( $_POST['term_meta'] ) ) {

        $t_id = $term_id;

        $term_meta = get_option( "taxonomy_$t_id");

        $cat_keys = array_keys($_POST['term_meta']);

        $allowed_html   =   array();

            foreach ($cat_keys as $key){

               $key=sanitize_key($key);

                if (isset($_POST['term_meta'][$key])){

                    $term_meta[$key] =  wp_kses( $_POST['term_meta'][$key],$allowed_html);

                }

            }

        //save the option array

        update_option( "taxonomy_$t_id", $term_meta );

    }

}

endif; // end   property_area_save_extra_fields_callback  





add_action( 'init', 'wpestate_my_custom_post_status' );

if( !function_exists('wpestate_my_custom_post_status') ):

function wpestate_my_custom_post_status(){

	register_post_status( 'expired', array(

		'label'                     => __( 'expired', 'wpestate' ),

		'public'                    => true,

		'exclude_from_search'       => false,

                'show_in_admin_all_list'    => true,

		'show_in_admin_status_list' => true,

		'label_count'               => _n_noop( 'Membership Expired <span class="count">(%s)</span>', 'Membership Expired <span class="count">(%s)</span>' ),

	) );

}

endif; // end   wpestate_my_custom_post_status  



?>