<?php

namespace App\Form;

use App\Entity\Actualite;
use App\Entity\Commentaire;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contenue', TextareaType::class, [
                'attr' => [
                    'class' => 'contenue-comment',
                ],
            ])
            // ->add('poster_le', DateTimeType::class, [
            //     'widget' => 'single_text',
            // ])
            // ->add('utilisateur', EntityType::class, [
            //     'class' => Utilisateur::class,
            // ])
            // ->add('actualite', EntityType::class, [
            //     'class' => Actualite::class,
            // ])
            ->add('envoyer', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commentaire::class,
        ]);
    }
}
