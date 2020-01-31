<?php include 'systemSettings.php';

use NFse\Models\lot;
use NFse\Service\ConsultBatch;

try {
    $protocol = 'AF0775697M20e0304bR5pJYe';

    $sync = new ConsultBatch($settings, $protocol);
    $result = $sync->sendConsultation();

    dd($result);

} catch (Exception $e) {
    throw $e;
}
