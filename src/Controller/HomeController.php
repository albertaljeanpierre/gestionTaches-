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
        $tacheEnCours =  [];
        for ($i = 1; $i <= $nbCategorie; $i++) {
            $tacheEnAttente[] =  $repoTache->findBy([
                'status' => 1, // tache en attente 
                'categorie' => $i,
            ]);
            $tacheEnCours[] =  $repoTache->findBy([
                'status' => 2, // tache en cours 
                'categorie' => $i,
            ]);
        }
        $nbTacheEnCours = (count($tacheEnCours, COUNT_RECURSIVE) - count($tacheEnCours));
        if ($nbTacheEnCours >= 5) {
            $this->addFlash('warning', ' Tu as 5 taches en cours ou plus attention à na pas te disperser…  ');
        }
        /************************************************
         * création du nom de fichier son de méditation *
         ************************************************/
        $dossier  = "/media/son/petitbambou/";
        $debutFichier = "daily_fr_";
        $finFichier = "_12.mp3";
        $numero = rand(1, 3);
        $numeroStr =  str_pad($numero, 3, "0", STR_PAD_LEFT); // ajouter des zéros 
        $path =  $dossier . $debutFichier . $numeroStr . $finFichier;

        return $this->render('home/index.html.twig', [
            'nbTacheEnAttente' => (count($tacheEnAttente, COUNT_RECURSIVE) - count($tacheEnAttente)),
            'nbTacheEnCours' =>  $nbTacheEnCours,
            'tacheEnAttente' => $tacheEnAttente,
            'tacheEnCours' => $tacheEnCours,
            'pathSon' => $path, 
            'numeroSon' => $numero, 
        ]);
    }
}
