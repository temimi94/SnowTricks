<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\User;
use App\Form\AuthType;
use App\Form\ResetPasswordType;
use App\Form\ChangePasswordType;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface as ORMEntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class SecurityController extends AbstractController
{

    public function __construct(private MailerInterface $mailer)
    {
    }

    /**************************************************** */
    // SYSTEME D'INSCRIPTION 
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
                ->htmlTemplate('email/activation.html.twig')
                ->context(['token' => $user->getActivationToken()]);

            $this->mailer->send($email);
            $this->addFlash(
                'success',
                "Email envoyé avec succès pour l'activation de votre compte"
            );
            $manager->persist($user);
            $manager->flush();

            // On retourne à l'accueil
            return $this->redirectToRoute('app_tricks');
        }

        return $this->render('security/signIn.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**************************************************** */
    // SYSTEME D'ACTIVATION DU COMPTE
    #[Route("/activation/{token}", name: "activation")]

    public function activation($token, UserRepository $usersrepo, ORMEntityManagerInterface $manager)
    {
        // On recherche si un utilisateur avec ce token existe dans la base de données
        $user = $usersrepo->findOneBy(['activation_token' => $token]);

        // Si aucun utilisateur n'est associé à ce token
        if (!$user) {
            // On renvoie une erreur 404
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }
        if ($token != null && $token === $user->getActivationToken()) {
            // On supprime le token
            $user->setEnabled(true);
            $manager->persist($user);
            $manager->flush();

            // On génère un message
            $this->addFlash(
                'success',
                "Votre compte a été activé avec succès ! Vous pouvez désormais vous connecter !"
            );
        } else {
            $this->addFlash(
                'danger',
                "La validation de votre compte a échoué. Le lien de validation a expiré !"
            );
        }

        // On retourne à l'accueil
        return $this->redirectToRoute('app_login');
    }


    /**************************************************** */
    // SYSTEME DE CONNEXION
    #[Route('/connexion', name: 'app_login')]
    public function index(AuthenticationUtils $AuthenticationUtils): Response
    {
        return $this->render('security/login.html.twig', [
            'last_username' => $AuthenticationUtils->getLastUsername(),
            'error' => $AuthenticationUtils->getLastAuthenticationError()
        ]);
    }

    /**************************************************** */
    // SYSTEME DE DECONNEXION
    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): never
    {
        // controller can be blank: it will never be called!
        throw new \Exception();
    }


    /**************************************************** */
    // SYSTEME DE RECUPERATION DU PASSWORD
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
                    ->htmlTemplate('/email/reinitialisation.html.twig')
                    ->context($content);

                $this->mailer->send($email);

                $this->addFlash(
                    'success',
                    'Email envoyé avec succès'
                );
            } else {
                $this->addFlash(
                    'danger',
                    "L'adresse E-mail saisie n'est pas valide!"
                );
            }

            // On redirige vers la page de connexion
            return $this->redirectToRoute('app_login');
        }
        return $this->render(
            'security/resetPassword.html.twig',
            [
                'form' => $formPassword->createView()
            ]
        );
    }

    /**************************************************** */
    // SYSTEME D'INITIALISATION DE PASSWORD
    #[Route('/oubli/pass/{token)', name: 'app_reset')]
    public function changePassword(ORMEntityManagerInterface $manager, UserRepository $users,  UserPasswordHasherInterface $passwordHasher, Request $request, string $token = null)
    {
        // On cherche un utilisateur avec le token donné
        $user = $users->findOneBy(['activation_token' => $token]);
if($user) { 
        $form = $this->createForm(ChangePasswordType::class);

        $form->handleRequest($request);
    
     if($form->isSubmitted() && $form->isValid()) {
          
            // On supprime le token
            $user->setActivationToken('');
            dd($user);
            // On chiffre le mot de passe
            $user->setPassword($passwordHasher->hashPassword($user, $form->get('reset_token')->getData()));
         
            /*   if($form->isSubmitted() && $form->isValid()){
      
        
        
// On enregistre le nouveau mot de passe en le hashant
        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                $form->get('password')->getData()
            )
        ); */
            $manager->persist($user);
            $manager->flush();

            // On crée le message flash
            $this->addFlash('message', 'Mot de passe mis à jour');

            // On redirige vers la page de connexion
            return $this->redirectToRoute('app_login'
    );
}
            
            // Si on n'a pas reçu les données, on affiche le formulaire
            return $this->render('security/changePassword.html.twig', [
                'form' => $form->createView()
            ]);
        }     
    
}
}
