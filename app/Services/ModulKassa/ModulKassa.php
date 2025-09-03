<?php

namespace App\Services\ModulKassa;

use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

/**
 * Сервис для работы с системой "Модулькасса"
 */
class ModulKassa
{
    private string $login;
    private string $password;
    private string $host;
    private PendingRequest $client;

    public function __construct(?string $login=null, ?string $password=null, ?string $host=null){
        $this->login = $login ?? env('MODULKASSA_LOGIN');
        $this->password = $password ?? env('MODULKASSA_PASSWORD');
        $this->host = $host ?? 'https://service.modulpos.ru/api';
        $this->client = Http::withBasicAuth($this->login, $this->password);
        
        if(!$this->login || !$this->password || !$this->host || !$this->client) throw new Exception('Не удалось инициализировать сервис Модулькассы');

    }

    public function getRetailPoints()
    {
        return $this->get('/v1/retail-points')->map(function($point){
            return [
                // 'lastRequestInfo' => $point['lastRequestInfo']
                'type' => $point['type'],
                // 'pointsWithSharedCatalog' => $point['pointsWithSharedCatalog'],
                'remotePointInfo' => $point['remotePointInfo'],
                // 'connectedRemotePoints' => $point['connectedRemotePoints'],
                'paymentAggregatorMember' => $point['paymentAggregatorMember'],
                'aggregatorRetailPointId' => $point['aggregatorRetailPointId'],
                'clientKktRetailPointId' => $point['clientKktRetailPointId'],
                'agentPhone' => $point['agentPhone'],
                'partnerSiteId' => $point['partnerSiteId'],
                'name' => $point['name'],
                'id' => $point['id'],
                'address' => $point['address'],
                // 'settings' => $point['settings'],
                'inn' => $point['inn'],
                'payments' => $point['payments'],
                'posLinkToken' => $point['posLinkToken'],
                'kktStatus' => $point['kktStatus'],
                // 'registrationRequestStatus' => $point['registrationRequestStatus'],
                // 'kkmInfo' => $point['kkmInfo'],
                'fullAddress' => $point['fullAddress'],
                'timeZoneId' => $point['timeZoneId'],
                'phone' => $point['phone'],
            ];
        });
    }

    private function get(string $method):Collection
    {
        $result = $this->client->get($this->host.$method);
        if ($result->successful()) return $result->collect();
        else throw new Exception('Не удалось получить ответ.');
    }
}
