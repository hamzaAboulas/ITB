<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;

class ProductController extends AbstractController
{
    #[Route('/admin/add', name: 'app_product')]
    public function Add(Request $request, EntityManagerInterface $manager, RouterInterface $router): Response
    {
        if ($request->isMethod('POST')) {
            $data = $request->request->all();
            $name = $data["productname"];
            $price = $data["price"];
            $details = $data["details"];

            $uploadedFile = $request->files->get('img');
            $fileName = md5(uniqid()) . '.' . $uploadedFile->guessExtension();

            // Move the file to the directory where images are stored
            $uploadedFile->move(
                $this->getParameter('images_directory'),
                $fileName
            );

            $product = new Product();
            $product->setName($name);
            $product->setPrice($price);
            $product->setDetails(trim($details));
            $product->setImagename($fileName);
            $manager->persist($product);
            $manager->flush();

            return new RedirectResponse($router->generate('app_admin_products'));

        }



        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }

    #[Route('/admin/products', name: 'app_admin_products')]

    public function products(Request $request, EntityManagerInterface $manager, ProductRepository $repository): Response
    {
        $products = $repository->findAll();

        return $this->render('product/products.html.twig', [
            'controller_name' => 'ProductController',
            'products' => $products
        ]);
    }

    #[Route('/admin/delete/{id}', name: 'app_admin_delete')]

    public function delete(Request $request, EntityManagerInterface $manager, Product $product): RedirectResponse
    {
        $manager->remove($product);
        $manager->flush();

        $this->addFlash('success', 'Product deleted successfully.');

        return $this->redirectToRoute('app_admin_products');

    }

    #[Route('/admin/update/{id}', name: 'app_admin_update')]

    public function update(Request $request, EntityManagerInterface $manager, Product $product): Response
    {
        $data = $request->request->all();

        if ($request->isMethod('POST') && $data ) {

            $name = $data["productname"];
            $price = $data["price"];
            $details = $data["details"];

            $uploadedFile = $request->files->get('img');

            if ($uploadedFile) {
                $fileName = md5(uniqid()) . '.' . $uploadedFile->guessExtension();

                // Move the file to the directory where images are stored
                $uploadedFile->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );
                $product->setImagename($fileName);
            }
            $product->setName($name);
            $product->setPrice($price);
            $product->setDetails(trim($details));
            $manager->persist($product);
            $manager->flush();

        }

        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
            'product' => $product
        ]);


    }
}
