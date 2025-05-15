<?php

namespace App\Form;

use App\Entity\DispoExpert;
use App\Entity\Expert;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class DispoExpertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('expert', EntityType::class, [
                'class' => Expert::class,
                'choice_label' => 'name', // Ensure this matches the property you want to display
                'placeholder' => 'Select an Expert',
                'constraints' => [
                    new NotBlank(['message' => 'Please select an expert.']),
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'attr' => ['class' => 'form-control']
            ])
            ->add('time', TimeType::class, [
                'widget' => 'single_text',
                'required' => true,
                'attr' => ['class' => 'form-control']
            ])
            ->add('location', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Location cannot be empty.']),
                ],
                'attr' => ['class' => 'form-control', 'placeholder' => 'Enter location']
            ])
            ->add('status', CheckboxType::class, [
                'label' => 'Available',
                'required' => false,
                'attr' => ['class' => 'form-check-input']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DispoExpert::class,
        ]);
    }
}
