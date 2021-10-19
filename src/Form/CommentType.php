<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                "label" => " ",
                "attr" => ["class" => "form-control", "placeholder" => "post.form.label.commentContent", "cols" => 9, "rows" => 5]
            ])
            ->add("submit", SubmitType::class, [
                "label" => "global.text.button.save",
                "attr" => ["class" => "form-control mt-2 mb-5 btn btn-primary"]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
            'translation_domain' => "BlogTrans"
        ]);
    }
}
