<?php

namespace App\Controller\Admin;

use App\Entity\Actualite;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ActualiteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Actualite::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('titre'),
            TextareaField::new('contenue'),
            ImageField::new('image')->setUploadDir('public/image/dessins'),
            DateTimeField::new('posterLe'),
        ];
    }
}
