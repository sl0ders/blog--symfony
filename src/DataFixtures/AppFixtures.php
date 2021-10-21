<?php

namespace App\DataFixtures;

use App\Entity\Chapter;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $comments = [];
        $users = [];
        $faker = Factory::create("fr_FR");
        $userAdmin = new User();
        $userAdmin
            ->setUsername("Sl0ders")
            ->setEmail("Sl0ders@gmail.com")
            ->setFirstname("Quentin")
            ->setLastname("Sommesous")
            ->setPassword($this->encoder->encodePassword($userAdmin, "258790"))
            ->setRoles(["ROLE_ADMIN", "ROLE_USER", "ROLE_SUPER_ADMIN"])
            ->setEnabled(true);
        $manager->persist($userAdmin);
        for ($u = 1; $u <= 10; $u++) {
            $user = new User();
            $user
                ->setUsername($faker->userName)
                ->setEmail($faker->email)
                ->setFirstname($faker->firstName)
                ->setLastname($faker->lastName)
                ->setPassword($this->encoder->encodePassword($userAdmin, "password"))
                ->setRoles(["ROLE_USER"])
                ->setEnabled(true);
            $manager->persist($user);
            array_push($users, $user);
        }

        for ($chap = 1; $chap <= 10; $chap++) {
            $chapter = new Chapter();
            $chapter
                ->setTitle($faker->sentence(2, true))
                ->setCreatedAt($faker->dateTimeBetween('-10 month', 'now', 'UTC'))
                ->setNumber($chap)
                ->setEnabled($faker->boolean());
            $manager->persist($chapter);

            for ($p = 1; $p <= mt_rand(2, 5); $p++) {
                $post = new Post();
                $post->setUpdatedAt(new \DateTime());
                $post
                    ->setTitle($faker->sentence(3, true))
                    ->setContent($faker->realText(500))
                    ->setCreatedAt($faker->dateTimeBetween('-10 month', 'now', 'UTC'))
                    ->setChapter($chapter)
                    ->setNumber($p)
                    ->setEnabled(true)
                    ->setAuthor($faker->randomElement($users))
                    ->setImage($faker->image());
                $manager->persist($post);

//                for ($i = 1; $i <= 60; $i++) {
//                    $postLike = new UserLikePost();
//                    $postLike
//                        ->setCreatedAt($faker->dateTimeBetween('-10 month', 'now', 'UTC'))
//                        ->setAuthor($faker->randomElement($users))
//                        ->setPost($post);
//                }

                for ($c = 1; $c <= mt_rand(6, 10); $c++) {
                    $comment = new Comment();
                    $comment
                        ->setAuthor($faker->randomElement($users))
                        ->setCreatedAt($faker->dateTimeBetween('-10 month', 'now', 'UTC'))
                        ->setContent($faker->realText(100))
                        ->setPost($post)
                        ->setEnabled($faker->boolean());
                    $manager->persist($comment);
                    array_push($comments, $comment);
//                    for ($i = 1; $i <= 50; $i++) {
//                        $commentLike = new UserLikeComment();
//                        $commentLike->setCreatedAt($faker->dateTimeBetween('-10 month', 'now', 'UTC'))
//                            ->setUser($faker->randomElement($users))
//                            ->setComment($comment);
//                        $manager->persist($commentLike);
//                    }
                }
            }
        }
        $manager->flush();
    }
}
