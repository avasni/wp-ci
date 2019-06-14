<?php

/* ---------------------------------------------------------------------------
 * Child Theme URI | DO NOT CHANGE
 * --------------------------------------------------------------------------- */
define( 'CHILD_THEME_URI', get_stylesheet_directory_uri() );


/* ---------------------------------------------------------------------------
 * Define | YOU CAN CHANGE THESE
 * --------------------------------------------------------------------------- */

// White Label --------------------------------------------
define( 'WHITE_LABEL', false );

// Static CSS is placed in Child Theme directory ----------
define( 'STATIC_IN_CHILD', false );


/* ---------------------------------------------------------------------------
 * Enqueue Style
 * --------------------------------------------------------------------------- */
add_action( 'wp_enqueue_scripts', 'mfnch_enqueue_styles', 101 );
function mfnch_enqueue_styles() {

	// Enqueue the parent stylesheet
// 	wp_enqueue_style( 'parent-style', get_template_directory_uri() .'/style.css' );		//we don't need this if it's empty

	// Enqueue the parent rtl stylesheet
	if ( is_rtl() ) {
		wp_enqueue_style( 'mfn-rtl', get_template_directory_uri() . '/rtl.css' );
	}

	// Enqueue the child stylesheet
	wp_dequeue_style( 'style' );
	wp_enqueue_style( 'style', get_stylesheet_directory_uri() .'/style.css' );

}


/* ---------------------------------------------------------------------------
 * Load Textdomain
 * --------------------------------------------------------------------------- */
add_action( 'after_setup_theme', 'mfnch_textdomain' );
function mfnch_textdomain() {
    load_child_theme_textdomain( 'betheme',  get_stylesheet_directory() . '/languages' );
    load_child_theme_textdomain( 'mfn-opts', get_stylesheet_directory() . '/languages' );
}


