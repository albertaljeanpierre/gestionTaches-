<?php

namespace App\Controller;

use App\Entity\Tache;
use App\Form\TacheType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EditController extends AbstractController
{
    #[Route('/edit', name: 'app_edit')]
    public function index(): Response
    {
        $this->addFlash('danger', '
        <p>⛔ Vous n\'avez pas acces à cette route! 
        <br> Pour éditer cliquer sur le lien d\'édition de la tache ou de la catégorie que vous souhaitez éditer.</p>');

        return   $this->redirectToRoute('app_home');

        // return $this->render('edit/index.html.twig', [
        //     'controller_name' => 'EditController',
        // ]);
    }

    #[Route('/edit/tache/{id<\d+>}', name: 'app_edit_tache')] // L'ID ne peut contenir que des chiffres 
    public function editTache(Tache $id, Request $request, EntityManagerInterface  $entityManager): Response
    {
        $repoTache =  $entityManager->getRepository(Tache::class);
        $tache =  $repoTache->find($id);
        // dd($tache ); 

        $form = $this->createForm(TacheType::class, $tache);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $tacheform =  $form->getData();
            $statusId =  $form->get('status')->getData()->getId();
            if ($statusId === 2) { // Si le status de la tâche est "En cours" alors insertion d'une date
                // Données de statistique : Date de début 
                $dateDebut = $tache->getDateDebut(); 
                if ($dateDebut === null) { // Modifie la date de début uniquement si elle n'a pas été déjà définie
                    $tache->setDateDebut(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Brussels')));
                } 
                
                // Dans le cas ou une tache passerait de "Terminée" à "En cours" il faut annuler les données déjà enregistrée : soit les champs  	date_fin, 	duree, 	duree_str. 
                $tache->setDuree(null); 
                $tache->setDateFin(null); 
                $tache->setDureeStr(null); 
            }
            if ($statusId === 3) { // Si le status de la tâche est "Terminée" alors insertion d'une date et calcul de la durée en deux formats
                // Données de statistique : Date de fin  
                $maintenant = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Brussels')); 
                $tache->setDateFin($maintenant);
                // Données de statistique : Calcul de la durée : Formatage timestamp 
                $dateDebut = $tache->getDateDebut(); 
                $duree = ( $dateDebut->getTimestamp() - $maintenant->getTimestamp()); 
                $tache->setDuree($duree ); 
                // Données de statistique : Calcul de la durée : Formatage lisible par un humain 

            }
            if ($statusId === 1) { // Si le status de la tâche repasse en "En attente" alors reset des données de temps
                 $tache->setDateFin(null); 
                 $tache->setDateDebut(null); 
            }

            $entityManager->persist($tacheform);
            $entityManager->flush();
            $this->addFlash('success', 'Votre tache à été modifiée.');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('edit/tache.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
