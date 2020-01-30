<?php include 'systemSettings.php';

use NFse\Models\Lot;
use NFse\Service\LoteRps;
use NFse\Service\Rps;

try {
    //adiciona as rps

    //set Identificacao Rps
    $parameter = new Lot();
    $parameter->rpsLot = '201700000000006';
    $parameter->rps->number = '201700000000002';
    $parameter->rps->serie = 'AAAAA';
    $parameter->rps->type = 1;
    $parameter->rps->date = date('Y-m-d H:i:s');
    $parameter->rps->nature = 1;
    $parameter->rps->regime = 6;
    $parameter->rps->simple = 1;
    $parameter->rps->culturalPromoter = 2;
    $parameter->rps->status = 1;

    //set serviço
    $parameter->rps->service->itemList = 17.19;
    $parameter->rps->service->municipalityTaxationCode = 171900188;
    $parameter->rps->service->municipalCode = 3106200;
    $parameter->rps->service->description = 'PRESTACAO DE SERVICOS CONTABEIS';
    $parameter->rps->service->serviceValue = 15.00;
    $parameter->rps->service->issWithheld = 2;
    $parameter->rps->service->aliquot = 5;
    $parameter->rps->service->valueDeductions = 0;
    $parameter->rps->service->otherDeductions = 0;
    $parameter->rps->service->valuePis = 0;
    $parameter->rps->service->valueConfis = 0;
    $parameter->rps->service->valueINSS = 0;
    $parameter->rps->service->valueIR = 0;
    $parameter->rps->service->valueCSLL = 0;
    $parameter->rps->service->discountCondition = 0;
    $parameter->rps->service->unconditionedDiscount = 0;

    //set tomador
    $parameter->rps->taker->type = 1;
    $parameter->rps->taker->name = 'Krypton Servicos Contabeis S/S';
    $parameter->rps->taker->document = 42784421000109;
    $parameter->rps->taker->municipalRegistration = 10876045716;
    //set tomador endereço
    $parameter->rps->taker->address->address = 'R Visconde De Taunay';
    $parameter->rps->taker->address->number = 173;
    $parameter->rps->taker->address->complement = '';
    $parameter->rps->taker->address->neighborhood = 'Sao Lucas';
    $parameter->rps->taker->address->zipCode = 30240300;
    $parameter->rps->taker->address->state = 'MG';
    $parameter->rps->taker->address->municipalityCode = 3106200;

    $lote = (new LoteRps($settings, $parameter->rpsLot));
    $rps = (new Rps($settings, $parameter->rps->number . $parameter->rps->serie));

    //set data
    $rps->setRpsIdentification($parameter);
    $rps->setService($parameter);
    $rps->setProvider();
    $rps->setTaker($parameter);

    //realiza chamada
    $signedRps = $rps->getSignedRps();
    $lote->addRps($signedRps);
    $result = $lote->sendLote();

    dd($result);

} catch (Exception $e) {
    throw $e;
}
