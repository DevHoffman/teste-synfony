<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\HttpFoundation\Request;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    #[Route('/query', name: 'query_example')]
    public function queryExample(Request $request): Response
    {
        $nome = $request->query->get('nome'); // exemplo: /home/query?nome=Thyago

        return new Response("Olá, $nome!");
    }
}
