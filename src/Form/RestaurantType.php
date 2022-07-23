<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Restaurant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RestaurantType extends Form
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class,
                [
                    'label' => "Name",
                    'attr' => array_merge(
                        $this->inputAttr,
                        [
                            //        'placeholder' => 'Nom',
                        ]
                    ),
                ])
            ->add('description',TextType::class,
                [
                    'label' => "Description",
                    'attr' => array_merge(
                        $this->inputAttr,
                        [
                            //        'placeholder' => 'Nom',
                        ]
                    ),
                ])
            ->add('city', EntityType::class,
                [
                    'class' => City::class,
                    'choice_label' => 'name',
                    'multiple' => false,
                    'label' => 'City',
                    'required' => true,
                    'attr' => array_merge(
                        $this->selectAttr,
                        [
                            //'placeholder' => 'Type',
                        ]
                    ),
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Restaurant::class,
        ]);
    }
}
