<?php

namespace App\Controller;

use App\Entity\Tache;
use App\Form\TacheTermineeType;
use App\Form\TacheType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * EditController Class permettant la gestion d’édition  
 *  - Permet la modification des données d’une tache 
 *  - 
 */
final class EditController extends AbstractController
{
    #[Route('/edit', name: 'app_edit')]
    /**
     * index Route par défaut, interdite car il faut un paramètre pour identifier la tache à modifier  
     *
     * @return Response
     */
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

    #[Route('/edit/tache/', name: 'app_edit__tache')]
    /**
     * tache Route interdite car il faut un paramètre pour identifier la tache à modifier  
     *
     * @return Response
     */
    public function tache(): Response
    {
        $this->addFlash('danger', '
        <p>⛔ Vous n\'avez pas acces à cette route! 
        <br> Pour éditer cliquer sur le lien d\'édition de la tache ou de la catégorie que vous souhaitez éditer.</p>');
        return   $this->redirectToRoute('app_home');
    }

    #[Route('/edit/tache/{id<\d+>}', name: 'app_edit_tache')] // L'ID ne peut contenir que des chiffres     
    /**
     * editTache Permet d’éditer une tache 
     *
     * @param  mixed $id L’identifiant de la tache à éditer  
     * @param  mixed $request La requête contenant les données à modifier     
     * @param  mixed $entityManager Entité de gestion de la base de donnée      
     * @return Response Les données sont retournées pour affichage 
     */
    public function editTache(Tache $id, Request $request, EntityManagerInterface  $entityManager): Response
    {
        $repoTache =  $entityManager->getRepository(Tache::class);
        $tache =  $repoTache->find($id);

        $form = $this->createForm(TacheType::class, $tache);
        $form2 = $this->createForm(TacheTermineeType::class, $tache);

        $form->handleRequest($request);
        $form2->handleRequest($request);
        if (($form->isSubmitted() && $form->isValid()) or ($form2->isSubmitted() && $form2->isValid())) {

            $tacheData =  $form->getData();
            /*************************************************
             * Gestion des dates de début et de fin de tache *
             *************************************************/
            $statusId =  $form->get('status')->getData()->getId();
            switch ($statusId) {
                case '1': // Si le status de la tâche repasse en "En attente" alors reset des données de temps
                    $tache->setDateFin(null);
                    $tache->setDateDebut(null);
                    break;
                case '2': // Si le status de la tâche est "En cours" alors insertion d'une date de début de la tache
                    $dateDebut = $tache->getDateDebut();
                    if ($dateDebut === null) { // Modifie la date de début uniquement si elle n'a pas été déjà définie (en cas de modification d'un autre champ, il ne faut pas que la date change)
                        $tache->setDateDebut(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Brussels')));
                    }
                    break;
                case '3': // Si le status de la tâche est "Terminée" alors insertion d'une date de fin
                    $maintenant = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Brussels'));
                    $tache->setDateFin($maintenant);
                    break;

                default:
                    $this->addFlash('danger', '<p>⛔ Une erreur c\'est produite. Statut de tache incorrect !!  
        <br> Pour éditer cliquer sur le lien d\'édition de la tache ou de la catégorie que vous souhaitez éditer.</p>');

                    return   $this->redirectToRoute('app_home');
                    break;
            }

            // Enregistrement des données en base  
            $entityManager->persist($tacheData);
            $entityManager->flush();
            $this->addFlash('success', 'Votre tache à été modifiée.');

            return $this->redirectToRoute('app_home');
        }

        // Gestion de la modification du statut si on édite une tache terminée, on ne peut que modifier son statut et rien d'autre. 
        $tacheStatus = $tache->getStatus()->getId();
        if ($tacheStatus === 3) {
            $form2 = $this->createForm(TacheTermineeType::class, $tache);
            $this->addFlash('info', '<span style="font-size: xx-large;">🛈</span> Pour cette tâche, vous ne pouvez modifier que son statut car c’est une tache terminée.');
            return $this->render('edit/tache.html.twig', [
                'form' => $form2->createView(),
            ]);
        } else {
            return $this->render('edit/tache.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    }
}
