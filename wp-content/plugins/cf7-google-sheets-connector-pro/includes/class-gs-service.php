<?php
/**
 * Service class for Google Sheet Connector
 * @since 1.0
 */
if (!defined('ABSPATH')) {
   exit; // Exit if accessed directly
}

/**
 * Gs_Connector_Service Class
 *
 * @since 1.0
 */
class Gs_Connector_Service {

   /**
    * Custom tags by plugin
    * @var array
    */
   private $allowed_tags = array( 'text', 'email', 'url', 'tel', 'number', 'range', 'date', 'textarea', 'select', 'checkbox', 'radio', 'acceptance', 'quiz', 'file', 'hidden' );
   private $special_mail_tags = array('date', 'time', 'serial_number', 'remote_ip', 'user_agent', 'user_login', 'user_email', 'user_display_name', 'user_url', 'user_first_name', 'user_last_name', 'user_nickname', 'url', 'post_id', 'post_name', 'post_title', 'post_url', 'post_author', 'post_author_email', 'site_title', 'site_description', 'site_url', 'site_admin_email', 'invalid_fields');
   protected $gs_uploads = array();

   /**
    *  Set things up.
    *  @since 1.0
    */
   public function __construct() {
      
      add_action( 'wp_ajax_verify_gs_integation', array( $this, 'verify_gs_integation' ) );
      add_action( 'wp_ajax_deactivate_gs_integation', array( $this, 'deactivate_gs_integation' ) );
      add_action( 'wp_ajax_gs_clear_log', array( $this, 'gs_clear_logs' ) );
      add_action( 'wp_ajax_sync_google_account', array( $this, 'sync_google_account' ) );
      add_action( 'wp_ajax_get_tab_list', array( $this, 'get_tab_list_by_sheetname' ) );
      add_action( 'wp_ajax_get_sheet_id', array( $this, 'get_sheet_id_by_sheetname' ) );

      // Add new tab to contact form 7 editors panel
      add_filter( 'wpcf7_editor_panels', array( $this, 'cf7_gs_editor_panels' ) );

      add_action( 'wpcf7_after_save', array( $this, 'save_gs_settings' ) );
      add_action( 'wpcf7_after_create', array( $this, 'duplicate_forms_support') );
      
      add_action( 'wpcf7_before_send_mail', array( $this, 'save_uploaded_files_local') );

      add_action('wpcf7_mail_sent', array( $this, 'cf7_save_to_google_sheets' ) );
      
      add_action('wpcf7_admin_warnings', array( $this, 'cf7gs_admin_validation' ), 5 );
   }

   /**
    * AJAX function - verifies the token
    * @since 1.0
    */
   public function verify_gs_integation() {
      // nonce check
      check_ajax_referer('gs-ajax-nonce', 'security');

      /* sanitize incoming data */
      $Code = sanitize_text_field($_POST["code"]);

      update_option('gs_access_code', $Code);

      if (get_option('gs_access_code') != '') {
         include_once( GS_CONNECTOR_PRO_ROOT . '/lib/google-sheets.php');
         CF7GSC_googlesheet::preauth(get_option('gs_access_code'));
         update_option('gs_verify', 'valid');
         // After validation fetch sheetname and tabs from the user account
         //$this->sync_google_account();  
         wp_send_json_success();
      } else {
         update_option('gs_verify', 'invalid');
         wp_send_json_error();
      }
   }
   
   /**
    * AJAX function - deactivate activation
    * @since 1.4
    */
   public function deactivate_gs_integation() {
      // nonce check
      check_ajax_referer('gs-ajax-nonce', 'security');

      if ( get_option('gs_token') !== '' ) {
         delete_option('gs_feeds');
         delete_option('gs_sheetId');
         delete_option('gs_token');
         delete_option('gs_access_code');
         delete_option('gs_verify');
         wp_send_json_success();
      } else {
         wp_send_json_error();
      }
   }

   /**
    * AJAX function - clear log file
    * @since 1.0
    */
   public function gs_clear_logs() {
      // nonce check
      check_ajax_referer('gs-ajax-nonce', 'security');

      $handle = fopen(GS_CONNECTOR_PRO_PATH . 'logs/log.txt', 'w');
      fclose($handle);

      wp_send_json_success();
   }

