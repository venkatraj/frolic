<?php
require_once get_template_directory() . '/includes/options-config.php';
require_once get_template_directory() . '/admin/control-icon-picker.php';

	if( ! class_exists('Frolic_Customizer_API_Wrapper') ) {
			require_once get_template_directory() . '/admin/class.frolic-customizer-api-wrapper.php';
	}


Frolic_Customizer_API_Wrapper::getInstance($options);
