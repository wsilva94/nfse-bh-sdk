<?php include 'systemSettings.php';

use NFse\Service\LotStatusConsultation;
use NFse\Models\Rps;

try {
    //consulta situação lote
    $rps = new Rps();
    $rps->number = 'AS0011742X17d149XKBtf2Yl';

    $sync   = new LotStatusConsultation($settings, $rps->number);
    $result = $sync->sendConsultation();

    dd($result);
} catch (Exception $e) {
    throw $e;
}
