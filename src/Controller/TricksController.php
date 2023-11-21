<?php

namespace App\Controller;

use App\Entity\Tricks;
use App\Entity\Images;
use App\Entity\Videos;
use App\Entity\Comment;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\TrickType;
use App\Repository\CommentRepository;
use App\Repository\TricksRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class TricksController extends AbstractController
{


  /** 
   * Afficher toutes les figures
   * */
  #[Route('/', name: 'app_tricks')]
  public function index(TricksRepository $repo, PaginatorInterface $paginator, Request $request): Response
  {

   // Get 15 tricks from position 0
   $tricks = $repo->findBy([], ['createdAt' => 'DESC'], 15, 0);

    $tricks = $paginator->paginate(
      $repo->findAll(),
      $request->query->getInt('page', 1),
      15
    );


    return $this->render('tricks/index.html.twig', compact('tricks'));
  }

  /** 
   * Créer une figure 
   * */
  #[Route("/tricks/new", name: "app_tricks_new")]
  #[Security("is_granted('ROLE_USER')")]

  public function new(Request $request, EntityManagerInterface  $manager): Response
  {

    $trick = new Tricks();

    $form = $this->createForm(TrickType::class, $trick);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      $trick = $form->getData();
      $trick->setUser($this->getUser());


      $medias = $form->get('TriksImage')->getData();
      foreach ($medias as $media) {
        $fichier = md5(uniqid()) . '.' . $media->guessExtension();
        try {
          $media->move(
            $this->getParameter('trick_img_directory'),
            $fichier
          );
        } catch (FileException $e) {
          //
        }
        $photo = new Images();
        $photo->setImageName($fichier);
        $trick->addImage($photo);
      }
      $trick->addImage($photo);


      foreach ($trick->getVideos() as $video) {
        $video->setTrick($trick);
        $manager->persist($video);
      }
      $trick->setUser($this->getUser());
      
      $manager->persist($trick);
      $manager->flush();
      $this->addFlash(
        'success',
        'Votre figure à bien été crée avec succès !'
      );
      return $this->redirectToRoute('app_tricks');
    }
    return  $this->render('tricks/create.html.twig', [
      'formTrick' => $form->createView()
    ]);
  }

  /** 
   * Modifier une figure 
   * */
  /* Modifier une figure */
  #[Security("is_granted('ROLE_USER') and user === trick.getUser()")]
  #[Route("tricks/edit/{slug}", name: "app_tricks_update")]
  public function update(Tricks $trick, TricksRepository $repo, int $id, Request $request, EntityManagerInterface $manager): Response
  {
    $trick = $repo->findOneBy(['id' => $id]);
    $form = $this->createForm(TrickType::class, $trick);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $trick = $form->getData();
      $manager->persist($trick);
      $manager->flush();
      $this->addFlash(
        'success',
        'Votre figure à bien été modifiée avec succès !'
      );
      return $this->redirectToRoute('app_tricks');
    }
    return $this->render(
      'tricks/update.html.twig',
      [
        'formEdit' => $form->createView()
      ]
    );
  }



  /** 
   * Afficher une figure par son Id
   * */

  #[Route('/tricks/{slug}', name: 'app_tricks_show')]
  public function show(Tricks $trick, Request $request,  EntityManagerInterface $manager, CommentRepository $repoComment): Response
  {


    $comment = new Comment();
    $form = $this->createForm(CommentType::class, $comment);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $user = $this->getUser();
      $comment->setUser($user);
      $comment->setTrick($trick);
      $comment->setCreatedAt(new \DateTime());

      $manager->persist($comment);
      $manager->flush();

      return $this->redirectToRoute('app_tricks_show', ['slug' => $trick->getSlug()]);
    }


    return $this->render('tricks/show.html.twig', [
      'trick' => $trick,
      'comment' => $form->createView(),
      'listComments' => $repoComment->findBy(['trick' => $trick])



    ]);
  }



  /** 
   * Supprimer une figure 
   * */
  #[Route('/tricks/delete/{id}', name: 'app_tricks_delete')]
  #[Security("is_granted('ROLE_USER') and user === trick.getUser()")]
  public function delete(EntityManagerInterface $manager, Tricks $trick): Response
  {
    if (!$trick) {
      $this->addFlash(
        'success',
        'La figue ,n/existe pas  !'
      );
    }
    $manager->remove($trick);
    $manager->flush();

    $this->addFlash(
      'success',
      'Votre figure à bien été supprimé avec succès !'
    );
    return $this->redirectToRoute('app_tricks');
  }
}
