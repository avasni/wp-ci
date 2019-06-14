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
			
			<p><b>'.$patientname.' '.$patientsurname.'</b> is interested in taking part in the specific dietitian services program provided by <b>Fuel Your Life.</b> This is a comprehensive nutrition program that will greatly assist <b>'.$patientname.' '.$patientsurname.' </b> in assisting the management of their conditions and help achieve their health and weight goals.  So that we can provide these services and meet the requirements of the Department of Veteran Affairs, we ask that you could please assess the client and complete this referral if you believe a dietetic intervention will assist in the management of this client’s condition/s.</p>
			
			<p><b>Veteran consented to referral: </b>Yes</p>
			
			<p style="text-align:center;"><b>COULD YOU PLEASE COMPLETE AND FAX A D904 FORM FOR:</b></p>
			
			<p><b>Patient : </b>'.$patientname.' '.$patientsurname.'                          DOB : '.$patientdob.'.<br>
			Address:  '.$patientaddress.'<br>
			Phone: '.$patientphonenumber.'<br>
			DVA file #: '.$dvscardnoifapplicable.'<br>
			White Card conditions (if applicable): '.$whitecardcondition.'</p><br>		
			<p><b><i>Dietitian</i></b>                                                                                     <b><i>Doctor</i></b>  </p>         
			<p><b>Business name:</b> Fuel Your Life                                                 <b>Name: </b>      _________________________________</p>
			<p><b>Postal address:</b> PO Box 303, BliBli, QLD 4560                                           _________________________________</p>
			<p><b>Name:</b> Tyson Tripcony                                                              <b>Provider #: </b>_________________________________</p>
            <p><b>Provider #:</b> 449735TW                                                                                 _________________________________</p>
            <p><b>Phone:</b> 0401 302 872                                                        		        <b>Condition/s to be treated: </b></p>
			<p><b>Fax:</b> (07) 3905 1855                                                       		           ___________________________________________</p>
			<p>                                                                                    		               ___________________________________________</p>
			<p>                                                                                    		               ___________________________________________</p>
			
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