   /**
    * Function - sync with google account to fetch sheet and tab name
    * @since 1.0
    */
   public function sync_google_account() {
      $return_ajax = false;

      if (isset($_POST['isajax']) && $_POST['isajax'] == 'yes') {
         // nonce check
         check_ajax_referer('gs-ajax-nonce', 'security');
         $init = sanitize_text_field($_POST['isinit']);
         $return_ajax = true;
      }

      include_once( GS_CONNECTOR_PRO_ROOT . '/lib/google-sheets.php');
      
      $worksheet_array = array();
      $sheetdata = array();
      $sheetId = array();
      
      $doc = new CF7GSC_googlesheet();
      $doc->auth();
      $spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
      
      // Get all spreadsheets
      $spreadsheetFeed = $spreadsheetService->getSpreadsheets();
      foreach ($spreadsheetFeed as $sheetfeeds) {
         // get sheet title
         $sheetname = $sheetfeeds->getTitle();
         $tablist = $spreadsheetFeed->getByTitle($sheetname);
         $worksheets = $tablist->getWorksheets();
         foreach ($worksheets as $worksheetfeeds) {
            $worksheetname = $worksheetfeeds->getTitle();
            // Get tab id
            $getGid = $worksheetfeeds->getGid();
            $gid[$worksheetname] = $getGid;
            $worksheet_array[] = $worksheetname;
         }
         // Get sheet id
         $link = $sheetfeeds->getId();
         $getid = substr($link, 64);
         $sheetId[$sheetname] = array("id" => $getid, "tabId" => $gid);
         unset($gid);
         $sheetdata[$sheetname] = $worksheet_array;
         unset($worksheet_array);
      }

      update_option('gs_sheetId', $sheetId);
      update_option('gs_feeds', $sheetdata);

      if ($return_ajax == true) {
         if ($init == 'yes') {
            wp_send_json_success(array("success" => 'yes'));
         } else {
            wp_send_json_success(array("success" => 'no'));
         }
      }
   }

   /**
    * AJAX function - Fetch tab list by sheet name
    * @since 1.0
    */
   public function get_tab_list_by_sheetname() {
      // nonce check
      check_ajax_referer('gs-ajax-nonce', 'security');

      $sheetname = sanitize_text_field($_POST['sheetname']);
      $sheet_data = get_option('gs_feeds');
      $html = "";
      $tablist = "";
      if (!empty($sheet_data) && array_key_exists($sheetname, $sheet_data)) {
         $tablist = $sheet_data[$sheetname];
      }

      if (!empty($tablist)) {
         $html = '<option value="">' . __("Select", "gsconnector") . '</option>';
         foreach ($tablist as $tab) {
            $html .= '<option value="' . $tab . '">' . $tab . '</option>';
         }
      }
      wp_send_json_success(htmlentities($html));
   }

   /**
    * AJAX function - Fetch sheet URL
    * @since 1.3
    */
   public function get_sheet_id_by_sheetname() {
      // nonce check
      check_ajax_referer('gs-ajax-nonce', 'security');
      
      $sheetname = sanitize_text_field($_POST['sheetname']);
      $tabname = sanitize_text_field($_POST['tabname']);
      
      $sheetId_data = get_option('gs_sheetId');
      $sheet_id = "";
      if ( ! empty( $sheetId_data ) && array_key_exists( $sheetname, $sheetId_data ) ) {
         $sheet_details = $sheetId_data[$sheetname];
         $sheet_id = $sheet_details['id'];
         $tab_id = 0;
         if ( ! empty( $sheet_details['tabId'] ) ) {
            $tab_id = $sheet_details['tabId'][$tabname];
         }
      }
      $html = "";

      if ( $sheet_id !== "") {
         $html .= '<label> Google Sheet URL </label> <a href="https://docs.google.com/spreadsheets/d/' . $sheet_id . '/edit#gid=' . $tab_id . '" target="_blank">Sheet URL</a>
			<input type="hidden" name="gsheet_id" value="' . $sheet_id . '">';
         wp_send_json_success(htmlentities($html));
      }
   }

   /**
    * Add new tab to contact form 7 editors panel
    * @since 1.0
    */
   public function cf7_gs_editor_panels($panels) {
      $current_role = Gs_Connector_Utility::instance()->get_current_user_role();
      $gs_roles = get_option('gs_tab_roles_setting');
      if ( ( is_array( $gs_roles ) && array_key_exists($current_role, $gs_roles ) ) || $current_role === "administrator") {
         $panels['google_sheets'] = array(
             'title' => __('Google Sheet Pro', 'contact-form-7'),
             'callback' => array($this, 'cf7_editor_panel_google_sheet')
         );
      }

      return $panels;
   }

   /**
    * Copy key values and assign it to duplicate form
    *
    * @param object $contact_form WPCF7_ContactForm Object - All data that is related to the form.
    */
   public function duplicate_forms_support($contact_form) {
      $contact_form_id = $contact_form->id();

      if (!empty($_REQUEST['post']) && !empty($_REQUEST['_wpnonce'])) {

         $post_id = intval($_REQUEST['post']);

         $get_settings = get_post_meta($post_id, 'gs_settings');
         foreach ($get_settings as $gskey => $gsval) {
            update_post_meta($contact_form_id, 'gs_settings', $gsval);
         }

         $get_special_tags = get_post_meta($post_id, 'gs_map_special_mail_tags');
         foreach ($get_special_tags as $gstkey => $gstval) {
            update_post_meta($contact_form_id, 'gs_map_special_mail_tags', $gstval);
         }

         $get_custom_tags = get_post_meta($post_id, 'gs_map_custom_mail_tags');
         foreach ($get_custom_tags as $gctkey => $gctval) {
            update_post_meta($contact_form_id, 'gs_map_custom_mail_tags', $gctval);
         }

         $get_mail_tags = get_post_meta($post_id, 'gs_map_mail_tags');
         foreach ($get_mail_tags as $gmtkey => $gmtval) {
            update_post_meta($contact_form_id, 'gs_map_mail_tags', $gmtval);
         }

         $get_custom_header_tags = get_post_meta($post_id, 'gs_custom_header_tags');
         foreach ($get_custom_header_tags as $gchkey => $gchval) {
            update_post_meta($contact_form_id, 'gs_custom_header_tags', $gchval);
         }
      }
   }

