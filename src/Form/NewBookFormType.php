<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class NewBookFormType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("title", TextType::class)
            ->add("author", TextType::class)
            ->add("isbn", TextType::class)
            ->add("publicationDate", DateType::class)
            ->add("genres", CollectionType::class, [
                'entry_type' => TextType::class,
            ])
            ->add('copies', NumberType::class)
        ;
    }
}