<?php

namespace App\Form;

use App\Entity\CategorieRessource;
use App\Entity\RessourceEducative;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RessourceEducativeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('contenu')
            ->add('auteur')
            ->add('datePublication', null, [
                'widget' => 'single_text',
            ])
            ->add('image')
            ->add('video')
            ->add('categorie', EntityType::class, [
                'class' => CategorieRessource::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RessourceEducative::class,
        ]);
    }
}