<?php
// src/Controller/ProductController.php
namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/products', name: 'blog_list')]
    public function index(Request $request, PaginatorInterface $paginator) : Response
    {
        // Get the repository for the Product entity
        $productRepository = $this->entityManager->getRepository(Product::class);

        // Create a query to select all products
        $query = $productRepository->createQueryBuilder('p')->getQuery();

        // Paginate the query
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), // Get the current page from the request, default to 1
            10 // Number of items per page
        );
        // dd($pagination);
        return $this->render('product/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}