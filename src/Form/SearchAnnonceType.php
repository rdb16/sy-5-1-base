<?php

namespace App\Form;

use App\Entity\Categories;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SearchAnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mots', SearchType::class, [
                'label' => false,
                'attr' => [
                    'class' =>'form-control',
                    'placeholder' => 'Entrez vos mots clés'
                ],
                'required' => false
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categories::class,
                'label' => false,
                'attr' => [
                    'class' =>'form-control',
                ],
                'required' => false
            ])
            ->add('Rechercher', SubmitType::class, [
                'attr' => [
                    'class' =>'btn primary rounded-3'
                ]
            ] )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
