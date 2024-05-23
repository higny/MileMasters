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

class EntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('NoDos')
            ->add('Rw')
            ->add('Tstart')
            ->add('Test', EntityType::class, [
                'class' => Test::class,
                'choice_label' => 'name',
            ])
            ->add('student', EntityType::class, [
                'class' => Student::class,
                'choice_label' => function ($student) {
                    return $student->getPrenom() . ' ' . $student->getNom();
                },
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
