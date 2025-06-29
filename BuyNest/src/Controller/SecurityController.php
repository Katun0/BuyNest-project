<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(
        Request $request,
        AuthenticationUtils $authUtils,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ): Response {
        $error = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();

        if ($request->isMethod('POST') && $request->request->get('_action') === 'register') {
            $user = new User();
            $form = $this->createForm(UserForm::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $user->setPassword(
                    $hasher->hashPassword($user, $form->get('password')->getData())
                );
                $em->persist($user);
                $em->flush();

                $this->addFlash('success', 'Cadastro realizado com sucesso. Faça login.');
                return $this->redirectToRoute('app_login');
            }

            // renderizar novamente com erros de registro
            return $this->render('login/login.html.twig', [
                'registrationForm' => $form->createView(),
                'last_username' => $lastUsername,
                'error' => $error
            ]);
        }

        $user = new User();
        $form = $this->createForm(UserForm::class, $user);

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'registrationForm' => $form,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
