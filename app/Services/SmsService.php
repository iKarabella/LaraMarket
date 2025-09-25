<?php

namespace App\Services;


class SmsService
{
    /**
     * Отправляем смс
     *
     * @return bool
     */
    public static function send($phone, $text): bool
    {
        //! временная заглушка на отправку смс
        return true;
        //! убрать после
        
        if(empty($phone) || empty($text)){
            // activity()->useLog('sms')->log("SmsService: Отсутствуют необходимые параметры ($phone, $text)");
            return false;
        }

        $url = 'https://admin.p1sms.ru/apiSms/create';

        $post_fields=[
            'apiKey' => config('app.p1sms_key'),
            'sms' => [[
                "channel" => "char",
                "sender" => "YouNaN",
                "text" => (string)$text,
                "phone" => (string)$phone
            ]]
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>json_encode($post_fields),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = json_decode(curl_exec($curl), true);

        if($response){
            if($response['status']=='success'){
                // activity()->useLog('sms')->log('Смс отправлена, ответ сервиса: '.json_encode($response['data']));
                return true;
            }
            else{
                // activity()->useLog('sms')->withProperties($response)->log('Не удалось отправить смс');
            }
        }
        else{
            // activity()->useLog('sms')->log("Не удалось подключится к сервису p1sms.", 1);
        }

        curl_close($curl);

        return false;
    }
}