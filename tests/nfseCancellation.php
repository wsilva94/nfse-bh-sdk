<?php include 'systemSettings.php';

use NFse\Service\NFseCancellation;

try {
    $parameters  = (object)[
        'id' => 122, //idPedido
        'numerNFse' => '201700000000001',
        'cancellationCode' => 2,
    ];

    $result = new NFseCancellation($settings, $parameters);
    $result = $result->sendConsultation();

    dd($result);
} catch (Exception $e) {
    throw $e;
}
