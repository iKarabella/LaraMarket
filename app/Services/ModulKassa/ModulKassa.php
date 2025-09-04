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

    /**
     * Запрос списка торговых точек модулькассы
     * 
     * @return Collection список торговых точек
     */
    public function getRetailPoints():Collection
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

    /**
     * Отправить список товаров в систему модулькассы
     * 
     * @param Collection $get список товаров из бд.
     * @param Collection $guids список идентификаторов торговых точек модулькассы, в которые нужно загрузить товары.
     * @return void
     */
    public function catalogChanges(Collection $get, Collection $guids):void
    {
        $offers = $get->map(function ($offer) {
                            switch($offer->product_measure){
                                case 2: $measure = 'kg'; break;
                                case 3: $measure = 'pcs'; break;
                                default: $measure = 'other';
                            };

                            return json_encode([
                                'command'=>'add',
                                'entity'=>[
                                    'catalogType' => 'INVENTORY',
                                    'inventCode' => str_pad($offer->offer_id, 3, '0', STR_PAD_LEFT),
                                    'name' => "{$offer->product_title}, {$offer->offer_title}",
                                    'barcode' => $offer->barcode??null,
                                    'barcodes'=>[],
                                    'price'=>$offer->price/100,
                                    'measure' => $measure,
                                    'alcoholType' => 'NO_ALCOHOL',
                                    'itemType' => 'INVENTORY',
                                    'separateModifiers' => true
                                ]
                            ], JSON_UNESCAPED_UNICODE);
                        })
                      ->implode("\n---\n");

        $data = "### data begin ###\n{\"command\":\"clear\",\"catalogs\":[\"INVENTORY\"]}".$offers.="\n### data end ###";

        foreach ($guids as $guid) if ($guid) $this->post('/v1/retail-point/'.$guid.'/catalog-changes', $data);
    }

    /** 
     * GET запрос к API модулькассы
     * @param string $method метод в URL
     * @return Collection
     * @throws Exception
     */
    private function get(string $method):Collection
    {
        $result = $this->client->get($this->host.$method);
        if ($result->successful()) return $result->collect();
        else throw new Exception('Не удалось получить ответ.');
    }

    /** 
     * POST запрос к API модулькассы
     * @param string $method метод в URL
     * @param string|array $data тело запроса
     * @return Collection
     * @throws Exception
     */
    private function post(string $method, string|array $data)
    {
        if (is_array($data)) $result = $this->client->post($this->host.$method, $data);
        else $result = $this->client->withBody($data, 'text/plain')->post($this->host.$method);
        
        if ($result->successful()) return $result->collect();
        else throw new Exception('Не удалось получить ответ.');
    }
}
