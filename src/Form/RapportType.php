<?php

namespace App\Form;

use App\Entity\Consultation;
use App\Entity\Rapport;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RapportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ 'date' (validation gérée par l'entité)
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date du rapport',
                'attr' => ['class' => 'form-control'],
            ])
            // Champ 'description' (validation gérée par l'entité)
            ->add('description', TextareaType::class, [
                'label' => 'Description du rapport',
                'attr' => ['class' => 'form-control', 'rows' => 5, 'placeholder' => 'Décrivez le rapport...'],
            ])
            // Champ 'contenu' (validation gérée par l'entité)
            ->add('contenu', TextareaType::class, [
                'label' => 'Contenu du rapport',
                'attr' => ['class' => 'form-control', 'rows' => 8, 'placeholder' => 'Entrez le contenu du rapport...'],
            ])
            // Champ 'consultation' (validation gérée par l'entité)
            ->add('consultation', EntityType::class, [
                'class' => Consultation::class,
                'choice_label' => 'id', // Affiche l'ID, mais vous pouvez ajouter un affichage plus explicite ici
                'label' => 'Consultation associée',
                'placeholder' => 'Sélectionner une consultation', // Ajout d'un placeholder
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rapport::class,
        ]);
    }
}
