<?php

namespace App\Form;

use App\Entity\DispoExpert;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class DispoExpertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('status', CheckboxType::class, [
                'label' => 'Status',
                'attr' => ['class' => 'form-check-input'],
                'required' => false,
            ])
            ->add('time', TimeType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('location', TextType::class, [
                'attr' => ['class' => 'form-control', 'placeholder' => 'Enter location'],
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DispoExpert::class,
        ]);
    }
}
