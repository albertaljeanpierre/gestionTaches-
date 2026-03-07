<?php

namespace App\Controller;

use App\Entity\Tache;
use App\Form\TacheType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AddTacheController extends AbstractController
{
    #[Route('/add/tache', name: 'app_add_tache')]
    public function addTache(Request $request, EntityManagerInterface $entityManager): Response
    {

        $tache = new Tache();
        $options = [
            'csrf_field_name' => 'delete_token',
            'csrf_token_id' => 'delete',
        ];
        // $options permet de passer des informations personnalisées pour le champ caché CSRF
        $form = $this->createForm(TacheType::class, $tache, $options);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $task = $form->getData();
            $statusId =  $form->get('status')->getData()->getId();
            if ($statusId === 3) { // Si le status de la tâche est "Terminée" alors interdiction de créer la tache directement.
                $this->addFlash('danger', 'Vous ne pouvez pas créer une tâche qui a le statut « Terminer » directement, créer une tâche avec le statut « En attente ».');
                return $this->redirectToRoute('app_add_tache');
            }
            if ($statusId === 2) { // Si le status de la tâche est "En cours" alors insertion d'une date
                // Données de statistique : Date de début  
                $tache->setDateDebut(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Brussels') ));
            }
            $entityManager->persist($tache);
            $entityManager->flush();
            $this->addFlash('success', 'Votre tache à été ajoutée!');

            return $this->redirectToRoute('app_home');
        }
        return $this->render('add_tache/ajoutTache.html.twig', [
            'form' => $form,
        ]);
        // return $this->render('add_tache/index.html.twig', [
        //     'form' => $form,
        // ]);
    }
}
