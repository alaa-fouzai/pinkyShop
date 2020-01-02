<?php

namespace khaledBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;



class ProductType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('image', FileType::class, [
            'label' => 'Image Produit',

            // unmapped means that this field is not associated to any entity property
            'mapped' => false,

            // make it optional so you don't have to re-upload the PDF file
            // everytime you edit the Product details
            'required' => false,

            // unmapped fields can't define their validation using annotations
            // in the associated entity, so you can use the PHP constraint classes
            'constraints' => [
                new File([
                    'maxSize' => '1024k',
                    'mimeTypes' => [
                        'image/jpg',
                        'image/png',
                        'image/jpeg',
                    ],
                    'mimeTypesMessage' => 'Please upload a valid image',
                ])
            ],
        ])
        ->add('price')->add('state')->add('title')->add('description')//->add('size')//->add('color')//->add('category')
            ->add('size', ChoiceType::class, [
                'choices'  => [
                    'Small' => "Small",
                    'XL' => "XL",
                    'Large' => "Large",
                    'Damn' => "Damn",
                    'OMFG' => "OMFG",
                ],
            ])
            ->add('color', ChoiceType::class, [
                'choices'  => [
                    'blue' => "blue",
                    'Rouge' => "Rouge",
                    'yellow' => "yellow",
                    'green' => "green",
                    'grey' => "grey",
                    'black' => "black",
                ],
            ])
            ->add('category', ChoiceType::class, [
                'choices'  => [
                    'Homme' => "Homme",
                    'Femme' => "Homme",
                    'Autre' => "Homme",
                ],
            ]);




            //->add('created_at')->add('updated_at')->add('deleted_at');
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'khaledBundle\Entity\Product'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'khaledbundle_product';
    }


}