   /**
    * Set Google sheet settings with contact form
    * @since 1.0
    */
   public function save_gs_settings($post) {

      $form_id = $post->id();
      $get_existing_data = get_post_meta($form_id, 'gs_settings');
      
      // Fetch dropdown fields
      $sheet_name_default = isset($_POST['cf7-gs']['sheet-name']) ? $_POST['cf7-gs']['sheet-name'] : "";
      $tab_name_default = isset($_POST['cf7-gs']['sheet-tab-name']) ? $_POST['cf7-gs']['sheet-tab-name'] : "";

      // get custom sheet name and tab name as per manual checkbox selection
      $sheet_name_custom = isset($_POST['cf7-gs']['sheet-name-custom']) ? $_POST['cf7-gs']['sheet-name-custom'] : "";
      $tab_name_custom = isset($_POST['cf7-gs']['sheet-tab-name-custom']) ? $_POST['cf7-gs']['sheet-tab-name-custom'] : "";
      $manual_field = isset($_POST['cf7-gs']['manual-field']) ? $_POST['cf7-gs']['manual-field'] : "";

      // Condition 1 : manual checkbox checked and inputed fields is not empty
      if ( $sheet_name_custom !== "" && $tab_name_custom !== "" && $manual_field == "on" ) {
         $settingArray = array("sheet-name" => $sheet_name_custom, "sheet-tab-name" => $tab_name_custom, "manual-field" => 'on');
         update_post_meta( $form_id, 'gs_settings', $settingArray );
         $sheet_name = $sheet_name_custom;
         $tab_name = $tab_name_custom;
      }
      
      // Condition 2 : manual checkbox checked and inputed fields is empty
      if ( $sheet_name_custom == "" && $tab_name_custom == "" && $manual_field == "on" ) {
         $settingArray = array("sheet-name" => $sheet_name_custom, "sheet-tab-name" => $tab_name_custom, "manual-field" => 'on');
         update_post_meta( $form_id, 'gs_settings', $settingArray );
         $sheet_name = $sheet_name_custom;
         $tab_name = $tab_name_custom;
      }

      // Conditon 3 : Manual Checkox unchecked and dropdown fields are selected
      if ( $sheet_name_default !== "" && $tab_name_default !== "" && $manual_field === "" ) {
         $sheet_name = $sheet_name_default;
         $tab_name = $tab_name_default;
         $sheetArray = array( "sheet-name" => $sheet_name, "sheet-tab-name" => $tab_name );
         update_post_meta( $form_id, 'gs_settings', $sheetArray);
      }
      
      // Conditon 4 : Manual Checkox unchecked and dropdown fields are not selected
      if ( $sheet_name_default === "" && $tab_name_default === "" && $manual_field === "" ) {
         $sheet_name = $sheet_name_default;
         $tab_name = $tab_name_default;
         $sheetArray = array( "sheet-name" => $sheet_name, "sheet-tab-name" => $tab_name );
         update_post_meta( $form_id, 'gs_settings', $sheetArray);
      }

      $gs_map_tags = array();

      // Save special mail tags
      $special_mail_tag = isset($_POST['gs-st-ck']) ? $_POST['gs-st-ck'] : array();
      $special_mail_tag_key = $_POST['gs-st-key'];
      $special_mail_tag_placeholder = $_POST['gs-st-placeholder'];
      $special_mail_tag_column = $_POST['gs-st-custom-header'];
      $special_mail_tag_array = array();
      if (!empty($special_mail_tag)) {
         foreach ($special_mail_tag as $key => $value) {
            $smt_key = $special_mail_tag_key[$key];
            $smt_val = (!empty($special_mail_tag_column[$key]) ) ? $special_mail_tag_column[$key] : $special_mail_tag_placeholder[$key];
            if ($smt_val !== "") {
               $special_mail_tag_array[$smt_key] = $smt_val;
               $gs_map_tags[] = $smt_val;
            }
         }
      }
      update_post_meta($form_id, 'gs_map_special_mail_tags', $special_mail_tag_array);

      // Save custom mail tags
      $custom_mail_tag = isset($_POST['gs-ct-ck']) ? $_POST['gs-ct-ck'] : array();
      $custom_mail_tag_key = isset($_POST['gs-ct-key']) ? $_POST['gs-ct-key'] : array();
      $custom_mail_tag_placeholder = isset($_POST['gs-ct-placeholder']) ? $_POST['gs-ct-placeholder'] : array();
      $custom_mail_tag_column = isset($_POST['gs-ct-custom-header']) ? $_POST['gs-ct-custom-header'] : array();
      $custom_mail_tag_array = array();
      if (!empty($custom_mail_tag)) {
         foreach ($custom_mail_tag as $key => $value) {
            $cmt_key = ltrim($custom_mail_tag_key[$key], '_');
            $cmt_val = (!empty($custom_mail_tag_column[$key]) ) ? $custom_mail_tag_column[$key] : $custom_mail_tag_placeholder[$key];
            if ($cmt_val !== "") {
               $custom_mail_tag_array[$cmt_key] = $cmt_val;
               $gs_map_tags[] = $cmt_val;
            }
         }
      }
      update_post_meta($form_id, 'gs_map_custom_mail_tags', $custom_mail_tag_array);

      // Save mail tags
      $mail_tag_chk = isset($_POST['gs-custom-ck']) ? $_POST['gs-custom-ck'] : array();
      $mail_tag = $_POST['gs-custom-header-key'];
      $mail_tag_placeholder = $_POST['gs-custom-header-placeholder'];
      $mail_tag_column = $_POST['gs-custom-header'];
      $mail_tag_array = array();
      if (!empty($mail_tag_chk)) {
         foreach ($mail_tag_chk as $key => $value) {
            $mt_key = $mail_tag[$key];
            $mt_val = (!empty($mail_tag_column[$key]) ) ? $mail_tag_column[$key] : $mail_tag_placeholder[$key];
            if ($mt_val !== "") {
               $mail_tag_array[$mt_key] = $mt_val;
               $gs_map_tags[] = $mt_val;
            }
         }
      }
      update_post_meta($form_id, 'gs_map_mail_tags', $mail_tag_array);

      $drag_val = isset($_POST['gs-drag-index']) ? $_POST['gs-drag-index'] : array();
      
      // Fetch old final header
      $old_header = get_post_meta( $form_id, 'gs_custom_header_tags', true );
      
      $final_header_array = array();
      if ( ! empty( $drag_val ) ) {
         foreach ( $drag_val as $val ) {
            if ( in_array( $val, $gs_map_tags ) ) {
               $final_header_array[] = $val;
            }
         }
      }
      update_post_meta($form_id, 'gs_custom_header_tags', $final_header_array );

      // if not empty sheet and tab name than save and add header to sheet
      if ( ! empty( $sheet_name ) && ( ! empty( $tab_name ) ) ) {
         try {
            include_once( GS_CONNECTOR_PRO_ROOT . "/lib/google-sheets.php" );
            $doc = new CF7GSC_googlesheet();
            $doc->auth();
            $doc->add_header($sheet_name, $tab_name, $final_header_array, $old_header );
         } catch (Exception $e) {
            $data['ERROR_MSG'] = $e->getMessage();
            $data['TRACE_STK'] = $e->getTraceAsString();
            Gs_Connector_Utility::gs_debug_log($data);
         }
      }
   }

