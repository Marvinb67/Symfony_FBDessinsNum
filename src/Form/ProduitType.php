<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class)
            ->add('description', TextareaType::class)
            ->add('prix', NumberType::class)
            ->add('largeur', NumberType::class)
            ->add('hauteur', NumberType::class)
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
            ])
            ->add('image', FileType::class, [
                'label' => 'Image',

                //Unmapped veut dire que le champs n'est associé à aucune entité
                'mapped' => false,

                // Rendre le champ optionel nous évite d'uploader l'image a chaque modification du produit
                'required' => false,

                // Définition des contraintes des images
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                           'image/jpeg',
                           'image/pdf',
                        ],
                        'mimeTypesMessage' => 'Veuillez choisir une image au format jpg',
                    ]),
                ],
            ])
            ->add('ajouter', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
