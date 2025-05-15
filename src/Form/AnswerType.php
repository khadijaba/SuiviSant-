<?php

namespace App\Form;

use App\Entity\Answer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AnswerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => 'Votre réponse',
                'attr' => [
                    'placeholder' => 'Rédigez votre réponse ici...',
                    'rows' => 5,
                    'class' => 'form-control mb-3'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer une réponse']),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'La réponse doit contenir au moins {{ limit }} caractères'
                    ])
                ]
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
            'data_class' => Answer::class,
        ]);
    }
} 