   /**
    * Function - To send contact form data to google spreadsheet
    * @param object $form
    * @since 1.0
    */
   public function cf7_save_to_google_sheets( $form ) {
      
      $expiration = $this->check_license_expiration();
      if ( $expiration ) {
         return;
      }
      
      $submission = WPCF7_Submission::get_instance();
      // get form data
      $form_id = $form->id();
      $form_data = get_post_meta($form_id, 'gs_settings');

      $mail_tags = get_post_meta($form_id, 'gs_map_mail_tags');
      $special_mail_tags = get_post_meta($form_id, 'gs_map_special_mail_tags');
      $custom_mail_tags = get_post_meta($form_id, 'gs_map_custom_mail_tags');
      $mereged_mail_tags = (!empty($special_mail_tags) ) && (!empty($custom_mail_tags) ) ? array_merge($special_mail_tags, $custom_mail_tags) : array();

      $data = array();
      $meta = array();

      // if contact form sheet name and tab name is not empty than send data to spreedsheet
      if ($submission && (!empty($form_data[0]['sheet-name']) ) && (!empty($form_data[0]['sheet-tab-name']) )) {
         $posted_data = $submission->get_posted_data();

         // Store upload files locally
         $uploads_stored = $this->gs_uploads;

         // make sure the form ID matches the setting otherwise don't do anything
         try {
            include_once( GS_CONNECTOR_PRO_ROOT . "/lib/google-sheets.php" );
            $doc = new CF7GSC_googlesheet();
            $doc->auth();
            $doc->settitleSpreadsheet($form_data[0]['sheet-name']);
            $doc->settitleWorksheet($form_data[0]['sheet-tab-name']);


            foreach ($mereged_mail_tags as $k => $v) {
               foreach ($v as $k1 => $v1) {
                  $meta[$v1] = apply_filters('wpcf7_special_mail_tags', '', sprintf('_%s', $k1), false);
               }
            }

            // Enter special mail tag values to sheet
            foreach ($meta as $k2 => $v2) {
               $data[$k2] = $v2;
            }

            foreach ($posted_data as $key => $value) {
               // exclude the default wpcf7 fields in object
               if (strpos($key, '_wpcf7') !== false || strpos($key, '_wpnonce') !== false) {
                  // do nothing
               } else {
                  // Get custom column name by key
                  if ( ! empty( $mail_tags ) && array_key_exists($key, $mail_tags[0])) {
                     $key = $mail_tags[0][$key];
                  }

                  // Get file uploaded URL
                  if (array_key_exists($key, $uploads_stored)) {
                     $value = $uploads_stored[$key];
                  }

                  // handle strings and array elements
                  if (is_array($value)) {
                     $data[$key] = implode(', ', $value);
                  } else {
                     $data[$key] = $value;
                  }
               }
            }
            $doc->add_row($data);
         } catch (Exception $e) {
            $data['ERROR_MSG'] = $e->getMessage();
            $data['TRACE_STK'] = $e->getTraceAsString();
            Gs_Connector_Utility::gs_debug_log($data);
         }
      }
   }