/* PDF Code */
add_action('wpcf7_before_send_mail', 'wpcf7_update_email_body');
function wpcf7_update_email_body($contact_form) 
{
	$submission = WPCF7_Submission::get_instance();	
	if($submission) 
	{		
		//define ('FPDF_PATH',get_template_directory().'/fpdf/'); // MAKE SURE THIS POINTS TO THE DIRECTORY IN YOUR THEME FOLDER THAT HAS FPDF.PHP
		
		$themePath = "http://fuelyourlife.com.au/wp-content/themes/betheme-child";  // get_stylesheet_directory_uri();
		$themeDirectoryPath = "/home/allie073/fuelyourlife.com.au/wp-content/themes/betheme-child";
		define ('FPDF_PATH', $themeDirectoryPath.'/fpdf/');
		
		//require(FPDF_PATH.'fpdf.php');
		//require(FPDF_PATH.'WriteHTML.php');
		require(FPDF_PATH.'html_table.php');
		$posted_data = $submission->get_posted_data();				
		if($posted_data["_wpcf7"] == "226")
		{			
			// SAVE FORM FIELD DATA AS VARIABLES
			$patientname = $posted_data["patientname"];
			$patientsurname = $posted_data["patientsurname"];
			$patientemail = $posted_data["patientemail"];
			$patientdob = $posted_data["patientdob"];
			$patientaddress = $posted_data["patientaddress"];
			$stateterritory = $posted_data["stateterritory"];
			$postcode = $posted_data["postcode"];
			$patientphonenumber = $posted_data["patientphonenumber"];
			$Services = $posted_data["Services"];
			$dvacardifapplicable = $posted_data["dvacardifapplicable"];
			$dvscardnoifapplicable = $posted_data["dvscardnoifapplicable"];
			$whitecardcondition = $posted_data["whitecardcondition"];
			$nameofgeneralpractitionerifapplicable = $posted_data["nameofgeneralpractitionerifapplicable"];
			$gptelephone = $posted_data["gptelephone"];
			$gpfaxno = $posted_data["gpfaxno"];
			$medicareno = $posted_data["medicareno"];
			$privatehealthinsurancefundifapplicable = $posted_data["privatehealthinsurancefundifapplicable"];
			$privatehealthinsurancenumberifapplicable = $posted_data["privatehealthinsurancenumberifapplicable"];
			$currentmedicalconditions = $posted_data["currentmedicalconditions"];
			$dpnumber = $posted_data["dpnumber"];
			$ndpractice = $posted_data["ndpractice"];
			$npsubmittingreferral = $posted_data["npsubmittingreferral"];
			$whitecardcondition = $posted_data["whitecardcondition"];
			$HearAboutUs = $posted_data["HearAboutUs"];
			
			$doctorsurname = "";
			$clinicname = "";
			$clinicfaxnumber = "";
									
			// Content Of PDF File 1...
			
			/* <b>Dear Dr. </b> '.$doctorsurname.' <br>
			<b>Clinic :</b> '.$clinicname.' <br>
			<b>Fax :</b> '.$clinicfaxnumber.' <br> */
			
			//$image_path = get_template_directory_uri().'/logo1.jpg';	
			//$image_path1 = get_template_directory_uri().'/logo.png';
			
			$image_path = $themePath.'/fpdf/logo1.jpg';	
			$image_path1 = $themePath.'/fpdf/logo.png';
			
			$today = date("Y-m-d");
			$return_html = '<br><br><br>Date: '.$today.'
			
			<p><b>Dear Dr. </b>'.$nameofgeneralpractitionerifapplicable.'<br>
			Clinic: '.$ndpractice.'<br>
			Fax: '.$gpfaxno.'</p>
			
		                                                   <p style="text-align:center;"><b> RE: 	REQUEST FOR D904 - DVA REFERRAL </b></p>
			
			<p><b>'.$patientname.' '.$patientsurname.'</b> is interested in taking part in the specific dietitian services program provided by <b>Fuel Your Life.</b> This is a comprehensive nutrition program that will greatly assist <b>'.$patientname.' '.$patientsurname.' </b> in assisting the management of their conditions and help achieve their health and weight goals.  So that we can provide these services and meet the requirements of the Department of Veteran Affairs, we ask that you could please assess the client and complete this referral if you believe a dietetic intervention will assist in the management of this client\'s condition/s.</p>
			
			<p><b>Veteran consented to referral: </b>Yes</p>
			
			<p style="text-align:center;"><b>COULD YOU PLEASE COMPLETE AND FAX A D904 FORM FOR:</b></p>
			
			<p><b>Patient : </b>'.$patientname.' '.$patientsurname.'                          DOB : '.$patientdob.'.<br>
			Address:  '.$patientaddress.'<br>
			Phone: '.$patientphonenumber.'<br>
			DVA file #: '.$dvscardnoifapplicable.'<br>
			White Card conditions (if applicable): '.$whitecardcondition.'</p>		
			<p><b><i>Dietitian</i></b>                                                                                     <b><i>Doctor</i></b>  </p>         
			<p><b>Business name:</b> Fuel Your Life                                                 <b>Name: </b>      _________________________________</p>
			<p><b>Postal address:</b> PO Box 303, Bli Bli, QLD 4560                                           _________________________________</p>
			<p><b>Name:</b> Tyson Tripcony                                                              <b>Provider #: </b>_________________________________</p>
            <p><b>Provider #:</b> 449735TW                                                                                 _________________________________</p>
            <p><b>Phone:</b> 0401 302 872                                                        		        <b>Condition/s to be treated: </b></p>
			<p><b>Fax:</b> (07) 3905 1855                                                       		           ___________________________________________</p>
			<p>                                                                                    		               ___________________________________________</p>
			<p>                                                                                    		               ___________________________________________</p>
			<p>                                                                                                 <b>Signature: </b>______________________  <b>Date:</b>_______</p>
			
			<p>Kind regards,<br>
			<b>Tyson Tripcony</b><br><span><i>Accredited Practising Dietitian</i><span><br><span><i>Managing Director - Fuel Your Life</i></span></p>';
			
	    	// PDF File 1 Created with Rename or Sending Code...
		
    		$date = date_create();
    		$date_time = date_format($date, 'Y_m_d_H_i_s');
    		$random_string = implode( str_split( substr( strtoupper( md5( time() . rand( 1000, 9999 ) ) ), 0, 5 ), 4 ) );
    		global $filename;
    		
    		$filename = 'referral_form_'.$patienttelephone.'_'.$date_time.'_'.$random_string.'_1.pdf';			
    						
    		$pdf = new PDF();
    		$pdf->AddPage();
    		//$pdf->Image($image_path);
    		$pdf->Image($image_path,10,12,20); // $pdf->Image($image_path,10,10,70); // $pdf->Image($image_path,10,0,30);
    		$pdf->Image($image_path1,150,10,20);  // $pdf->Image($image_path1,150,10,30);
    		$pdf->SetFont('Arial','',10);
    		$pdf->WriteHTML($return_html);
    		$pdf->Output(FPDF_PATH.$filename,'F');						
		
    		$file_open_path = get_template_directory_uri().'/fpdf/'.$filename;		
    		
    		if($file_open_path != "")
    		{
    			$_SESSION['pdf_1'] = $file_open_path;
    		}	
		
		}
	}
}

