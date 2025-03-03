<?php

namespace App\Form;

use App\Entity\ProgrammeSante;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProgrammeSanteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('localisation', TextType::class, [
                'label' => 'Localisation',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('objectif', TextType::class, [
                'label' => 'Objectif',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProgrammeSante::class,
        ]);
    }
}
