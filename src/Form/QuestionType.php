<?php

namespace App\Form;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la question',
                'attr' => [
                    'placeholder' => 'Ex: Comment améliorer mon régime alimentaire?',
                    'class' => 'form-control mb-3'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer un titre']),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Le titre doit contenir au moins {{ limit }} caractères',
                        'max' => 255,
                        'maxMessage' => 'Le titre ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Détails de votre question',
                'attr' => [
                    'placeholder' => 'Décrivez votre question en détail...',
                    'rows' => 6,
                    'class' => 'form-control mb-3'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer le contenu de votre question']),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Le contenu doit contenir au moins {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'Catégorie',
                'choices' => [
                    'Nutrition' => 'Nutrition',
                    'Exercice physique' => 'Exercice physique',
                    'Santé mentale' => 'Santé mentale',
                    'Sommeil' => 'Sommeil',
                    'Médecine générale' => 'Médecine générale',
                    'Bien-être' => 'Bien-être',
                    'Autre' => 'Autre'
                ],
                'attr' => ['class' => 'form-select mb-3'],
                'placeholder' => 'Choisir une catégorie',
                'required' => true
            ])
            ->add('authorName', TextType::class, [
                'label' => 'Votre nom',
                'attr' => [
                    'placeholder' => 'Votre nom ou pseudo',
                    'class' => 'form-control mb-3'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre nom'])
                ]
            ])
            ->add('authorEmail', EmailType::class, [
                'label' => 'Votre email',
                'attr' => [
                    'placeholder' => 'exemple@email.com',
                    'class' => 'form-control mb-3'
                ],
                'required' => false,
                'constraints' => [
                    new Email(['message' => 'Veuillez entrer une adresse email valide'])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
} 