<div class="cd-faq-items">
	<ul id="basics" class="cd-faq-group">
		<li class="content-visible">
			<a class="cd-faq-trigger" href="#0"><?php echo esc_html( __( 'Special Mail Tags ', 'gsconnector' ) ); ?><span class="gs-info"><?php echo esc_html( __( '( Map special mail tags to google sheet with custom header name. ) ', 'gsconnector' ) ); ?></span></a>
			<div class="cd-faq-content" style="display: block;">
				<div class="gs-demo-fields gs-third-block">
					<?php $this->display_form_special_tags( $form_id ); ?>
				 </div>
			</div>
		</li>
	</ul>
</div>
