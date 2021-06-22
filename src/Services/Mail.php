<?php namespace App\Services;

use \Mailjet\Resources;

class Mail
{
    protected \Mailjet\Client $client;

    protected array $body;

    protected array $messages;

    protected array $from;
    
    protected array $to;

    protected string $subject;

    protected string $textPart = "";

    protected string $HTMLPart;

    public function __construct()
    {
        $this->client = new \Mailjet\Client(
            $_ENV['MJ_APIKEY_PUBLIC'], 
            $_ENV['MJ_APIKEY_PRIVATE'], 
            true, 
            ['version' => 'v3.1']
        );
    }

    public function setFrom(string $senderEmail, string $name): self
    {
        $this->from = ['Email' => $senderEmail, 'Name' => $name];

        return $this;
    }

    public function setTo(string $recepientEmail, string $name): self
    {  
        $this->to[] = ['Email' => $recepientEmail, 'Name' => $name];
        return $this;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    public function setTextPart(string $text): self
    {
        $this->textPart = $text;
        return $this;
    }

    public function setHTMLPart(string $html): self
    {
        $this->HTMLPart = $html;
        return $this;
    }

    public function send(): \Mailjet\Response
    {
        $this->body = [
            'Messages' => [
               [
                'From'       => $this->from,
                'To'         => $this->to,
                'Subject'    => $this->subject,
                'TextPart'   => $this->textPart,
                'HTMLPart'   => $this->HTMLPart,
               ]
            ]
        ];

        return $this->client->post(Resources::$Email, ['body' => $this->body, 'timeout' => 10000,  'connect_timeout' => 10000]);
    }
}