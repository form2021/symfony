<?php

namespace App\Form;

use App\Entity\Annonce;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
// ne pas oublier d'ajouter les use
use Symfony\Component\Form\Extension\Core\Type\FileType;
// https://symfony.com/doc/current/reference/constraints/Image.html
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Categorie;


class AnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre')
            ->add('slug')
            // https://symfony.com/doc/current/reference/forms/types/entity.html#select-tag-checkboxes-or-radio-buttons
            ->add('categories', EntityType::class, [
                // looks for choices from this entity
                'class' => Categorie::class,
            
                // uses the User.username property as the visible option string
                'choice_label' => 'label',
            
                // used to render a select box, check boxes or radios
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('contenu')
            ->add('image', FileType::class, [
                'label' => 'choisissez une photo à uploader',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new Image([
                        'maxSize' => '10240k',
                        // on peut ajouter des contraintes sur les tailles en pixels...
                    ])
                ],
            ])
            // ->add('datePublication')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Annonce::class,
        ]);
    }
}
