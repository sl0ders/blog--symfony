<?php

namespace App\Form;

use App\Entity\Chapter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChapterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                "label" => " ",
                "attr" => ["class" => "input--style-1", "placeholder" => "chapter.form.label.title"]
            ])
            ->add('submit', SubmitType::class, [
                "label" => "global.text.button.save",
                "attr" => ["class" => "btn btn-success"]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Chapter::class,
            "translation_domain" => "BlogTrans"
            ]);
    }
}
