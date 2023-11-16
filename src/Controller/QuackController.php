<?php

namespace App\Controller;

use App\Entity\Quack;
use App\Entity\Tag;
use App\Form\QuackType;
use App\Repository\DuckRepository;
use App\Repository\QuackRepository;
use App\Service\FileUploader;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Author;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/')]
class QuackController extends AbstractController
{
    #[Route('/', name: 'app_quack_index', methods: ['GET'])]
    public function index(QuackRepository $quackRepository, DuckRepository $duckRepository): Response
    {
        $quacks = $quackRepository->findAll();
        foreach ($quacks as $quack){
            $quack->author = $duckRepository->findOneBy(['id' => $quack->getAuthorId()])->getDuckname();
        }

        return $this->render('quack/index.html.twig', [
            'quacks' => $quacks,
        ]);
    }

    #[Route('/new', name: 'app_quack_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $quack = new Quack();
        $quack->addTags(new ArrayCollection());
        $form = $this->createForm(QuackType::class, $quack);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $quack->setAuthorId($this->getUser()->getId());

            $tags = $quack->getTags();
            foreach ($tags as $tag) {
                $entityManager->persist($tag);
            }

            $image = $form->get('img')->getData();
            if ($image) {

                $imageFileName = $fileUploader->upload($image);
                $quack->setImg($imageFileName);

            }

            $entityManager->persist($quack);
            $entityManager->flush();

            return $this->redirectToRoute('app_quack_index', [], Response::HTTP_SEE_OTHER);
        }

        // Separate form for adding tags
        $tagForm = $this->createFormBuilder()
            ->add('tag', TextType::class, [
                'label' => 'Add New Tag',
                'required' => false,
            ])
            ->add('addTag', SubmitType::class, [
                'label' => 'Add Tag',
            ])
            ->getForm();

        $tagForm->handleRequest($request);

        if ($tagForm->isSubmitted() && $tagForm->isValid()) {
            $newTag = $tagForm->get('tag')->getData();

            // Handle the new tag (e.g., create Tag entity and associate with Quack)
            $tag = new Tag();
            $tag->setName($newTag);

            // Add the tag to the Quack entity
            $quack->addTag($tag);

            $entityManager->persist($tag);
            $entityManager->flush();

            // Redirect back to the new quack page or modify as needed
            return $this->redirectToRoute('app_quack_new');
        }

        return $this->render('quack/new.html.twig', [
            'quack' => $quack,
            'form' => $form->createView(),
            'tagForm' => $tagForm->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_quack_show', methods: ['GET'])]
    public function show(Quack $quack): Response
    {
        return $this->render('quack/show.html.twig', [
            'quack' => $quack,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_quack_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Quack $quack, EntityManagerInterface $entityManager): Response
    {
        $quack->setImg(
            new File($this->getParameter('images_directory').'/'.$quack->getImg())
        );

        $form = $this->createForm(QuackType::class, $quack);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();

            return $this->redirectToRoute('app_quack_index', [], Response::HTTP_SEE_OTHER);
        }

        // Separate form for adding tags
        $tagForm = $this->createFormBuilder()
            ->add('tag', TextType::class, [
                'label' => 'Add New Tag',
                'required' => false,
            ])
            ->add('addTag', SubmitType::class, [
                'label' => 'Add Tag',
            ])
            ->getForm();

        $tagForm->handleRequest($request);

        if ($tagForm->isSubmitted() && $tagForm->isValid()) {
            $newTag = $tagForm->get('tag')->getData();

            // Handle the new tag (e.g., create Tag entity and associate with Quack)
            $tag = new Tag();
            $tag->setName($newTag);

            // Add the tag to the Quack entity
            $quack->addTag($tag);

            $entityManager->persist($tag);
            $entityManager->flush();

            // Redirect back to the new quack page or modify as needed
            return $this->redirectToRoute('app_quack_new', ['id' => $quack->getId()]);
        }

        return $this->render('quack/new.html.twig', [
            'quack' => $quack,
            'form' => $form->createView(),
            'tagForm' => $tagForm->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_quack_delete', methods: ['POST'])]
    public function delete(Request $request, Quack $quack, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$quack->getId(), $request->request->get('_token'))) {
            $entityManager->remove($quack);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_quack_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/comment', name: 'app_quack_new_comment', methods: ['GET', 'POST'])]
    public function newComment(Request $request, Quack $quack, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $quackComment = new Quack();
        $quackComment->addTags(new ArrayCollection());
        $form = $this->createForm(QuackType::class, $quackComment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $quackComment->setAuthorId($this->getUser()->getId());
//            $quackComment->setQuack($quack->getId());
            $quack->addComment($quackComment);

            $image = $form->get('img')->getData();
            if ($image) {

                $imageFileName = $fileUploader->upload($image);
                $quackComment->setImg($imageFileName);

            }

            $tags = $quackComment->getTags();
            foreach ($tags as $tag) {
                $entityManager->persist($tag);
            }

            $entityManager->persist($quackComment);
            $entityManager->flush();

            return $this->redirectToRoute('app_quack_index', [], Response::HTTP_SEE_OTHER);
        }

        // Separate form for adding tags
        $tagForm = $this->createFormBuilder()
            ->add('tag', TextType::class, [
                'label' => 'Add New Tag',
                'required' => false,
            ])
            ->add('addTag', SubmitType::class, [
                'label' => 'Add Tag',
            ])
            ->getForm();

        $tagForm->handleRequest($request);

        if ($tagForm->isSubmitted() && $tagForm->isValid()) {
            $newTag = $tagForm->get('tag')->getData();

            // Handle the new tag (e.g., create Tag entity and associate with Quack)
            $tag = new Tag();
            $tag->setName($newTag);

            // Add the tag to the Quack entity
            $quackComment->addTag($tag);

            $entityManager->persist($tag);
            $entityManager->flush();

            // Redirect back to the new quack page or modify as needed
            return $this->redirectToRoute('app_quack_new');
        }

        return $this->render('quack/new.html.twig', [
            'quack' => $quackComment,
            'form' => $form->createView(),
            'tagForm' => $tagForm->createView(),
        ]);
    }

}
