<?php

namespace App\Services\ModulKassa;

use App\Models\ModulkassaDocs;
use App\Models\Order;
use App\Services\Modulkassa\DTO\OrderDTO;
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
     * Запрос списка смен торговой точки
     * 
     * @param int $days количество дней
     * @return Collection список смен
     */
    public function getShifts(string $guid, int $days=1):Collection
    {
        return $this->get('/v1/retail-point/'.$guid.'/get-recent-shifts?days='.$days);
    }

    /**
     * Запрос документов смены торговой точки
     * 
     * @param int $days количество дней
     * @return Collection список смен
     */
    public function getCashDocs(string $pointId, string $shiftDocId):Collection
    {
        return $this->get("/v1/retail-point/{$pointId}/shift/{$shiftDocId}/cashdoc");
    }

    /**
     * Передать информацию о заказе в кассовую систему
     * 
     * @param Collection $points торговые точки, в которые нужно передать заказ
     * @param Order $order заказ
     */
    public function sendOrder(Collection $points, Order $order)
    {
        if($order->shipping_code=='self_pickup') {
            $description='Самовывоз ('.($order->delivery['warehouse']??'').')';
        }
        elseif($order->shipping_code=='own') 
        {
            $delivery=[
                'street'=>$order->delivery['street']??'',
                'house'=>$order->delivery['house']??'',
                'apartment'=>$order->delivery['apartment']??'',
            ];
            $description="ул. {$delivery['street']}, д.{$delivery['house']}, кв.{$delivery['apartment']}";
        }
        else $description='';

        $data = [
            'id'=>'',
            'documentNumber'=>"Заказ №{$order->id}",
            'documentType'=>'SALE',
            'documentDateTime'=>(string)$order->created_at->format('c'),
            //'customerContact'=>(string)'+'.$order->customer['phone']??'', //TODO телефон брать из данных пользователя
            'description'=>$description,
            'retailPointId'=>null,
            'prepaid'=>false,
            'inventPositions'=>[],
            'remoteId'=>$order->id,
            'responsURL'=>null,
        ];

        foreach ($order->body as $key=>$position)
        {
            if($position['measure']=='кг.') $measure='kg';
            else if($position['measure']=='г.') {
                $measure='kg';
                $position['quantity']=$position['quantity']/1000;
                $position['price']=$position['price']*1000;
            }
            else $measure = 'pcs';

            $price = floor($position['price']/100);
            
            $data['inventPositions'][]=[
                'barcode'=>null,
                'inventCode'=>$position['offer'],
                'name' => "{$position['product_title']}, {$position['offer_title']}",
                'description'=>null,
                'measure' => $measure,
                'quantity' => $position['quantity'],
			    'price' => $price,
			 	'minPrice' => 0,
                'inventoryType'=>'INVENTORY'
            ];
        }

        $points->each(function($point) use ($data)
        {
            $result = $this->post("/v2/retail-point/{$point}/order", $data);

            ModulkassaDocs::create([
                'order_id'=>$data['remoteId'],
                'guid'=>$result['id'],
                'point'=>$point,
                'order_info'=>$result,
            ]);
        });
    }

    public function getRetailPointUsers(string $pointId)
    {
        $result = $this->get("/v1/retail-point/{$pointId}/catalog/CONTRACTOR")->first();
        
        $result = array_filter($result, function($contractor){
            return $contractor['locked']!==true && in_array('CASHIER', $contractor['roles']);
        });

        return array_map(function($arr){
            return [
                'code'=>$arr['code'],
                'name'=>$arr['name']
            ];
        }, $result);
    }

    /** 
     * Удалить заказ из касс
     * @param int $order_id ID заказа
     */
    public function cancelOrder(int $order_id)
    {
        $docs = ModulkassaDocs::whereOrderId($order_id)->get();

        $docs->each(function($doc){
            $res = $this->delete("/v2/retail-point/{$doc->point}/order/$doc->guid");
            if($res) $doc->delete();
        });
    }

    /** 
     * DELETE запрос к API модулькассы
     * @param string $method метод в URL
     * @return bool
     */
    private function delete(string $method):bool
    {
        $result = $this->client->delete($this->host.$method);
        return $result->successful();
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
