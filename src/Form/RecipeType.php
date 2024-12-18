<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Recipe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\AsciiSlugger;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'empty_data' => ''
            ])
            ->add('slug', TextType::class, [
                'required' => false,
                'empty_data' => ''
            ])
            ->add('content', TextareaType::class, [
                'empty_data' => ''
            ])
            ->add('duration', IntegerType::class, [
                'label' => 'Durée en minutes'
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name'
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, $this->autoSlug(...))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->attachTimestamps(...))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
            'validation_groups' => [Recipe::VALIDATION_GROUP_NEW, Recipe::VALIDATION_GROUP_EDIT],
        ]);
    }

    private function autoSlug(PostSubmitEvent $event): void
    {
        $data = $event->getData();

        if (!$event->getForm()->isValid()) {
            return;
        }

        if (!($data instanceof Recipe)) {
            return;
        }

        if ($data->getSlug() === '') {
            $slugger = new AsciiSlugger();
            $data->setSlug($slugger->slug($data->getTitle())->lower());
        }
    }

    private function attachTimestamps(PostSubmitEvent $event): void
    {
        $data = $event->getData();

        if (!$event->getForm()->isValid()) {
            return;
        }

        if (!($data instanceof Recipe)) {
            return;
        }

        $data->setUpdatedAt(new \DateTimeImmutable());
        if (!$data->getId()){
            $data->setCreatedAt(new \DateTimeImmutable());
        }
    }
}
