<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("firstname", TextType::class, [
                "label" => " ",
                "attr" => ["class" => "form-control", "placeholder" => "user.form.label.firstname"]
            ])
            ->add("lastname", TextType::class, [
                "label" => " ",
                "attr" => ["class" => "form-control", "placeholder" => "user.form.label.lastname"]
            ])
            ->add("username", TextType::class, [
                "label" => " ",
                "attr" => ["class" => "form-control", "placeholder" => "user.form.label.username"]
            ])
            ->add("email", EmailType::class, [
                "label" => " ",
                "attr" => ["class" => "form-control", "placeholder" => "user.form.label.email"]
            ])
            ->add("password", RepeatedType::class, [
                "type" => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne coïncide pas',
                'options' => ['attr' => ['class' => 'password-field']],
                'first_options' => ['label' => ' ', "attr" => ["class" => "form-control", "placeholder" => "user.form.label.password"]],
                'second_options' => ['label' => ' ', "attr" => ["class" => "form-control", "placeholder" => "user.form.label.repeatPassword"]],
            ])
        ->add("submit", SubmitType::class, [
            "label" => "global.text.button.save",
            "attr" => ["class" => "btn btn-success mt-3", "placeholder" => "user.form.label.birthday"]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "translation_domain" => "BlogTrans",
            'data_class' => User::class,
        ]);
    }
}
