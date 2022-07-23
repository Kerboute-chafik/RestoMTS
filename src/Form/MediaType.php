<?php

namespace App\Form;

use App\Entity\Media;
use App\Entity\Restaurant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaType extends Form
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filename',FileType::class, array('data_class' => null))
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
            'data_class' => Media::class,
        ]);
    }
}
