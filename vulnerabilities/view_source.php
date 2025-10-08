<?php
define( 'DVWA_WEB_PAGE_TO_ROOT', '../' );
require_once DVWA_WEB_PAGE_TO_ROOT . 'dvwa/includes/dvwaPage.inc.php';

dvwaPageStartup( array( 'authenticated' ) );

$page = dvwaPageNewGrab();
$page[ 'title' ] = 'Help' . $page[ 'title_separator' ].$page[ 'title' ];

if (array_key_exists ("id", $_GET) &&
	array_key_exists ("security", $_GET) &&
	array_key_exists ("locale", $_GET)) {
	
	$id       = $_GET[ 'id' ];
	$security = $_GET[ 'security' ];
	$locale   = $_GET[ 'locale' ];
	
	// Sanitizar entradas para prevenir Path Traversal
	$id = basename($id);
	$id = preg_replace('/[^a-zA-Z0-9_-]/', '', $id);
	
	$locale = basename($locale);
	$locale = preg_replace('/[^a-zA-Z_]/', '', $locale);
	
	// Lista blanca de IDs permitidos
	$allowed_ids = array('brute', 'exec', 'csrf', 'fi', 'sqli', 'sqli_blind', 'upload', 'xss_d', 'xss_r', 'xss_s');
	$allowed_locales = array('en', 'es', 'fr', 'de', 'zh');
	
	if (!in_array($id, $allowed_ids)) {
		$help = "<p>Invalid help page requested</p>";
	} elseif (!in_array($locale, $allowed_locales)) {
		$locale = 'en'; // Default a inglés
	}
	
	if (in_array($id, $allowed_ids)) {
		ob_start();
		
		if ($locale == 'en') {
			$help_file = DVWA_WEB_PAGE_TO_ROOT . "vulnerabilities/{$id}/help/help.php";
		} else {
			$help_file = DVWA_WEB_PAGE_TO_ROOT . "vulnerabilities/{$id}/help/help.{$locale}.php";
		}
		
		// Verificar que el archivo existe antes de incluirlo
		if (file_exists($help_file) && is_file($help_file)) {
			include($help_file);
		} else {
			echo "<p>Help file not found</p>";
		}
		
		$help = ob_get_contents();
		ob_end_clean();
	}
} else {
	$help = "<p>Not Found</p>";
}

$page[ 'body' ] .= "
<script src='/vulnerabilities/help.js'></script>
<link rel='stylesheet' type='text/css' href='/vulnerabilities/help.css' />
<div class=\"body_padded\">
	{$help}
</div>\n";

dvwaHelpHtmlEcho( $page );
?>
