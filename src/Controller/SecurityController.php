<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AuthType;
use Doctrine\ORM\EntityManagerInterface as ORMEntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    #[Route('/inscription', name: 'app_security.signIn')]
    public function signIn(Request $request, ORMEntityManagerInterface $manager) 
    {

        $user = new User();
        $form = $this->createForm(AuthType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $manager->persist($user);
            $manager->flush();
            $this->addFlash(
                'success',
                'Votre compte à bien été crée !'
              );

            return $this->redirectToRoute('app_security_login');
        }

        return $this->render('security/signIn.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/connexion', name: 'app_security_login')]
    public function login()
    {
        if ($this->getUser()) {
              return $this->redirectToRoute('app_tricks');
            }
        return $this->render('security/login.html.twig');
    }



    #[Route('/deconnexion', name: 'app_security.logout')]
    public function logout(): void
    {
        throw new \LogicException();
    }
}
