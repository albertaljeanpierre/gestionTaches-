<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Status;
use App\Entity\Tache;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TacheTermineeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'disabled' => true,
            ])
            ->add('description', TextType::class, [
                'disabled' => true,
            ])
            ->add('status', EntityType::class, [
                'class' => Status::class,
                'choice_label' => 'nom',
                'label' => 'Statut',
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'disabled' => true,
                'label' => 'Catégorie',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Modifier le statut de cette tache',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tache::class,
        ]);
    }
}
