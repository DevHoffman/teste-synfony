<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class MailerService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function boasVindas(string $to, string $nome = 'Anônimo'): void
    {
        // $email = (new TemplatedEmail())
        //     ->from('no-reply@techlog.com')
        //     ->to($to)
        //     ->subject('Bem-vindo à Techlog')
        //     ->htmlTemplate('emails/boas_vindas.html.twig')
        //     ->context([
        //         'usuario' => [
        //             'nome' => $nome,
        //         ]
        //     ]);

        $email = (new TemplatedEmail())
            ->from('no-reply@techlog.com')
            ->to($to)
            ->subject('Bem-vindo à Techlog')
            ->htmlTemplate('emails/boas_vindas.html.twig')
            ->context([
                'usuario' => [
                    'nome' => $nome,
                ]
            ]);

        $this->mailer->send($email);
    }
}
