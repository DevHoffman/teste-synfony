<?php

namespace App\Controller;

use App\Service\MailerService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MailController extends AbstractController
{
    #[Route('/enviar-boas-vindas', name: 'enviar_boas_vindas')]
    public function boasVindas(MailerService $mailer): Response
    {
        /** @var \App\Entity\Users $usuario */
        $usuario = $this->getUser();

        if (!$usuario) {
            throw $this->createAccessDeniedException();
        }

        $mailer->boasVindas($usuario->getEmail(), $usuario->getNome());

        return new Response('E-mail de boas-vindas enviado com sucesso.');
    }
}
