<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(Request $request): Response
    {
        $error = null;

        if ($request->isMethod('POST')) {
            $username = $request->request->get('username');
            $password = $request->request->get('password');

            // Exemplo simples, autenticação fictícia
            if ($username === 'admin' && $password === '1234') {
                return $this->redirectToRoute('app_home'); // depois cria essa rota
            } else {
                $error = 'Usuário ou senha inválidos.';
            }
        }

        return $this->render('login/login.html.twig', [
            'error' => $error,
        ]);
    }
}
