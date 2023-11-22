<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AuthType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
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

    public function __construct(private MailerInterface $mailer)
    {
    }
    #[Route('/inscription', name: 'app_security.signIn')]
    public function signIn(Request $request, ORMEntityManagerInterface $manager) 
    {

        $user = new User();
        $form = $this->createForm(AuthType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            // On génère un token et on l'enregistre
            $user->setActivationToken(md5(uniqid()));

            // do anything else you need here, like send an email
            // On crée le message
            $email = (new TemplatedEmail())
            ->from('contact@snowtrick.fr')
            ->to($user->getEmail())
            ->subject('Activation du compte!')
            ->htmlTemplate( 'email/activation.html.twig')
           
            ->context( ['token' => $user->getActivationToken()]);
    

        $this->mailer->send($email);
       
        
            $manager->persist($user);
            $manager->flush();
           

           /*  return $this->redirectToRoute('app_login'); */
            // On retourne à l'accueil
      
        }

        return $this->render('security/signIn.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/activation/{token}", name="activation")
     */
    public function activation($token, UserRepository $usersrepo, ORMEntityManagerInterface $manager)
    {
        // On recherche si un utilisateur avec ce token existe dans la base de données
        $user = $usersrepo->findOneBy(['activation_token' => $token]);

        // Si aucun utilisateur n'est associé à ce token
        if (!$user) {
            // On renvoie une erreur 404
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }

        // On supprime le token
        $user->setActivationToken(null);
       $manager->persist($user);
      $manager->flush();

        // On génère un message
        $this->addFlash('success', 'Utilisateur activé avec succès, Veuillez vous connecter');

        // On retourne à l'accueil
        return $this->redirectToRoute('app_login');
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
    ): Response {

        $formPassword = $this->createForm(ResetPasswordType::class);
        $formPassword->handleRequest($request);
        if ($formPassword->isSubmitted() && $formPassword->isValid()) {
            $user = $repo->findOneByEmail($formPassword->get('email')->getData());

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
                    ->context($content);
    
                $this->mailer->send($email);

                $this->addFlash(
                    'success',
                    'Email envoyé avec succès'
                );
            }
            $this->addFlash(
                'danger',
                'un problème est survenu!'
            );
           
        }

        return $this->render(
            'security/resetPassword.html.twig',
            [
                'form' => $formPassword->createView()
            ]
        );
    }

    #[Route('/oubli/pass/{token)', name: 'app_reset')]
    public function reset(Request $request): Response
    {
        $formPassword = $this->createForm(ResetPasswordType::class);
        $formPassword->handleRequest($request);
        return $this->render(
        'security/resetPassword.html.twig',
        [
            'form' => $formPassword->createView()
        ]
    );
    }
}
