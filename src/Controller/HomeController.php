<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Posts;
use App\Form\CommentsType;
use App\Repository\PostsRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/post/{slug}', methods: ['GET', 'POST'], name: 'app_show_post')]
    public function postShow(Posts $post, string $slug, Request $request, ManagerRegistry $doctrine): Response
    {

        if ($post->getSlug() !== $slug) {
            //Si on est sur le bien et que le slug est modifié, on est renvoyé sur le bien avec le slug corrigé
            //Très important pour le référencement
            return $this->redirectToRoute('app_show_post', [
                'id' => $post->getId(),
                'slug' => $post->getSlug()
            ], 301);
        }

        $comment = new Comments();
        // $comment->setPosts($post)->setUsers($this->getUser())->setContent("Hello World");
        // $entityManager = $doctrine->getManager();
        // $entityManager->persist($comment);
        // $entityManager->flush();

        //dd($post);

        $form = $this->createForm(CommentsType::class, $comment);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // On persiste la nouvelle property en base de données
            $comment->setPosts($post)->setUsers($this->getUser());

            $entityManager = $doctrine->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();


            $this->addFlash('success', 'Bien créé avec succès');
        }

        return $this->render('blog/post_show.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }
}
