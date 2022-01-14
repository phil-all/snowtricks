<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Gender;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

/**
 * Class RegistrationFormType
 * @package App\Form
 */
class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, ['label' => 'Votre prénom :'])
            ->add('lastName', TextType::class, ['label' => 'Votre nom : '])
            ->add('email', EmailType::class, ['label' => 'Email : '])
            ->add('gender', EntityType::class, [
                'label'        => 'Votre avatar : ',
                'class'        => Gender::class,
                'choice_label' => function ($choice) {
                    return ('male' === $choice->getGender()) ? 'Masculin' : 'Feminin';
                }
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type'            => PasswordType::class,
                'mapped'          => false,
                'first_options'   => ['label' => 'Votre mot de passe :'],
                'second_options'  => ['label' => 'Confirmez le mot de passe :'],
                'invalid_message' => 'Non correspondance des mots de passe',
                'constraints'     => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un mot de passe',
                    ]),
                    new Length([
                        'min'        => 8,
                        'minMessage' => 'Votre mot de passe doit comporter au minimum {{ limit }} caractères',
                        'max'        => 254,
                    ])
                ]
            ])
            ->add('rgpd', CheckboxType::class, [
                'constraints' => [
                    new IsTrue(['message' => 'Vous devez validez la confirmation des conditions générales.'])
                ],
                'label' => 'Je confirme avoir lu la #RGPD# concernant le traitement de mes données personnelles.'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
