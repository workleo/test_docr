<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


use AppBundle\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;


class DefaultController extends Controller
{
    //
    //   @Route("/", name="homepage")


    /*
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }
    */


    /**
     * @Route("/index", name="index")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */

    public function createAction(EntityManagerInterface $em)
    {
        // or fetch the em via the container
        // $em = $this->get('doctrine')->getManager();

        /**
         * @var $product Product
         */
        $product = new Product();
        $product->setName('Keyboard');
        $product->setPrice(19.99);
        $product->setDescription('Ergonomic and stylish!');

        // tells Doctrine you want to (eventually) save the Product (no queries yet)
        $em->persist($product);

        // actually executes the queries (i.e. the INSERT query)
        $em->flush();

        //$response = new Response();
        //$response->setStatusCode(200);
        $content = 'Saved new product with id ' . $product->getId() . '<br>';

        $productId = 0;
        do {
            $productId++;
            $product = $em->getRepository('AppBundle:Product')->find($productId);
            if (!$product) {
                //throw $this->createNotFoundException('No product found for id ' . $productId);
                break;
            }
            $content .= $product->getId() . ' ' .
                $product->getName() . ' ' . $product->getPrice() . ' ' . $product->getDescription() . '<br>';
        } while (true);


        return new Response($content);
    }

    // if you have multiple entity managers, use the registry to fetch them
    public function editAction(ManagerRegistry $doctrine)
    {
        $em = $doctrine->getManager();
        // $em2 = $doctrine->getManager('other_connection')
    }
}
