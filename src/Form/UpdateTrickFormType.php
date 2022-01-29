<?php

namespace App\Form;

use App\Entity\Trick;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class UpdateTrickFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $cat = $options['reorderedCategory'];

        $builder
            ->add('title', TextType::class, ['label' => 'Nom de la figure :'])
            ->add('content', TextareaType::class, [
                'label' => 'Description :',
                'attr'  => ['rows' => '10'],
            ])
            ->add('category', EntityType::class, [
                'class'        => Category::class,
                'label'        => 'Groupe :',
                'choice_label' => 'category',
                'choice_value' => 'id',
                'choices'      => $cat,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'        => Trick::class,
            'reorderedCategory' => false,
        ]);
    }
}
