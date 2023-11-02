<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use App\Repository\BookRepository; 
use App\Entity\Book;
use Symfony\Component\HttpFoundation\Request;
use App\Form\BookType;

use Doctrine\ORM\EntityManagerInterface;
class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }


    #[Route('/addbook', name: 'addbook')]
    public function add(ManagerRegistry $mr, BookRepository $repo, Request $req): Response
    {
        $b = new Book();

        $form = $this->createForm(BookType::class, $b);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $mr->getManager();
            $em->persist($b);

            // Assuming you have a way to retrieve the selected author, e.g., from the form
            $author = $form->get('author')->getData();

            // Update the author's nb_books attribute
            $author->setNbBooks($author->getNbBooks() + 1);

            $em->persist($author);
            $em->flush();
            return $this->redirectToRoute('fetchbooks');
        }

        return $this->render('book/addbook.html.twig', [
            'f' => $form->createView()
        ]);
    }
   

    #[Route('/fetchbooks', name: 'fetchbooks')]
    public function fetchbooks(BookRepository $repo):Response
    {
       $books=$repo->findAll();
       return $this->render('book/affi.html.twig',[
           'books' => $books,
       ]);
    }

    #[Route('/fetchbooks1', name: 'fetchbooks1')]
    public function fetchbooks1(BookRepository $repo):Response
    {
        $books = $repo->findBy(['published' => true]);

        return $this->render('book/affiche2.html.twig', [
            'books' => $books,
            
       ]);
    }


    #[Route('/fetchbooks2', name: 'fetchbooks2')]
    public function fetchbooks2(BookRepository $repo): Response
{
    // Récupérez tous les livres publiés depuis le référentiel (repository)
    $books = $repo->findBy(['published' => true]);
    
    // Initialisez des compteurs pour les livres publiés et non publiés
    $publishedCount = count($books);
    $unpublishedCount = 0; // Il n'y a pas de livres non publiés puisque nous filtrons par 'published = true'
    
    return $this->render('book/affiche1.html.twig', [
        'publishedCount' => $publishedCount,
        'unpublishedCount' => $unpublishedCount,
        'books' => $books,
    ]);
}

#[Route('/fetchbooks3', name: 'fetchbooks3')]
public function fetchbooks3(BookRepository $repo): Response
{
    // Récupérez tous les livres publiés depuis le référentiel (repository)
    $books = $repo->findBy(['published' => true]);

    // Initialize counters for the books
    $publishedCount = count($books);
    $unpublishedCount = 0; // There are no unpublished books since we're filtering by 'published = true'

    if (empty($books)) {
        // If no books are found, render the "no_books_found" template
        return $this->render('book/no_books_found.html.twig');
    }

    // If there are books, render the "affiche1" template with the list of books
    return $this->render('book/affiche1.html.twig', [
        'books' => $books,
        'publishedCount' => $publishedCount,
        'unpublishedCount' => $unpublishedCount,
    ]);
}

#[Route('/book/edit/{ref}', name: 'edit_book')]
public function edit(BookRepository $repo, ManagerRegistry $mr, Request $request, int $ref): Response
{
    $book = $repo->find($ref);
    $form = $this->createForm(BookType::class, $book);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em = $mr->getManager();
        $em->persist($book);
        $em->flush();

        return $this->redirectToRoute('fetchbooks2');
    }

    return $this->render('book/editBook.html.twig', [
        'f' => $form->createView(),
    ]);
}

#[Route('/delete/{ref}', name: 'delete')]
public function delete(BookRepository $repo,$ref,ManagerRegistry $mr): Response
{
    $book=$repo->find($ref);
    $em=$mr->getManager();
    $em->remove($book);
    $em->flush();

    return new Response('Book est supprimer');
}

#[Route('/book/{ref}', name: 'show_book')]
public function show(BookRepository $bookRepository, $ref): Response
{
    // Récupérez le livre à partir du référentiel en fonction du "ref"
    $book = $bookRepository->find($ref);

    if (!$book) {
        throw $this->createNotFoundException('Le livre n\'a pas été trouvé.');
    }

    // Affichez les détails du livre dans une vue
    return $this->render('book/show.html.twig', [
        'book' => $book,
    ]);
}

}

