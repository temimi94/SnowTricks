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

class TricksController extends AbstractController
{
  #[Route('/', name: 'app_tricks')]
  public function index(TricksRepository $repo): Response
  {

    $tricks = $repo->findAll();


    return $this->render('tricks/index.html.twig', [
      'controller_name' => 'TricksController',
      'tricks' => $tricks
    ]);
  }
  #[Route("/tricks/{id}/edit", name: "app_tricks_edit")]
  #[Route("/tricks/new", name: "app_tricks_new")]
 

  public function form(Tricks $trick = null, Request $request, EntityManagerInterface  $manager): Response
  {
    if (!$trick) {
      $trick = new Tricks();
    }

    $form = $this->createForm(TrickType::class, $trick);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      if (!$trick->getId()) {
        $trick->setCreatedAt(new \DateTime());
      }

      $manager->persist($trick);
      $manager->flush();
      return $this->redirectToRoute('app_tricks_show', ['id' => $trick->getId()]);
    }

    return  $this->render(
      'tricks/create.html.twig',
      [
        'formTrick' => $form->createView(),
        'editMode' => $trick->getId() !== null

      ]
    );
  }

  #[Route('/tricks/{id}', name: 'app_tricks_show')]
  public function show(TricksRepository $repo, $id): Response
  {

    $trick = $repo->find($id);
    return $this->render('tricks/show.html.twig', [
      'trick' => $trick
    ]);
  }
}
