<?php

namespace App\Jobs;

use App\Mail\NotificationAboutAdmission;
use App\Models\ExpectedOffer;
use App\Services\SmsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendNotificationsAboutAdmission implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public array $offers){}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        ExpectedOffer::whereIn('offer_id', $this->offers)->with(['offer', 'product', 'user'])->chunk(100, function($rows) {
            foreach ($rows as $row) 
            {
                $email = $phone = $name = null;
                $offername = "{$row->product->title}, {$row->offer->title}";

                if ($row->user) 
                {
                    if ($row->user->email && $row->user->email_verified_at) $email = $row->user->email;
                    else if ($row->user->phone && $row->user->phone_verifies_at) $phone = $row->user->phone;

                    $name = $row->user->name?($row->user->patronomyc?"{$row->user->name} {$row->user->patronymic}":$row->user->name):$row->user->nickname;
                }
                else {
                    $email = $row->email;
                    $name = $row->name;
                }

                if ($email) 
                {
                    Mail::to($email)->send(new NotificationAboutAdmission($name, $offername));
                }
                else if ($phone) 
                {
                    $sms = "В магазин ".config('app.name')." поступил товар {$offername}.";
                    SmsService::send($phone, $sms);
                }

                $row->delete();
            }
        });        
    }
}
