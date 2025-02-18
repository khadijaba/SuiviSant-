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

class ConsultationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text', // Utilisation du format YYYY-MM-DD
                'input' => 'datetime', // Indique que Symfony doit travailler avec un objet DateTime
                'label' => 'Date de consultation',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Sélectionnez une date'
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description de la consultation',
                'attr' => [
                    'class' => 'form-control', 
                    'rows' => 5, 
                    'placeholder' => 'Détails de la consultation...'
                ],
            ])
            ->add('rapport', EntityType::class, [
                'class' => Rapport::class,
                'required' => false,  // Permet à ce champ d'être vide
                'placeholder' => 'Sélectionner un rapport (optionnel)',
                'choice_label' => 'description', // Change selon ce que tu veux afficher
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Consultation::class,
        ]);
    }
}
