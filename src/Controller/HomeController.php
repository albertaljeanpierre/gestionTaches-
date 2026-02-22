<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Tache;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {

        $repoTache = $entityManager->getRepository(Tache::class);
        $repoCategorie =  $entityManager->getRepository(Categorie::class);
        $nbCategorie = count($repoCategorie->findAll()); 




        $tacheEnAttente =  [];  
        for ($i=1; $i <= $nbCategorie ; $i++) { 
            $tacheEnAttente[] =  $repoTache->findBy([
            'status' => 1, // tache en attente 
            'categorie' => $i, 
        ]);
        }
      

  // dd($tacheEnAttente);

        return $this->render('home/index.html.twig', [
            'nbTacheEnAttente' => ( count($tacheEnAttente, COUNT_RECURSIVE) - count($tacheEnAttente )) ,
            'tacheEnAttente' => $tacheEnAttente,

        ]);
    }
}
