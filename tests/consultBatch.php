<?php include 'systemSettings.php';

use NFse\Models\lot;
use NFse\Service\ConsultBatch;

try {
    $lot = new lot();
    $lot->rpsLot = 'AF0775697M20e0304bR5pJYe';

    $sync = new ConsultBatch($settings, $lot->rpsLot);
    $result = $sync->sendConsultation();

    dd($result);

} catch (Exception $e) {
    throw $e;
}