/*
add_filter( 'my_ninja_forms_savepdf', 'my_ninja_forms_savepdf_fun' );
function my_ninja_forms_savepdf_fun( $form_data ) {
   //define ('FPDF_PATH',get_template_directory().'/fpdf/'); // MAKE SURE THIS POINTS TO THE DIRECTORY IN YOUR THEME FOLDER THAT HAS FPDF.PHP
		
		$themePath = "http://fuelyourlife.com.au/wp-content/themes/betheme-child";  // get_stylesheet_directory_uri();
		$themeDirectoryPath = "/home/allie073/fuelyourlife.com.au/wp-content/themes/betheme-child";
		define ('FPDF_PATH', $themeDirectoryPath.'/fpdf/');
		
		
		$themeDirectoryPath2 = "/home/allie073/fuelyourlife.com.au/wp-content/uploads";
		define ('FPDF_PATH2', $themeDirectoryPath2.'/fpdf/');
		
		
		//require(FPDF_PATH.'fpdf.php');
		//require(FPDF_PATH.'WriteHTML.php');
		require(FPDF_PATH.'html_table.php');
		$posted_data = $form_data[ 'fields' ];		
		$form_id=$form_data[ 'form_id' ];
   		if ($form_id == 1) 		
		{		
	// SAVE FORM FIELD DATA AS VARIABLES
			$patientname = $form_data[ 'fields' ]['1']['value'];
			$patientsurname = $form_data[ 'fields' ]['5']['value'];
			$patientemail = $form_data[ 'fields' ]['6']['value'];
			$patientdob = $form_data[ 'fields' ]['7']['value'];
			$patientaddress = $form_data[ 'fields' ]['30']['value'];
			$stateterritory = $form_data[ 'fields' ]['8']['value'];
			$postcode = $form_data[ 'fields' ]['9']['value'];
			$patientphonenumber = $form_data[ 'fields' ]['10']['value'];
			$Services = $form_data[ 'fields' ]['11']['value'];
			$dvacardifapplicable = $form_data[ 'fields' ]['32']['value'];
			$dvscardnoifapplicable = $form_data[ 'fields' ]['13']['value'];
			$whitecardcondition = $form_data[ 'fields' ]['14']['value'];
			$nameofgeneralpractitionerifapplicable = $form_data[ 'fields' ]['15']['value'];
			$gptelephone = $form_data[ 'fields' ]['17']['value'];
			$gpfaxno = $form_data[ 'fields' ]['18']['value'];
			$medicareno = $form_data[ 'fields' ]['19']['value'];
			$privatehealthinsurancefundifapplicable = $form_data[ 'fields' ]['20']['value'];
			$privatehealthinsurancenumberifapplicable = $form_data[ 'fields' ]['21']['value'];
			$currentmedicalconditions = $form_data[ 'fields' ]['22']['value'];
			$dpnumber = $form_data[ 'fields' ]['23']['value'];
			$ndpractice = $form_data[ 'fields' ]['24']['value'];
			$npsubmittingreferral = $form_data[ 'fields' ]['25']['value'];
			$whitecardcondition = '';
			$HearAboutUs = $posted_data["HearAboutUs"];
			
			$doctorsurname = "";
			$clinicname = "";
			$clinicfaxnumber = "";
									
			// Content Of PDF File 1...
			
			//$image_path = get_template_directory_uri().'/logo1.jpg';	
			//$image_path1 = get_template_directory_uri().'/logo.png';
			
			$image_path = $themePath.'/fpdf/logo1.jpg';	
			$image_path1 = $themePath.'/fpdf/logo.png';
			
			$today = date("Y-m-d");
			$return_html = '<br><br><br>Date: '.$today.'
			
			<p><b>Dear Dr. </b>'.$nameofgeneralpractitionerifapplicable.'<br>
			Clinic: '.$ndpractice.'<br>
			Fax: '.$gpfaxno.'</p>
			
		                                                   <p style="text-align:center;"><b> RE: 	REQUEST FOR D904 - DVA REFERRAL </b></p>
			
			<p><b>'.$patientname.' '.$patientsurname.'</b> is interested in taking part in the specific dietitian services program provided by <b>Fuel Your Life.</b> This is a comprehensive nutrition program that will greatly assist <b>'.$patientname.' '.$patientsurname.' </b> in assisting the management of their conditions and help achieve their health and weight goals.  So that we can provide these services and meet the requirements of the Department of Veteran Affairs, we ask that you could please assess the client and complete this referral if you believe a dietetic intervention will assist in the management of this client\'s condition/s.</p>
			
			<p><b>Veteran consented to referral: </b>Yes</p>
			
			<p style="text-align:center;"><b>COULD YOU PLEASE COMPLETE AND FAX A D904 FORM FOR:</b></p>
			
			<p><b>Patient : </b>'.$patientname.' '.$patientsurname.'                          DOB : '.$patientdob.'.<br>
			Address:  '.$patientaddress.'<br>
			Phone: '.$patientphonenumber.'<br>
			DVA file #: '.$dvscardnoifapplicable.'<br>
			White Card conditions (if applicable): '.$whitecardcondition.'</p>		
			<p><b><i>Dietitian</i></b>                                                                                     <b><i>Doctor</i></b>  </p>         
			<p><b>Business name:</b> Fuel Your Life                                                 <b>Name: </b>      _________________________________</p>
			<p><b>Postal address:</b> PO Box 303, Bli Bli, QLD 4560                                           _________________________________</p>
			<p><b>Name:</b> Tyson Tripcony                                                              <b>Provider #: </b>_________________________________</p>
            <p><b>Provider #:</b> 449735TW                                                                                 _________________________________</p>
            <p><b>Phone:</b> 0401 302 872                                                        		        <b>Condition/s to be treated: </b></p>
			<p><b>Fax:</b> (07) 3905 1855                                                       		           ___________________________________________</p>
			<p>                                                                                    		               ___________________________________________</p>
			<p>                                                                                    		               ___________________________________________</p>
			<p>                                                                                                 <b>Signature: </b>______________________  <b>Date:</b>_______</p>
			
			<p>Kind regards,<br>
			<b>Tyson Tripcony</b><br><span><i>Accredited Practising Dietitian</i><span><br><span><i>Managing Director - Fuel Your Life</i></span></p>';
			
	    	// PDF File 1 Created with Rename or Sending Code...
		
    		$date = date_create();
    		$date_time = date_format($date, 'Y_m_d_H_i_s');
    		$random_string = implode( str_split( substr( strtoupper( md5( time() . rand( 1000, 9999 ) ) ), 0, 5 ), 4 ) );
    		global $filename;
    		
    		$filename = 'referral_form_'.$patienttelephone.'_'.$date_time.'_'.$random_string.'_1.pdf';			
    						
    		$pdf = new PDF();
    		$pdf->AddPage();
    		//$pdf->Image($image_path);
    		$pdf->Image($image_path,10,12,20); // $pdf->Image($image_path,10,10,70); // $pdf->Image($image_path,10,0,30);
    		$pdf->Image($image_path1,150,10,20);  // $pdf->Image($image_path1,150,10,30);
    		$pdf->SetFont('Arial','',10);
    		$pdf->WriteHTML($return_html);
    		$pdf->Output(FPDF_PATH2.$filename,'F');						
		
    		$file_open_path = get_template_directory_uri().'/fpdf/'.$filename;		
    		
    		if($file_open_path != "")
    		{
    			$_SESSION['pdf_1'] = $file_open_path;
    		}	
		
		}
}


add_filter( 'ninja_forms_action_email_attachments', function( $attachments, $data, $settings ) {
     global $filename;
     
		   $attachments = array(WP_CONTENT_DIR . '/uploads/fpdf/'.$filename);
		    //get_template_directory_uri().'/fpdf/'.$filename;
             return $attachments; 
}, 10, 3 );


*/



