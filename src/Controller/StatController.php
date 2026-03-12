<?php

namespace App\Controller;

use App\Entity\Tache;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class StatController extends AbstractController
{
    #[Route('/stat', name: 'app_stat')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $repo = $entityManager->getRepository(Tache::class);
        $taches = $repo->findBy([
            'status' => 3, // tache terminée 
        ]);
        return $this->render('stat/index.html.twig', [
            'taches' => $taches
         ]);
    }
}
