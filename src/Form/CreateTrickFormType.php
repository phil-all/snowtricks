<?php

namespace App\Form;

use App\Entity\Trick;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class CreateTrickFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('title', TextType::class, ['label' => 'Nom de la figure :'])
            ->add('title', TextType::class, [
                'label' => 'Nom de la figure',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un nom de figure',
                    ])
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Description :',
                'attr'  => ['rows' => '10'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un nom de figure',
                    ])
                ]
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'label' => 'Groupe :',
                'choice_label' => 'category',
            ])
            ->add('thumbnail', FileType::class, [
                'mapped'   => false,
                'required' => false,
                'label'    => false,
            ])
            ->add('images', CollectionType::class, [
                'entry_type'    => ImageFormType::class,
                'entry_options' => ['label' => false],
                'allow_add'     => true,
                'allow_delete'  => true,
                'required'      => false,
                'label'         => false,
            ])
            ->add('videos', CollectionType::class, [
                'entry_type'    => VideoFormType::class,
                'entry_options' => ['label' => false],
                'allow_add'     => true,
                'allow_delete'  => true,
                'required'      => false,
                'label'         => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
