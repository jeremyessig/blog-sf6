<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Repository\PostsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(PostsRepository $postsRepository): Response
    {

        $posts = $postsRepository->findLatest();

        //dd($posts);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'posts' => $posts
        ]);
    }

    #[Route('/post/{slug}', methods: ['GET'], name: 'app_show_post')]
    public function postShow(Posts $post, string $slug): Response
    {

        if ($post->getSlug() !== $slug) {
            //Si on est sur le bien et que le slug est modifié, on est renvoyé sur le bien avec le slug corrigé
            //Très important pour le référencement
            return $this->redirectToRoute('app_show_post', [
                'id' => $post->getId(),
                'slug' => $post->getSlug()
            ], 301);
        }
        return $this->render('blog/post_show.html.twig', ['post' => $post]);
    }
}
