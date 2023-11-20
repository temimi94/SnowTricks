<?php

namespace App\Controller;

use App\Repository\ImagesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{
    #[Route('/image', name: 'app_image')]
    public function index(ImagesRepository $repo): Response
    {

        $image = $repo->findAll();
        return $this->render('image/index.html.twig', [
            'images' => $image,
           
        ]);
    }
}
