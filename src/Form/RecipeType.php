<?php

namespace App\Form;

use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\AsciiSlugger;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('content')
            ->add('duration')
            ->addEventListener(FormEvents::POST_SUBMIT, $this->autoSlug(...))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->attachTimestamps(...))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }

    private function autoSlug(PostSubmitEvent $event): void
    {
        $data = $event->getData();

        if (!($data instanceof Recipe)) {
            return;
        }

        if ($data->getSlug() === null) {
            $slugger = new AsciiSlugger();
            $data->setSlug($slugger->slug($data->getTitle())->lower());
        }
    }

    private function attachTimestamps(PostSubmitEvent $event): void
    {
        $data = $event->getData();

        if (!($data instanceof Recipe)) {
            return;
        }

        $data->setUpdatedAt(new \DateTimeImmutable());
        if (!$data->getId()){
            $data->setCreatedAt(new \DateTimeImmutable());
        }
    }
}
