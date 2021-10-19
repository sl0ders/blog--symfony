<?php

namespace App\Form;

use App\Entity\Chapter;
use App\Entity\Post;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                "label" => " ",
                "attr" => ["class" => "form-control mb-2", "placeholder" => "post.form.label.title"]
            ])
            ->add('content', TextareaType::class, [
                "label" => " ",
                "attr" => ["class" => "form-control mb-2", "cols" => 12, "rows" => 12, "placeholder" => "post.form.label.content"]
            ])
            ->add('created_at', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                "label" => "post.form.label.createdAt",
                "attr" => ["class" => "form-control mb-2"]
            ])
            ->add('enabled', CheckboxType::class, [
                "label" => "post.form.label.enabledQuestion",
                "attr" => ["class" => "mb-2"]
            ])
            ->add('chapter', EntityType::class, [
                "class" => Chapter::class,
                "choice_label" => "chapterIdentity",
                "label" => "post.form.label.chapterSelect",
                "attr" => ["class" => "select2 form-control mb-2"]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
            "translation_domain" => "BlogTrans"
        ]);
    }
}
