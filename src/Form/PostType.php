<?php

namespace App\Form;

use App\Entity\Chapter;
use App\Entity\Post;
use Liip\ImagineBundle\Form\Type\ImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                "label" => " ",
                "attr" => ["class" => "form-control", "placeholder" => "post.form.label.title"]
            ])
            ->add('content', TextareaType::class, [
                "label" => " ",
                "attr" => ["class" => "form-control", "cols" => 12, "rows" => 12, "placeholder" => "post.form.label.content"]
            ])
            ->add('created_at', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                "label" => "post.form.label.createdAt",
                "attr" => ["class" => "form-control mt-2"]
            ])
            ->add("imageFile", VichImageType::class, [
                "label" => "post.form.label.picture",
                'required' => false,
                'allow_delete' => true,
                "attr" => ["class" => "form-control mt-2"]
            ])
            ->add('enabled', CheckboxType::class, [
                "label" => "post.form.label.enabledQuestion",
                "attr" => ["class" => "my-2 w-25 mt-2"]
            ])
            ->add('chapter', EntityType::class, [
                "class" => Chapter::class,
                "choice_label" => "chapterIdentity",
                "label" => "post.form.label.chapterSelect",
                "attr" => ["class" => "select2 form-control mt-2"]
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