   /*
    * Google sheet settings page  
    * @since 1.0
    */

   public function cf7_editor_panel_google_sheet($post) {

      $form_id = sanitize_text_field($_GET['post']);
      $form_data = get_post_meta($form_id, 'gs_settings');

      $saved_sheet_name = isset($form_data[0]['sheet-name']) ? $form_data[0]['sheet-name'] : "";
      $saved_tab_name = isset($form_data[0]['sheet-tab-name']) ? $form_data[0]['sheet-tab-name'] : "";

      $val = get_post_meta($form_id, 'gs_settings');
      $checked = isset($val[0]['manual-field']) ? $val[0]['manual-field'] : "";

      $sheet_data = get_option('gs_feeds');
      ?>
      <form method="post">
         <div class="gs-fields">
            <h2><span><?php echo esc_html(__('Google Sheet Settings', 'gsconnector')); ?></span></h2>
      <?php
      if ($checked == 'on') {
         $class = 'hide';
      } else {
         $class = 'display_div';
      }
      ?>
            <div class="sheet-details <?php echo $class; ?>">
               <p>
                  <label><?php echo esc_html(__('Google Sheet Name', 'gsconnector')); ?></label>
                  <select name="cf7-gs[sheet-name]" id="gs-sheet-name" >
                     <option value=""><?php echo __('Select', 'gsconnector'); ?></option>
                     <?php
                     if (!empty($sheet_data)) {
                        foreach ($sheet_data as $key => $value) {
                           $selected = "";
                           if ($saved_sheet_name !== "" && $key == $saved_sheet_name) {
                              $selected = "selected";
                           }
                           ?>
                           <option value="<?php echo $key; ?>" <?php echo $selected; ?> ><?php echo $key; ?></option>
                           <?php
                        }
                     }
                     ?>
                  </select>
                  <span class="loading-sign">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                  <input type="hidden" name="gs-ajax-nonce" id="gs-ajax-nonce" value="<?php echo wp_create_nonce('gs-ajax-nonce'); ?>" />
               </p>
               <p>
                  <label><?php echo esc_html(__('Google Sheet Tab Name', 'gsconnector')); ?></label>

                  <select name="cf7-gs[sheet-tab-name]" id="gs-sheet-tab-name" >
                     <?php
                     if ($saved_sheet_name !== "") {
                        $selected_tabs = isset( $sheet_data[$saved_sheet_name] ) ? $sheet_data[$saved_sheet_name] : null;
                        if ( ! empty( $selected_tabs ) ) {
                           foreach ($selected_tabs as $tab) {
                              $selected = "";
                              if ($saved_tab_name !== "" && $tab == $saved_tab_name) {
                                 $selected = "selected";
                              }
                              ?>
                              <option value="<?php echo $tab; ?>" <?php echo $selected; ?> ><?php echo $tab; ?></option>
                              <?php
                           }
                        }
                     }
                     ?>
                  </select>
               </p> 
               <p class="sheet-url" id="sheet-url">
                  <?php
                  $sheet_id = "";
                  $getsheets_id = get_option('gs_sheetId');
                  if ( ! empty( $getsheets_id ) && array_key_exists( $saved_sheet_name, $getsheets_id ) ) {
                     $sheet_details = $getsheets_id[ $saved_sheet_name ];
                     $sheet_id = isset( $sheet_details['id'] ) ? $sheet_details['id'] : "" ;
                     $tab_id = 0;
                     if ( ! empty( $sheet_details['tabId'] ) ) {
                        $tab_id = $sheet_details['tabId'][$saved_tab_name];
                     }
                  }
                  if( $sheet_id !== "" ) {
                  ?>
                     <label><?php echo __('Google Sheet URL', 'gsconnector'); ?> </label> <a href="https://docs.google.com/spreadsheets/d/<?php echo $sheet_id; ?>/edit#gid=<?php echo $tab_id; ?>" target="_blank"><?php echo __('Sheet URL', 'gsconnector'); ?></a>
                  <?php
                  }
                  ?>
               </p>
               
               <p class="gs-sync-row"><?php echo __('Not showing Sheet Name, Tab Name and Sheet URL Link ? <a id="gs-sync" data-init="no">Click here </a> to fetch it.', 'gsconnector'); ?><span class="loading-sign">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></p>
            </div>
            <?php
            if ($checked == 'on') {
               $class = 'display_div';
            } else {
               $class = 'hide';
            }
            ?>
            <div class="manual-fields <?php echo $class; ?>">
               <p>
                  <label><?php echo esc_html(__('Google Sheet Name', 'gsconnector')); ?></label>
                  <input type="text" name="cf7-gs[sheet-name-custom]" id="gs-sheet-name" 
                         value="<?php echo ( isset($form_data[0]['sheet-name']) ) ? esc_attr($form_data[0]['sheet-name']) : ''; ?>"/>
                  <a href="" class="gs-name help-link"><?php echo esc_html(__('Google Sheet Name ?', 'gsconnector')); ?><span class='hover-data'><?php echo esc_html(__('Go to your google account and click on"Google apps" icon and than click "Sheets". Select the name of the appropriate sheet you want to link your contact form or create new sheet.', 'gsconnector')); ?> </span></a>
               </p>
               <p>
                  <label><?php echo esc_html(__('Google Sheet Tab Name', 'gsconnector')); ?></label>
                  <input type="text" name="cf7-gs[sheet-tab-name-custom]" id="gs-sheet-tab-name"
                         value="<?php echo ( isset($form_data[0]['sheet-tab-name']) ) ? esc_attr($form_data[0]['sheet-tab-name']) : ''; ?>"/>
                  <a href="" class=" gs-name help-link"><?php echo esc_html(__('Google Sheet Tab Name ?', 'gsconnector')); ?><span class='hover-data'><?php echo esc_html(__('Open your Google Sheet with which you want to link your contact form . You will notice a tab names at bottom of the screen. Copy the tab name where you want to have an entry of contact form.', 'gsconnector')); ?></span></a>
               </p>
            </div>
      <?php      
      if ($checked == 'on') {
         $checked = 'checked';
      } else {
         $checked = "";
      }
      ?>
            <div class="custom-chk">
               <input type="checkbox" name="cf7-gs[manual-field]" id="manual-name" <?php echo $checked ?>>
               <span class="enable-manual"><?php echo __('Enable Manually adding of sheet name and tab name.', 'gsconnector'); ?></span>
               <span class="tooltip"> 
                  <img src="<?php echo GS_CONNECTOR_PRO_URL; ?>assets/img/help.png" class="help-icon"> 
                  <span class="tooltiptext tooltip-right-msg"><?php echo __("Not fetching sheet details to the dropdown than add sheet and tab name manually.", "gsconnector"); ?></span>
               </span>
            </div>
         </div>
      <?php
      include( GS_CONNECTOR_PRO_PATH . "includes/pages/gs-field-list.php" );

      include( GS_CONNECTOR_PRO_PATH . "includes/pages/gs-special-mail-tags.php" );

      include( GS_CONNECTOR_PRO_PATH . "includes/pages/gs-custom-mail-tags.php" );
      ?>
         <div class="card-gs">
            <div class="column">
               <div class="title-custom">
                  <a class="order-val"><?php echo __('Custom Ordering', 'gsconnector'); ?></a>
               </div>
               <p class="gs-sync-row"><?php echo __('Not showing correct header name ? Un-select and select the fields checkbox again. It happens due to various reasons like change in field/mail tag name.', 'gsconnector'); ?></p>
               <ul class="connected-sortable droppable-area1" id="drag">
                  <?php
                  $get_ordered_list = get_post_meta($form_id, 'gs_custom_header_tags');
                  if (isset($get_ordered_list) && $get_ordered_list != "") {
                     $count = 0;
                     foreach ($get_ordered_list as $val) {
                        foreach ($val as $k => $v) {
                           ?>
                           <div class="drag-item"><li class="draggable-item"><?php echo $v; ?><input type="hidden" data-count="<?php echo $count; ?>" name="gs-drag-index[<?php echo $count; ?>]" id="gs-drag-drop" value="<?php echo $v; ?>"></li></div>
               <?php
               $count++;
            }
         }
      }
      ?>
                  <input type="hidden" id="total-count" value="<?php echo $count; ?>">
               </ul>
            </div>
         </div>

      </form>
      <?php
   }

