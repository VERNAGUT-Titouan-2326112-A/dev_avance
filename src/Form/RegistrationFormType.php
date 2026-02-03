<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => false,
                'attr' => ['placeholder' => 'Firstname', 'class' => 'form-control'],
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('lastName', null, [
                'label' => false,
                'attr' => ['placeholder' => 'Lastname', 'class' => 'form-control'],
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('email', EmailType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Email ✉️', 'class' => 'form-control'],
                'constraints' => [new Assert\NotBlank(), new Assert\Email()],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'first_options' => ['label' => false, 'attr' => ['placeholder' => 'Password 🔒', 'class' => 'form-control']],
                'second_options' => ['label' => false, 'attr' => ['placeholder' => 'Confirm Password 🔒', 'class' => 'form-control']],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez entrer un mot de passe']),
                    new Assert\Length(['min' => 6, 'minMessage' => 'Votre mot de passe doit faire au moins {{ limit }} caractères']),
                ],
            ])
            ->add('userType', ChoiceType::class, [
                'mapped' => false, // Important : ce n'est pas une propriété de l'entité User
                'label' => 'Vous êtes :',
                'choices' => [
                    'Étudiant 🎓' => 'student',
                    'Professeur 🧑‍🏫' => 'teacher',
                ],
                'expanded' => true, // Boutons radio
                'multiple' => false,
                'data' => 'student', // Valeur par défaut
                'attr' => ['class' => 'd-flex justify-content-center gap-3 mb-3'], // Un peu de style
            ])
;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
