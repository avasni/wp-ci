<?php
/*
 * GS Dashboard Widget
 * @since 1.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
   exit();
}
?>
<div class="dashboard-content">
   <?php
   $gs_connector_service = new Gs_Connector_Service();

   $forms_list = $gs_connector_service->get_forms_connected_to_sheet();
   // Fetch sheet and tab name ids
   $sheet_ids = get_option( 'gs_sheetId' );
   ?>
   <div class="main-content">
      <div>
         <h3><?php echo __( "Contact forms connected with Google Sheet", "gsconnector" ); ?></h3>
         <ul class="contact-form-list">
            <?php
            if ( ! empty( $forms_list ) ) {
               foreach ( $forms_list as $key => $value ) {
                  $meta_value = unserialize( $value->meta_value );
                  $sheet_name = $meta_value['sheet-name'];
                  $tab_name = $meta_value['sheet-tab-name'];
                  if ( $sheet_name !== "" && ( ! empty( $sheet_ids ) ) && array_key_exists( $sheet_name, $sheet_ids ) ) {
                     $sheet_details = $sheet_ids[$sheet_name];
                     $sheet_id = $sheet_details['id'];
                     $tab_id = 0;
                     if ( ! empty( $sheet_details['tabId'] ) ) {
                        $tab_id = $sheet_details['tabId'][$tab_name];
                     }
                     ?>
                     <li style= "list-style:none;">
                        <a class="form-titl" href="<?php echo admin_url( 'admin.php?page=wpcf7&post=' . $value->ID . '&action=edit' ); ?>">
                           <span class="title"><?php echo $value->post_title; ?></span>
                        </a>
                        <p class="sheet-url"> 
                           <span class="sheets">Sheet URL -</span> <a href="https://docs.google.com/spreadsheets/d/<?php echo $sheet_id; ?>/edit#gid=<?php echo $tab_id; ?>" target="_blank"><?php echo $sheet_name; ?></a>
                        </p>
                     </li>
                  <?php
                  }
               }
            } else { ?>
                     <p>
                        <?php echo __( "No contact form is connected with Google Sheet", "gsconnector"); ?>
                     </p>
            <?php }
            ?>


         </ul>
      </div>
   </div> <!-- main-content end -->
</div> <!-- dashboard-content end -->