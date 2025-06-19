<?php

namespace App\Controller;

use App\Dto\BookDto;
use App\Entity\Book;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/book' )]
final class BookController extends AbstractController
{
    #[Route('/all', name: 'all_book', methods: [Request::METHOD_GET])]
    public function allUsers(
        EntityManagerInterface $em,
        ): JsonResponse
    {
        
        $books = $em->getRepository(Book::class)->findAll();
    
        $booksData = array_map(function(Book $b) {

            return [
                'id'            => $b->getId(),
                'title'         => $b->getTitle(),
                'autore'        => $b->getAutore(),
                'quantity'      => $b->getQuantity(),
                'daily_price'   => $b->getDailyPrice(),
            ];

        }, $books);
                
        return $this->json([
            'message' => 'Book data',
            'path' => 'src/Controller/BookController.php',
            'books' => $booksData
        ]);
    }

    #[Route('/{id}', name: 'single_book', methods: [Request::METHOD_GET])]
    public function singleBook(
        EntityManagerInterface $em,
        #[MapEntity(mapping: [ 'id' => 'id' ])] 
        Book $book
    ): JsonResponse {

        $book = $em->getRepository(Book::class)->find($book->getId());

        if (!$book) {
            throw $this->createNotFoundException('Book not found');
        }

        return $this->json([
            'message' => 'Book data',
            'path' => 'src/Controller/BookController.php',
            'data' => [
                'id'            => $book->getId(),
                'title'         => $book->getTitle(),
                'autore'        => $book->getAutore(),
                'quantity'      => $book->getQuantity(),
                'daily_price'   => $book->getDailyPrice(),
            ]
        ]);

    }


    #[Route('', name: 'update_book', methods: [Request::METHOD_PUT])]
    public function updateBook(
        #[MapRequestPayload()] BookDto $dto,
        EntityManagerInterface $em,
    ): JsonResponse {


        foreach ($dto->books as $item) {
            $book = $em->getRepository(Book::class)->find($item->id);
            
            if (!$book) {
                throw new NotFoundHttpException("Book with ID {$item->id} not found.");
            }

            // Aggiorna la quantità
            $book->setQuantity((int) $item->quantity);
        }

        // Salva le modifiche nel DB
        $em->flush();

        return $this->json([
            'message' => 'Books updated successfully',
            'updated_books' => array_map(fn($b) => [
                'id' => $b->id,
                'quantity' => $b->quantity
            ], $dto->books)
        ]);



    }

}
