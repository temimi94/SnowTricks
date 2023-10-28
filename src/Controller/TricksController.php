<?php

namespace App\Controller;

use App\Entity\Tricks;
use App\Repository\TricksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TricksController extends AbstractController
{
  #[Route('/', name: 'app_tricks')]
  public function index(TricksRepository $repo): Response
  {
    //repo = $this->getDoctrine()->getRepository(Tricks::class);

    $tricks = $repo->findAll();


    return $this->render('tricks/index.html.twig', [
      'controller_name' => 'TricksController',
      'tricks' => $tricks
    ]);
  }

  #[Route('tricks/{id}', name: 'app_tricks_show')]
  public function show( TricksRepository $repo, $id): Response
  {
    
    $trick = $repo->findOne($id);
    return $this->render('tricks/show.html.twig', [
      'trick' => $trick
    ]);
  }
}
