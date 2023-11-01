<?php

namespace App\Controller;

use App\Entity\Tricks;
use App\Form\TrickType;
use App\Repository\TricksRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;


class TricksController extends AbstractController
{


  /* Afficher toutes les figures */
  #[Route('/', name: 'app_tricks')]
  public function index(TricksRepository $repo, PaginatorInterface $paginator, Request $request): Response
  {

    $tricks = $repo->findAll();

    $tricks = $paginator->paginate(
      $repo->findAll(),
      $request->query->getInt('page', 1),
      10
    );
    return $this->render('tricks/index.html.twig', [
      'controller_name' => 'TricksController',
      'tricks' => $tricks
    ]);
  }


  /* Afficher créer et modifier une figure */
  #[
    Route("/tricks/new", name: "app_tricks_new"),
    Route("/tricks/{id}/update", name: "app_tricks_update")
  ]

  public function formTrick(Tricks $trick = null, Request $request, EntityManagerInterface  $manager): Response
  {
    if (!$trick) {
      $trick = new Tricks();
    }

    $form = $this->createForm(TrickType::class, $trick);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      if (!$trick->getId()) {
        $trick->setCreatedAt(new \DateTimeImmutable());
      }

      $manager->persist($trick);
      $manager->flush();
      return $this->redirectToRoute('app_tricks');
    }

    return  $this->render(
      'tricks/create.html.twig',
      [
        'formTrick' => $form->createView(),
        'editMode' => $trick->getId() !== null

      ]
    );
  }

  /* Afficher une figure par son id*/

  #[Route('/tricks/{id}', name: 'app_tricks_show')]
  public function show(TricksRepository $repo, $id): Response
  {

    $trick = $repo->find($id);
    return $this->render('tricks/show.html.twig', [
      'trick' => $trick
    ]);
  }
}