   /**
    * Function - display contact form fields to be mapped to google sheet
    * @param int $form_id
    * @since 1.0
    */
   public function display_form_fields($form_id, $post) {
      ?>
      <?php
      // fetch saved fields
      $saved_mail_tags = get_post_meta($form_id, 'gs_map_mail_tags');

      // fetch mail tags
      $mail_tags = array();
      $manager = WPCF7_FormTagsManager::get_instance();
      $scanned = $manager->scan($post->prop('form'));
      foreach ($scanned as $tags) {
         if (in_array($tags['basetype'], $this->allowed_tags)) {
            $mail_tags[] = $tags['name'];
         }
      }

      if (!empty($mail_tags)) {
         ?>

         <table class="gs-field-list">
         <?php
         $count = 0;
         foreach ($mail_tags as $key => $value) {
            $saved_val = "";
            $checked = "";
            if (!empty($saved_mail_tags) && array_key_exists($value, $saved_mail_tags[0])) :
               $saved_val = $saved_mail_tags[0][$value];
               $checked = "checked";
            endif;

            $placeholder = preg_replace('/[\\_]|\\s+/', '-', $value);
            ?>
               <tr>
                  <td><input type="checkbox" class="fcheckBoxClass" id="gs-cstm-chk" name="gs-custom-ck[<?php echo $count; ?>]" value="1" <?php echo $checked; ?> ></td>
                  <td><?php echo $value; ?></td>
                  <td>
                     <input type="hidden" value="<?php echo $value; ?>" name="gs-custom-header-key[<?php echo $count; ?>]">
                     <input type="hidden" value="<?php echo $placeholder; ?>" name="gs-custom-header-placeholder[<?php echo $count; ?>]">
                     <input type="text" id="gs-custom-header" name="gs-custom-header[<?php echo $count; ?>]" value="<?php echo $saved_val; ?>" placeholder="<?php echo $placeholder; ?>">
                     <input type="hidden" name="custom-value" value="<?php echo $placeholder; ?>" id="custom-value">
                  </td>
               </tr>
            <?php
            $count++;
         }
         ?>
         </table>
         <?php
      } else {
         echo '<p><span class="gs-info">' . __('No mail tags available.', 'gsconnector') . '</span></p>';
      }
   }

