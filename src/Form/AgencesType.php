<?php

namespace App\Form;

use App\Entity\Agences;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AgencesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('latitude')
            ->add('longitude')
            ->add('address')
            ->add('Valider', SubmitType::class, [
                'attr' => [
                    'class' => 'btn  my-3 rounded-2 primary'
                ] 
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Agences::class,
        ]);
    }
}
