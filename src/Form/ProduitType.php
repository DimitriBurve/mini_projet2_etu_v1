<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


//  http://api.symfony.com/4.0/Symfony/Component/Form/Extension/Core/Type.html
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use App\Entity\TypeProduit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, ['required' => false])
            ->add('prix', NumberType::class, ['required' => false])
            ->add('photo', TextType::class,[
                'label' => 'Photo du produit',
                'required' => false
            ])
            ->add('disponible', CheckboxType::class,  ['required' => false,'mapped' => true ])
       //     ->add('stock', NumberType::class, ['required' => false])
//            ->add('typeProduitId')

            ->add('typeProduitId', EntityType::class, array(
                // query choices from this entity
                'class' => TypeProduit::class,

                // use the User.username property as the visible option string
                'choice_label' => 'libelle',

                // used to render a select box, check boxes or radios
                // 'multiple' => true,
                // 'expanded' => true,
                'required' => false,
                'placeholder' => 'Choisir un type de produit'
            ))


            ->add('submit', SubmitType::class)
            ->getForm()
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