   /**
    * Function - display contact form Special mail tags to be mapped to google sheet
    * @since 1.0
    */
   public function display_form_special_tags($form_id) {

      $custom_mail_tags = array();

      // fetch saved fields
      $saved_smail_tags = get_post_meta($form_id, 'gs_map_special_mail_tags');

      $tags_count = count($this->special_mail_tags);
      ?>

      <table class="gs-field-list custom">
         <?php
         echo '<tr>';
         for ($i = 0; $i <= $tags_count; $i++) {
            if ($i == $tags_count) {
               break;
            }
            $tag_name = $this->special_mail_tags[$i];
            $saved_val = "";
            $checked = "";
            if (!empty($saved_smail_tags) && array_key_exists($tag_name, $saved_smail_tags[0])) :
               $saved_val = $saved_smail_tags[0][$tag_name];
               $checked = "checked";
            endif;

            $placeholder = str_replace('_', '-', $tag_name);

            echo '<td><input type="checkbox" class="checkBoxClass" id="gs-cstm-chk" name="gs-st-ck[' . $i . ']" value="1" ' . $checked . '></td>';
            echo '<td class="second-val">[_' . $tag_name . '] </td>';
            echo '<td class="gs-r-pad"><input type="hidden" name="gs-st-key[' . $i . ']" value="' . $tag_name . '" ><input type="hidden" name="gs-st-placeholder[' . $i . ']" value="' . $placeholder . '" ><input type="text" id="gs-custom-header" name="gs-st-custom-header[' . $i . ']" value="' . $saved_val . '" placeholder="' . $placeholder . '"><input type="hidden" name="custom-value" value="'.$placeholder.'" id="custom-value"></td>';
            echo '</tr>';
         }
         ?>
      </table>
      <?php
   }

