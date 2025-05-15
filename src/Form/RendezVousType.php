<?php

namespace App\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use App\Entity\RendezVous;
use App\Entity\DispoExpert;

class RendezVousType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $selectedDispo = $options['selected_dispo'] ?? null;

        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email Address',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Enter your email'],
                'required' => true
            ])
            ->add('phoneNumber', TelType::class, [
                'label' => 'Phone Number',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Phone: 123-456-7890'],
                'required' => true
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Enter your message', 'rows' => 4],
                'required' => false
            ])
            ->add('dispoExpert', EntityType::class, [
                'class' => DispoExpert::class,
                'choice_label' => function (DispoExpert $dispo) {
                    return $dispo->getDate()->format('Y-m-d H:i') . ' - ' . $dispo->getLocation();
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->where('d.status = :status')
                        ->setParameter('status', true);
                },
                'label' => 'Selected Availability',
                'attr' => ['class' => 'form-control', 'readonly' => true], // Make it readonly
                'placeholder' => 'Select an available slot',
                'required' => true,
                'data' => $selectedDispo, // Prefill the selected dispo
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Book Appointment',
                'attr' => ['class' => 'btn btn-primary mt-3']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RendezVous::class,
            'selected_dispo' => null, // Allow passing the selected dispoExpert
        ]);
    }
}