add_filter( 'wpcf7_mail_components', 'mycustom_wpcf7_mail_components' );
function mycustom_wpcf7_mail_components($components)
{	
    if (empty($components['attachments'])) 	
	{
		global $filename;
		if($filename != "")
		{
			// PDF File Send to admin...
			
			if($components['recipient'] == "admin@fuelyourlife.com.au")  // admin@fuelyourlife.com.au
			{
			   
				$components['attachments'] = array(FPDF_PATH .$filename); // ATTACH THE NEW PDF THAT WAS SAVED ABOVE			
			}		
		}			
	}	
	return $components;
}










function location_shortcode( $atts ) {
    return $_GET["q"];
}
add_shortcode( 'get_var_location', 'location_shortcode');


function location_map( $atts ) {
    echo "<iframe style='border: 0;' src='https://maps.google.com/maps?q=". $_GET['l']."&z=10&amp;output=embed' width='600' height='450' frameborder='0' allowfullscreen='allowfullscreen'></iframe>";
}
add_shortcode( 'get_map_location', 'location_map');

add_action( 'my_ninja_forms_processing', 'submit_data_to_onedrive' );
function submit_data_to_onedrive( $form_data){
	$form_id=$form_data[ 'form_id' ];
	
   if ($form_id == 1) {
        switch ($form_data[ 'fields' ]['8']['value']) {
	          case "QLD Sites":
	              $link= "https://graph.microsoft.com/v1.0/me/drive/items/D527D0C8BCF0824F!173563/workbook/worksheets/QLD/tables/3/rows/add";
	           break;
	          case "SA":
	              $link= "https://graph.microsoft.com/v1.0/me/drive/items/D527D0C8BCF0824F!173563/workbook/worksheets/SA/tables/5/rows/add";
	           break;
	           case "NT":
	              $link= "https://graph.microsoft.com/v1.0/me/drive/items/D527D0C8BCF0824F!173563/workbook/worksheets/NT/tables/10/rows/add";
	           break;
	           case "Tasmania":
	              $link= "https://graph.microsoft.com/v1.0/me/drive/items/D527D0C8BCF0824F!173563/workbook/worksheets/TAS/tables/11/rows/add";
	           break;
	           case "WA Sites":
	              $link= "https://graph.microsoft.com/v1.0/me/drive/items/D527D0C8BCF0824F!173563/workbook/worksheets/WA/tables/15/rows/add";
	           break;
	            case "NSW Sites":
	              $link= "https://graph.microsoft.com/v1.0/me/drive/items/D527D0C8BCF0824F!173563/workbook/worksheets/NSW/tables/16/rows/add";
	           break;
	            case "VIC":
	              $link= "https://graph.microsoft.com/v1.0/me/drive/items/D527D0C8BCF0824F!173563/workbook/worksheets/VIC/tables/17/rows/add";
	           break;
				case "ACT":
 					$link= "https://graph.microsoft.com/v1.0/me/drive/items/D527D0C8BCF0824F!173563/workbook/worksheets/ACT/tables/1/rows/add";
	           break;

	      }
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://login.microsoftonline.com/common/oauth2/v2.0/token",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => "client_id=447cb75b-6ea6-422f-9f9e-50d7e8e38057&client_secret=rkmiuBFUP921*!bdWSX12^$&scope=offline_access%20https%3A%2F%2Fgraph.microsoft.com%2FUser.Read%20https%3A%2F%2Fgraph.microsoft.com%2FFiles.Read%20https%3A%2F%2Fgraph.microsoft.com%2FUser.Read%20%20Files.ReadWrite.All&grant_type=refresh_token&redirect_uri=https%3A%2F%2Ffuelyourlife.com.au%2Finternal-referral-form%2F&refresh_token=MCa20JpKgqq1NmiBL*XyqgqSvM8BUCR1cJEUuuEpMDrhNVmpO6G7AQ62Vo0moH1nbHfcdNzknrVZHjf*M7p0eK5LNMm8QN7TQO6grd!3fNPtPYnDb0JGp4hoeJcx6fVh8mDsLhhwl!oltDkp8xr0x88DOWU0PujbLtR1jTH3tzXW*FFDU4p0hRp3znr4*l0XUN5opWBf3uanbjz!dfwx6gB1CjFFcihaCgk089yvMApey4R!ntsPFUvOFFBCITjMYteHnhqivdqRrwa1KsZtN5WjnYj1TFjYdSOcEJERmsZufJ6zOI0GG3obTFRVaRXW4oHw9RLXJEtTxhWLvthee3P46eomnRMV8SQKwwAhjuGOZqv!Tv*qnidL0PhogBingOVu*FnWb6UCUT2qfQiye66zILW6WyUxg20OcQk5HRzn0bSLM!2Jt5yblfRYoeNX*i5UI4aJu!IddvRk0yP!LjihsV4T9rLa3WAJz5MW**GC0tDqtai8BtklNfidQh6IH8w$$",
		  CURLOPT_HTTPHEADER => array(
		    "cache-control: no-cache",
		    "content-type: application/x-www-form-urlencoded",

		  ),
		));
		$response = curl_exec($curl);
		$response =json_decode($response, true);
		
		$err = curl_error($curl);

		curl_close($curl);

		$token= $response["access_token"];
			
        $curl2 = curl_init();
        date_default_timezone_set('Australia/Brisbane');
        $today=date("Y-m-d");
		$FundingType='';
		foreach ($form_data[ 'fields' ]['29']['value'] as $value)
			$FundingType=$FundingType.''.$value.',';

		curl_setopt_array($curl2, array(
		  CURLOPT_URL => $link,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
	      CURLOPT_POSTFIELDS => "{\n  \"values\": [\n    [\n      \"".$form_data[ 'fields' ]['5']['value']."\",\n      \"".$form_data[ 'fields' ]['1']['value']."\",\n       \"".$today."\",\n      \"\",\n     \"\",\n      \"\",\n      \"\",\n      \"\",\n      \"".$form_data[ 'fields' ]['24']['value']."\",\n  \"".$form_data[ 'fields' ]['15']['value']."\",\n   \"".$form_data[ 'fields' ]['17']['value']."\",\n \"".$form_data[ 'fields' ]['23']['value']."\",\n \"".$form_data[ 'fields' ]['18']['value']."\",\n         \"".$form_data[ 'fields' ]['10']['value']."\",\n       \"".$form_data[ 'fields' ]['30']['value']."\",\n     \"".$form_data[ 'fields' ]['9']['value']."\",\n       \"".$form_data[ 'fields' ]['7']['value']."\",\n      \"".$form_data[ 'fields' ]['6']['value']."\",\n       \"".$FundingType."\",\n      \"".$form_data[ 'fields' ]['13']['value']."\",\n      \"".$form_data[ 'fields' ]['32']['value']."\",\n       \"".$form_data[ 'fields' ]['13']['value']."\",\n       \"\",\n       \"\",\n      \"\",\n      \"\",\n      \"".$form_data[ 'fields' ]['11']['value']."\",\n     \"".$form_data[ 'fields' ]['14']['value']."\",\n     \"".$form_data[ 'fields' ]['19']['value']."\",\n     \"".$form_data[ 'fields' ]['20']['value']."\",\n       \"".$form_data[ 'fields' ]['21']['value']."\",\n        \"".$form_data[ 'fields' ]['25']['value']."\",\n        \"".$form_data[ 'fields' ]['26']['value']."\",\n     ]\n  ]\n}",
		  CURLOPT_HTTPHEADER => array(
		    "cache-control: no-cache",
		    "content-type: application/json",
		    "authorization: Bearer ".$token
		  ),
		));
        $info = curl_getinfo($curl2);
		$response2 = curl_exec($curl2);
		
		$response2 =json_decode($response2, true);
		$err = curl_error($curl2);
		
		
		
	    
   }
}
function _remove_script_version( $src ){
    $parts = explode( '?ver', $src );
    return $parts[0];
}

add_filter( 'script_loader_src', '_remove_script_version', 15, 1 );
add_filter( 'style_loader_src', '_remove_script_version', 15, 1 );