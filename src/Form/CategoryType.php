<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\AsciiSlugger;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'empty_data' => '',
            ])
            ->add('slug', TextType::class, [
                'required' => false,
                'empty_data' => '',
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, $this->autoSlug(...))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->attachTimestamps(...))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }


    private function autoSlug(PostSubmitEvent $event): void
    {
        $data = $event->getData();

        if (!$event->getForm()->isValid()) {
            return;
        }

        if (!($data instanceof Category)) {
            return;
        }

        if ($data->getSlug() === '') {
            $slugger = new AsciiSlugger();
            $data->setSlug($slugger->slug($data->getName())->lower());
        }
    }

    private function attachTimestamps(PostSubmitEvent $event): void
    {
        $data = $event->getData();

        if (!$event->getForm()->isValid()) {
            return;
        }

        if (!($data instanceof Category)) {
            return;
        }

        $data->setUpdatedAt(new \DateTimeImmutable());
        if (!$data->getId()){
            $data->setCreatedAt(new \DateTimeImmutable());
        }
    }
}