   /**
    * Function - display contact form Custom mail tags to be mapped to google sheet
    * @since 1.0
    */
   function display_form_custom_tag($form_id) {
      $custom_mail_tags = array();
      $num_of_cols = 2;

      if (has_filter("gscf7_special_mail_tags")) {
         // Filter hook for custom mail tags
         $custom_tags = apply_filters("gscf7_special_mail_tags", $custom_mail_tags, $form_id);
         $custom_tags_count = count($custom_tags);
         $num_of_cols = 2;
         // fetch saved fields
         $saved_cmail_tags = get_post_meta($form_id, 'gs_map_custom_mail_tags');
         ?>
         <table class="gs-field-list">
            <?php
            echo '<tr>';
            for ($i = 0; $i <= $custom_tags_count; $i++) {
               if ($i == $custom_tags_count) {
                  break;
               }
               $tag_name = $custom_tags[$i];
               $modify_tag = ltrim($tag_name, '_');
               $saved_val = "";
               $checked = "";
               if (!empty($saved_cmail_tags) && array_key_exists($modify_tag, $saved_cmail_tags[0])) :
                  $saved_val = $saved_cmail_tags[0][$modify_tag];
                  $checked = "checked";
               endif;

               //hack - todo
               $placeholder_explode = explode('_', $tag_name, 2);
               $placeholder = str_replace('_', '-', $placeholder_explode[1]);

               echo '<td><input type="checkbox" id="gs-cstm-chk" name="gs-ct-ck[' . $i . ']" value="1" ' . $checked . '></td>';
               echo '<td>[' . $tag_name . '] <input type="hidden" name="custom-nm" id="cstm-nm" value="' . $placeholder . '"></td>';
               echo '<td class="gs-r-pad"><input type="hidden" name="gs-ct-key[' . $i . ']" value="' . $tag_name . '" ><input type="hidden" name="gs-ct-placeholder[' . $i . ']" value="' . $placeholder . '" ><input type="text" id="gs-custom-header" name="gs-ct-custom-header[' . $i . ']" value="' . $saved_val . '" placeholder="' . $placeholder . '"><input type="hidden" name="custom-value" value="'.$placeholder.'" id="custom-value"></td>';
               if ($i % $num_of_cols == 1) {
                  echo '</tr><tr>';
               }
            }
            ?>
         </table>

         <?php
      } else {
         echo '<p><span class="gs-info">' . __('No custom mail tags available.', 'gsconnector') . '</span></p>';
      }
   }

   /**
    * Function - fetch contant form list that is connected with google sheet
    * @since 1.0
    */
   public function get_forms_connected_to_sheet() {
      global $wpdb;

      $query = $wpdb->get_results("SELECT ID,post_title,meta_value,meta_key from " . $wpdb->prefix . "posts as p JOIN " . $wpdb->prefix . "postmeta as pm on p.ID = pm.post_id where pm.meta_key='gs_settings' AND p.post_type='wpcf7_contact_form'");
      return $query;
   }

   /**
    * Returns Upload document URL
    * @param array $files
    * @return URL
    * @since 1.3.2
    */
   public function save_uploaded_files_local() {
      $upload = wp_upload_dir();
      if (get_option('uploads_use_yearmonth_folders')) {
         // Generate the yearly and monthly dirs
         $time = current_time('mysql');
         $y = substr($time, 0, 4);
         $m = substr($time, 5, 2);
         $upload['subdir'] = "/$y/$m";
      }

      $upload['subdir'] = '/cf7gs' . $upload['subdir'];
      $upload['path'] = $upload['basedir'] . $upload['subdir'];
      $upload['url'] = $upload['baseurl'] . $upload['subdir'];

      if (!is_dir($upload['path'])) {
         wp_mkdir_p($upload['path']);
      }

      $htaccess_file = sprintf('%s/.htaccess', $upload['path']);

      // Make sure that uploads directory is protected from listing
      if ( ! file_exists( $htaccess_file ) ) :
         file_put_contents( $htaccess_file, 'Options -Indexes' );
      endif;
      
      $time_now       = time();
      
      $form = WPCF7_Submission::get_instance();
      if ( $form ) {
         $files          = $form->uploaded_files();
         $uploads_stored = array();
         
         foreach ( $files as $name=>$path ) {
            if ( ! isset ( $_FILES[ $name ] ) ) {
               continue;
            }
            $file_name = basename($path);
            $destination = $upload['path'].'/'.$time_now.'-'.$file_name;
            $destination_url = sprintf('%s/%s', $upload['url'], $time_now.'-'.$file_name );
            $uploads_stored[$name] = $destination_url;
            copy( $path, $destination );
         }
         $this->gs_uploads = $uploads_stored;
      }
   }
   
   public function cf7gs_admin_validation() {
      if ( get_option( 'gs-admin-notice') ) {
          $error_message = get_option( 'gs-admin-notice' );
          $plugin_error = Gs_Connector_Utility::instance()->admin_notice(array(
          'type' => 'error',
          'message' => $error_message
         ) );
         echo $plugin_error;
         delete_option( "gs-admin-notice" );
      }
      
      $expiration = $this->check_license_expiration();
      if ( $expiration ) {
         $plugin_error = Gs_Connector_Utility::instance()->admin_notice(array(
          'type' => 'error',
          'message' => 'Hey, your CF7 Google Sheet Connector PRO license has Expired. <a href="https://www.gsheetconnector.com/your-account" target="__blank" >Renew Now</a>'
         ) );
         echo $plugin_error;
      }
      
   }
   
   /**
    * Check expiration of the license
    * @return boolean
    * @since 1.4
    */
   public function check_license_expiration() {
      $expiration = get_option( 'gs-lexpiration');
      if ( $expiration !== "lifetime" ) {
        $current_date = date("Y-m-d H:i:s");
        $current_timestamp = strtotime( $current_date );
        $expiration_timestamp = strtotime( $expiration );
        if( $current_timestamp > $expiration_timestamp ) {
           return true;
        }
      } 
      return false;
   }

}

$gs_connector_service = new Gs_Connector_Service();


