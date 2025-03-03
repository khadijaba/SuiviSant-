<?php

namespace App\Form;

use App\Entity\SuiviProgramme;
use App\Entity\ProgrammeSante;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class SuiviProgrammeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('progression')
            ->add('programme', EntityType::class, [
                'class' => ProgrammeSante::class,
                'choice_label'=>'nom',
                'placeholder' => 'Choose a programme', // Optional, if you want a default option
            ]);
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SuiviProgramme::class,
        ]);
    }
}
