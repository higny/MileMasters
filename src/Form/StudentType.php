<?php

namespace App\Form;

use App\Entity\SchoolClass;
use App\Entity\Student;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Nom')
            ->add('Prenom', TextType::class, [
                "label" => "Prénom"
            ])
            ->add('Sexe', ChoiceType::class, [
                'choices' => [
                    'Homme' => true,
                    'Femme' => false,
                ],
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('Class', EntityType::class, [
                'class' => SchoolClass::class,
                'choice_label' => 'ident',
                'label' => 'Classe'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
        ]);
    }
}
