<?php

namespace App\Form;

use App\Entity\Programme;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProgrammeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du programme'
            ])
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date'
            ])
            ->add('progression', TextType::class, [
                'label' => 'Progression'
            ])
            ->add('evaluation', IntegerType::class, [
                'label' => 'Evaluation',
                'required' => false
            ])
            ->add('likes', IntegerType::class, [
                'label' => 'Likes',
                'required' => false
            ])
            ->add('dislikes', IntegerType::class, [
                'label' => 'Dislikes',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Programme::class,
        ]);
    }
} 