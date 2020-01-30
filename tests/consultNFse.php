<?php include 'systemSettings.php';

use NFse\Models\ConsultNFse as MdlConsultNFse;
use NFse\Service\ConsultNFSe;

try {

    $parameters = new MdlConsultNFse();
    $parameters->startDate = '2017-01-01';
    $parameters->endDate = '2017-05-29';
    $parameters->takerType = 1;
    $parameters->document = '00584004000164';

    $find = new ConsultNFSe($settings);
    $result = $find->sendConsultation($parameters);

    dd($result);

} catch (Exception $e) {
    throw $e;
}
