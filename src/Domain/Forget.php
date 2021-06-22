<?php namespace App\Domain;

use App\Domain\Exceptions\UserNotFoundException;
use App\Repository\UsersRepository;
use App\Services\Auth;
use App\Services\Mail;
use DateTime;

class Forget
{
    public function __construct(
        protected UsersRepository $usersRepository
    ){}

    public function sendEmail(string $email): array
    {
        $user = $this->usersRepository->findOneBy([
            "email" => $email
        ]);

        if (!$user) {
            throw new UserNotFoundException('User not found');
        }

        $mail = new Mail;

        $userName = $user->getName();

        $token = Auth::createToken([
            'user_id'    => $user->getId(),
            'email'      => $user->getEmail(),   
            'exp'   => (new DateTime('+1 hour'))->getTimestamp()
        ]);

        $html = <<<HTML
            Olá <strong>$userName</strong>,
            <br>
            <p>Você acaba de solicitar a alteração da sua senha.</p>
            <p>Segue seu token para finalizar esse processo de alteração.</p>
            <p>Mande esse token no authorization do header da requisição para alterar sua senha:</p>
            <p><strong>Token:</strong> Bearer $token</p>
        HTML;

        $response = $mail
            ->setFrom($_ENV['MJ_FROM_EMAIL'], $_ENV['MJ_FROM_NAME'])
            ->setTo($email, $user->getName())
            ->setSubject('Promobit Teste - Alteração de senha')
            ->setHTMLPart($html)
        ->send();

        if (!$response->success()) {
            throw new \Exception('Email not sended');
        }

        return [
            'success' => [
                'message' => "Email sended for: $email"
            ]
        ];
    }
}