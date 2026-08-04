<?php

namespace App\Http\Controllers;


class SendSms
{
    private $url;
    private $token;
    private $credit;
    private $message;
    private $number;
    private $sender;
    private $msgid;
    public function __construct($url, $token)
    {
        $this->url = $url . '/';
        $this->token = '?token=' . $token;
        $this->credit = '&credit=';
        $this->sender = '&sender=';
        $this->number = '&number=';
        $this->message = '&message=';
        $this->msgid = '&msgid=';
    }
    public function __destruct()
    {
    }
    function sendme($smsurl)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $smsurl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }
    function sendmessage($credit, $sender, $message, $number)
    {
        $message = urlencode($message);
        $smsurl = $this->url . 'sendsms/' . $this->token . $this->sender . $sender . $this->number . $number . $this->credit . $credit . $this->message . $message;
        $result = $this->sendme($smsurl);
        return $result;
    }
    function checkdlr($message_id)
    {
        $smsurl = $this->url . 'Dlrcheck/' . $this->token . $this->msgid . $message_id;
        $result = $this->sendme($smsurl);
        return $result;
    }
    function availablecredit($credit)
    {
        $smsurl = $this->url . 'Credit-Balance/' . $this->token . $this->credit . $credit;
        $result = $this->sendme($smsurl);
        return $result;
    }
}
