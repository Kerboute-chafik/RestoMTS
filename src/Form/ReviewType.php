<?php

namespace App\Form;

use App\Entity\Restaurant;
use App\Entity\Review;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewType extends Form
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('message',TextareaType::class,
                [
                    'label' => "Message",
                    'attr' => array_merge(
                        $this->inputAttr,
                        [
                            //        'placeholder' => 'Nom',
                        ]
                    )
                ])
            ->add('note', IntegerType::class,
                [
                    'label' => "Note",

                    'attr' => array_merge(
                        $this->inputAttr,
                        [
                            'min' => 1,
                            'max' => 20,
                            'placeholder' => 'Note',
                        ]
                    ),
                ])
            ->add('restaurant', EntityType::class,
                [
                    'class' => Restaurant::class,
                    'choice_label' => 'name',
                    'multiple' => false,
                    'label' => 'Restaurant',
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
            'data_class' => Review::class,
        ]);
    }
}
