<?php

define( 'DVWA_WEB_PAGE_TO_ROOT', '../../' );
require_once DVWA_WEB_PAGE_TO_ROOT . 'dvwa/includes/dvwaPage.inc.php';

// helper de escape (si no existe)
if (!function_exists('h')) {
    function h($v) {
        return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

dvwaPageStartup( array( 'authenticated' ) );

$page = dvwaPageNewGrab();
$page[ 'title' ] = 'SQL Injection Session Input' . $page[ 'title_separator' ].$page[ 'title' ];

if( isset( $_POST[ 'id' ] ) ) {
    // Obtener raw
    $raw_id = $_POST['id'];

    // Sanitizar: si es entero lo guardamos como entero; si no, limpiamos caracteres peligrosos
    if (is_numeric($raw_id)) {
        $safe_id = (int) $raw_id;
    } else {
        // permitimos letras, números, espacios, guiones, guion bajo y punto
        $safe_id = preg_replace('/[^\p{L}\p{N}\s\-\_\.]/u', '', $raw_id);
        // Si queda vacío, marcar como empty string
        if ($safe_id === null) $safe_id = '';
    }

    // Guardar la versión sanitizada en sesión
    $_SESSION[ 'id' ] =  $safe_id;

    // Mostrar la sesión escapada (evitar XSS en la salida)
    $page[ 'body' ] .= "Session ID: " . h($_SESSION[ 'id' ]) . "<br /><br /><br />";

    // Script estático (no inyectable) para recargar el opener. Dejar si necesitas esa funcionalidad.
    $page[ 'body' ] .= "<script>window.opener.location.reload(true);</script>";
}

$page[ 'body' ] .= "
<form action=\"#\" method=\"POST\">
    <input type=\"text\" size=\"15\" name=\"id\">
    <input type=\"submit\" name=\"Submit\" value=\"Submit\">
</form>
<hr />
<br />

<button onclick=\"self.close();\">Close</button>";

dvwaSourceHtmlEcho( $page );

?>
