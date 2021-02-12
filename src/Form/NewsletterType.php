<?php

namespace App\Form;

use App\Entity\Newsletter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewsletterType extends AbstractType
{
    // cette méthode est activée par le controller $this->createForm
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // on définit les champs du formulaire
            ->add('nom')
            ->add('email')
//            ->add('dateInscription')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Newsletter::class,
        ]);
    }
}
