<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Gender;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Regex;
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
            ->add('firstName', TextType::class, [
                'label' => 'Votre prénom :',
                'constraints' => [
                    new Length([
                        'min'        => 2,
                        'minMessage' => 'Votre prénom doit comporter au moins deux caractères',
                    ]),
                    new Regex([
                        'pattern'  => '/^(?=.*[A-Z])[a-zA-zÀ-ÖØ-öø-ÿœŒ\s\-\']+$/',
                        'message' => 'Votre prénom contient des caractères non valides',
                    ])
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Votre nom : ',
                'constraints' => [
                    new Length([
                        'min'        => 1,
                        'minMessage' => 'Votre nom doit comporter au moins un caractère',
                    ]),
                    new Regex([
                        'pattern'  => '/^(?=.*[A-Z])[a-zA-zÀ-ÖØ-öø-ÿœŒ\s\-\']+$/',
                        'message' => 'Votre nom contient des caractères non valides',
                    ])
                ],
            ])
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
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]).{0,}$/',
                        'message' => 'Le password doit comporter au moins un chiffre, une minuscule et une majuscule',
                    ])
                ],
            ])
            ->add('rgpd', CheckboxType::class, [
                'label'       => 'Je confirme avoir lu la #RGPD# concernant le traitement de mes données personnelles.',
                'constraints' => [
                    new IsTrue(['message' => 'Vous devez validez la confirmation des conditions générales.'])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
