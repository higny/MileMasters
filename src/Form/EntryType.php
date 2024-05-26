<?php

namespace App\Form;

use App\Entity\Entry;
use App\Entity\Student;
use App\Entity\Test;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class EntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Test', EntityType::class, [
                'class' => Test::class,
                'choice_label' => 'name',
                'label' => 'Épreuve',
            ])
            ->add('Tstart', TimeType::class, [
                'label' => 'Heure de départ',
            ])
            ->add('student', EntityType::class, [
                'class' => Student::class,
                'label' => 'Étudiant',
                'choice_label' => function ($student) {
                    return $student->getPrenom() . ' ' . $student->getNom();
                },
                ])
            ->add('Rw', ChoiceType::class, [
                'choices' => [
                    'Course' => true,
                    'Marche' => false,
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'Type d\'activité',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Entry::class,
        ]);
    }
}
