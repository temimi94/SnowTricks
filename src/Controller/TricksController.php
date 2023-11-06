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


 /** 
  * Afficher toutes les figures
  * */
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

/** 
  * Créer une figure 
  * */
  #[Route("/tricks/new", name: "app_tricks_new")]
  public function new(Request $request, EntityManagerInterface  $manager): Response
  {

    $trick = new Tricks();
    $form = $this->createForm(TrickType::class, $trick);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $trick = $form->getData();
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
  #[Route("tricks/edit/{id}", name: "app_tricks_update")]
  public function update(TricksRepository $repo, int $id, Request $request, EntityManagerInterface $manager): Response
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
        'form' => $form->createView()
      ]
    );
  }


  /** 
  * Afficher une figure par son Id
  * */

  #[Route('/tricks/{id}', name: 'app_tricks_show')]
  public function show(TricksRepository $repo, $id): Response
  {

    $trick = $repo->find($id);
    return $this->render('tricks/show.html.twig', [
      'trick' => $trick

    ]);
  }



  /** 
  * Supprimer une figure 
  * */
  #[Route('/tricks/delete/{id}', name: 'app_tricks_delete')]
  public function delete(EntityManagerInterface $manager, Tricks $trick): Response
  {
    dump($trick);
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
