<?php

namespace App\Controller;
use App\Repository\AuthorRepository;
use App\Entity\Author;
use Symfony\Component\HttpFoundation\Request;
use App\Form\AuthorType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use Doctrine\ORM\EntityManagerInterface;

class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    #[Route('/fetchAuthors', name: 'fetchAuthors')]
    public function fetchAuthors(AuthorRepository $repo):Response
    {
        $result=$repo->findAll();
        return $this->render('author/afficheAuthors.html.twig',[
            'response' => $result,
        ]);
    }

    #[Route('/addAuthor', name: 'addAuthor')]
    public function add1(AuthorRepository $repo,ManagerRegistry $mr): Response
    {
        $s=new Author();
        $s->setUsername('test');
        $s->setEmail('test@gmail.com');
        $em=$mr->getManager();
        $em->persist($s);
        $em->flush();
        return $this->redirectToRoute('fetchAuthors');
    }

    #[Route('/add', name: 'add')]
    public function add(ManagerRegistry $mr,AuthorRepository $repo,Request $req):Response
    {
        $s=new Author(); //instance

        $form=$this->createForm(AuthorType::class,$s); // creation formulaire et binding
        $form->handleRequest($req);
        if ($form->isSubmitted()){

            $em=$mr->getManager();
            $em->persist($s);
            $em->flush();
            return $this->redirectToRoute('fetchAuthors');
        }

        return $this->render('author/addauthor.html.twig',[
            'f'=>$form->createView()
        ]);
    }


    #[Route('/delete/{ref}', name: 'delete')]
    public function delete(StudentRepository $repo, $ref, ManagerRegistry $mr): Response
    {
        $author = $repo->find($ref);
    
        if (!$author) {
            return new Response('Student not found', 404);
        }
    
        $em = $mr->getManager();
        $em->remove($author);
        $em->flush();
    
        return new Response('Removed');
    }


    #[Route('/author/edit/{id}', name: 'edit_author')]
    public function edit(AuthorRepository $repo, ManagerRegistry $mr,Request $request, int $id): Response
    {
        $author = $repo->find($id);
        $form = $this->createForm(AuthorType::class, $author); 
        $form->handleRequest($request);

        if ($form->isSubmitted())
        {
            $em = $mr->getManager();
            $em->persist($author);
            $em->flush();

            return $this->redirectToRoute('fetchAuthors'); 
        }

        return $this->render('author/editAuthor.html.twig', [
            'f' => $form->createView(),
        ]);
    }

}
