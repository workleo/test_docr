<?php

namespace AppBundle\Controller;

use AppBundle\Form\CommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use AppBundle\Entity\Product;
use AppBundle\Form\ProductType;

class ProductController extends Controller
{
    /**
     * @Route("/product", name="product_index")
     * @Route("/product", name="product")
     */
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('AppBundle:Product')->findAll();

        return $this->render('table/product.html.twig', ['products' => $products]);
    }



    /**
     * Deletes a Product entity.
     *
     * @Route("/{id}/delete", name="product_delete")
     * @Method("POST")test_dcr
     *
     */
    public function deleteAction(Request $request, Product $product)
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('product_index');
        }

        // Delete the tags associated with this blog post. This is done automatically
        // by Doctrine, except for SQLite (the database used in this application)
        // because foreign key support is not enabled by default in SQLite

        $product->getTags()->clear();

        $em = $this->getDoctrine()->getManager();
        $em->remove($product);
        $em->flush();

        $this->addFlash('success', 'product.deleted_successfully');

        return $this->redirectToRoute('product_index');
    }


    /**
     * Creates a new Product entity.
     *
     * @Route("/new", name="product_new")
     * @Method({"GET", "POST"})
     *
     * NOTE: the Method annotation is optional, but it's a recommended practice
     * to constraint the HTTP methods each controller responds to (by default
     * it responds to all methods).
     */
    public function newAction(Request $request)
    {
        $product = new Product();


        // See https://symfony.com/doc/current/book/forms.html#submitting-forms-with-multiple-buttons
        $form = $this->createForm(ProductType::class, $product)
            ->add('saveAndCreateNew', SubmitType::class);

        $form->handleRequest($request);

        // the isSubmitted() method is completely optional because the other
        // isValid() method already checks whether the form is submitted.
        // However, we explicitly add it to improve code readability.
        // See https://symfony.com/doc/current/best_practices/forms.html#handling-form-submits
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            // Flash messages are used to notify the user about the result of the
            // actions. They are deleted automatically from the session as soon
            // as they are accessed.
            // See https://symfony.com/doc/current/book/controller.html#flash-messages
            $this->addFlash('success', 'post.created_successfully');

            if ($form->get('saveAndCreateNew')->isClicked()) {
                return $this->redirectToRoute('product_new');
            }

            return $this->redirectToRoute('product_index');
        }

        return $this->render('table/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }


    /**
     * Finds and displays a Product entity.
     *
     * @Route("/{id}", requirements={"id": "\d+"}, name="product_show")
     * @Method("GET")
     */
    public function showAction(Product $product)
    {
        return $this->render('table/show.html.twig', [
            'product' => $product,
        ]);
    }


    /**
     * @Route("/{id}/comments_edit", requirements={"id": "\d+"}, name="comments_edit")
     * @Method({"GET", "POST"})
     * @param Product $product
     */
    public function productEditComment(Product $product)
    {
        return $this->render('table/comments_edit.html.twig', [
            'product' => $product,
        ]);
    }


    /**
     * This controller is called directly via the render() function in the
     * blog/post_show.html.twig template. That's why it's not needed to define
     * a route name for it.
     *
     * The "id" of the Post is passed in and then turned into a Post object
     * automatically by the ParamConverter.
     *
     * @param Product $product
     *
     * @return Response
     */
    public function commentFormAction(Product $product)
    {
        $form = $this->createForm(CommentType::class);

        return $this->render('table/_comment_form.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }


    /**
     * Displays a form to edit an existing Product entity.
     *
     * @Route("/{id}/edit", requirements={"id": "\d+"}, name="product_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Product $product)
    {

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'product.updated_successfully');

            return $this->redirectToRoute('product_edit', ['id' => $product->getId()]);
        }

        return $this->render('table/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

}