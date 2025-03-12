<?php

namespace App\Form;

use App\Entity\UserContact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'empty_data' => '',
            ])
            ->add('name', TextType::class, [
                'empty_data' => '',
            ])
            ->add('message', TextareaType::class, [
                'empty_data' => '',
            ])
            ->add('service', ChoiceType::class, [
                'choices'  => [
                    'Digital' => 'digital@email.com',
                    'DSI' => 'dsi@email.com',
                    'Communication' => 'communication@email.com',
                    'RH' => 'rh@email.com',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserContact::class,
        ]);
    }
}
