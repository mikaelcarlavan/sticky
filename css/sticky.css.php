
<?php

$res = @include("../../main.inc.php");                   // For root directory
if (!$res) $res = @include("../../../main.inc.php");    // For "custom" directory
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions.lib.php';

header("Content-type: text/css");
header('X-Content-Type-Options: nosniff');

 ?>
.div-table-responsive-no-min:has(#tablelines) {
    overflow-y: scroll;
    height: 100vh;
}

#tablelines th {
    position: sticky;
    top: 0;
    background: rgb(220,220,223);
}

#tablelines thead {
    position: sticky;
}

