<?php

require_once plugin_dir_path(__FILE__) . 'CF7GSC/Client.php';
include_once ( plugin_dir_path(__FILE__) . 'CF7GSC/autoload.php' );
include_once ( plugin_dir_path(__FILE__) . 'spreadsheet_autoload.php' );

use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;

class CF7GSC_googlesheet {

   private $token;
   private $spreadsheet;
   private $worksheet;

   const clientId = '1021473022177-agam4fkd36jkefe9ru8bvrsrara7b7s3.apps.googleusercontent.com';
   const clientSecret = 'TdJm0Dg8xe5VleeqqZOdH_Yo';
   const redirect = 'urn:ietf:wg:oauth:2.0:oob';

   public function __construct() {
      
   }

   //constructed on call
   public static function preauth($access_code) {
      $client = new CF7GSC_Client();
      $client->setClientId(CF7GSC_googlesheet::clientId);
      $client->setClientSecret(CF7GSC_googlesheet::clientSecret);
      $client->setRedirectUri(CF7GSC_googlesheet::redirect);
      $client->setScopes(array('https://spreadsheets.google.com/feeds'));
      $results = $client->authenticate($access_code);
      $tokenData = json_decode($client->getAccessToken(), true);
      CF7GSC_googlesheet::updateToken($tokenData);
   }

   public static function updateToken($tokenData) {
      $tokenData['expire'] = time() + intval($tokenData['expires_in']);
      try {
         $tokenJson = json_encode($tokenData);
         update_option('gs_token', $tokenJson);
      } catch (Exception $e) {
         Gs_Connector_Utility::gs_debug_log("Token write fail! - " . $e->getMessage());
      }
   }

   public function auth() {
      $tokenData = json_decode(get_option('gs_token'), true);

      if (time() > $tokenData['expire']) {
         $client = new CF7GSC_Client();
         $client->setClientId(CF7GSC_googlesheet::clientId);
         $client->setClientSecret(CF7GSC_googlesheet::clientSecret);
         $client->refreshToken($tokenData['refresh_token']);
         $tokenData = array_merge($tokenData, json_decode($client->getAccessToken(), true));
         CF7GSC_googlesheet::updateToken($tokenData);
      }

      /* this is needed */
      $serviceRequest = new DefaultServiceRequest($tokenData['access_token']);
      ServiceRequestFactory::setInstance($serviceRequest);
   }

   //preg_match is a key of error handle in this case
   public function settitleSpreadsheet($title) {
      $this->spreadsheet = $title;
   }

   //finished setting the title
   public function settitleWorksheet($title) {
      $this->worksheet = $title;
   }

   //choosing the worksheet
   public function add_row($data) {
      $spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
      $spreadsheetFeed = $spreadsheetService->getSpreadsheets();
      $spreadsheet = $spreadsheetFeed->getByTitle($this->spreadsheet);
      $worksheetFeed = $spreadsheet->getWorksheets();
      $worksheet = $worksheetFeed->getByTitle($this->worksheet);
      // if worksheet feeds data is not empty
      if (!empty($worksheet)) {
         $listFeed = $worksheet->getListFeed();
         $listFeed->insert($data);
      }
   }

   /**
    * Function - Adding custom column header to the sheet
    * @param string $sheet_name
    * @param string $tab_name
    * @param array $gs_map_tags 
    * @since 1.0
    */
   public function add_header($sheet_name, $tab_name, $final_header_array, $old_header ) {
      $spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
      $spreadsheetFeed = $spreadsheetService->getSpreadsheets();
      $spreadsheet = $spreadsheetFeed->getByTitle($sheet_name);
      // If sheet is found with the integrated account
      if ( ! empty( $spreadsheet ) ) {
         $worksheetFeed = $spreadsheet->getWorksheets();
         $worksheet = $worksheetFeed->getByTitle($tab_name);
         if ( ! empty( $worksheet ) ) {
            $cellFeed = $worksheet->getCellFeed();
         
            $count_old_header = count( $old_header );
            $count_new_header = count( $final_header_array );

            // If old header count is greater than new header count than empty the header
            if( $count_old_header !== 0 && $count_old_header > $count_new_header ) {
               for( $i = 0; $i <= $count_old_header; $i++ ) {
                  $column_name = isset( $final_header_array[ $i ] ) ? $final_header_array[ $i ] : "" ;
                  if( $column_name !== "" ) {
                     $cellFeed->editCell(1, $i+1, $column_name );
                  } else {
                     $cellFeed->editCell(1, $i+1, "");
                  }
               }
            } else {
               $count = 1;
               foreach ($final_header_array as $column_name) {
                  $cellFeed->editCell(1, $count, $column_name);
                  $count++;
               }
            }
         }  else {
            update_option('gs-admin-notice', "No sheet or tab name found with your Google Sheet authenticated account. If exist than re-authenticate and fetch sheet details again.");
         }        
      } else {
         update_option('gs-admin-notice', "No sheet or tab name found with your Google Sheet authenticated account. If exist than re-authenticate and fetch sheet details again.");
      }
   }
}

?>