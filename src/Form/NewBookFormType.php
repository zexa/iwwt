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
            ->add("publication_date", DateType::class)
            ->add("genres", CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,  // Allows adding new fields dynamically
                'allow_delete' => true,  // Allows removing existing fields dynamically
                'prototype' => true,  // Allows a prototype to be used for new entries
                'by_reference' => false,  // Ensure proper handling when using collections
                'label' => false, // Disables the label from being rendered automatically
            ])
            ->add('copies', NumberType::class)
        ;
    }
}