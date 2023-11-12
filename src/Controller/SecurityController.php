<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AuthType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface as ORMEntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/inscription', name: 'app_security.signIn')]
    public function signIn(Request $request, ORMEntityManagerInterface $manager)
    {

        $user = new User();
        $form = $this->createForm(AuthType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $manager->persist($user);
            $manager->flush();
            $this->addFlash(
                'success',
                'Votre compte à bien été crée !'
            );

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/signIn.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/connexion', name: 'app_login')]
    public function index(AuthenticationUtils $AuthenticationUtils): Response
    {
        return $this->render('security/login.html.twig', [
            'last_username' => $AuthenticationUtils->getLastUsername(),
            'error' => $AuthenticationUtils->getLastAuthenticationError()
        ]);
    }


    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): never
    {
        // controller can be blank: it will never be called!
        throw new \Exception();
    }


    #[Route('/recuperation/motdepasse', name: 'app_password', methods: ['GET', 'POST'])]
    public function resetPassword(
        Request $request,
        UserRepository $repo,
        TokenGeneratorInterface $tokengenerate,
        ORMEntityManagerInterface $manager,
        UrlGeneratorInterface $urlGenerate,
        MailerInterface $mailer
    ): Response {

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $repo->findOneByEmail($form->get('email')->getData());

            //verification user existant
            if ($user) {
                //génerer un token
                $token = $tokengenerate->generateToken();

                $user->setResetToken($token);

                $manager->persist($user);
                $manager->flush();

                //generer un lien 
                $url = $this->generateUrl('app_reset', ['token' => $token], $urlGenerate::ABSOLUTE_URL);

                //on creer les donnees du mail
                $content = compact('user', 'url');
                //envoyer le mail
                $email = (new TemplatedEmail())
                    ->from('rogaya086@gmail.com')
                    ->to($user->getEmail())
                    ->subject('Réinitialisation de votre mot de passe')
                   
                    ->htmlTemplate('/email/reset.html.twig')
                    ->context([
                        'url' => $url
                    ]);


                $mailer->send($email);
              

               
                $this->addFlash(
                    'success',
                    'Email envoyé avec succès'
                );
                return $this->redirectToRoute('app_login');
            }
            $this->addFlash(
                'danger',
                'un problème est survenu!'
            );
            return $this->redirectToRoute('app_login');
        }
        return $this->render(
            'security/resetPassword.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    #[Route('/oubli/pass/{token)', name: 'app_reset')]
    public function reset(): Response
    {
        return $this->render(
            'security/resetPassword.html.twig');
    }
}
