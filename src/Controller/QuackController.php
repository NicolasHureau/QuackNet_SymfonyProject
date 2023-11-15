<?php

namespace App\Controller;

use App\Entity\Quack;
use App\Form\QuackType;
use App\Repository\DuckRepository;
use App\Repository\QuackRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Author;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        $form = $this->createForm(QuackType::class, $quack);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $quack->setAuthorId($this->getUser()->getId());

            $image = $form->get('img')->getData();
            if ($image) {
//                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
//                // this is needed to safely include the file name as part of the URL
//                $safeFilename = $slugger->slug($originalFilename);
//                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();
//
//                // Move the file to the directory where brochures are stored
//                try {
//                    $image->move(
//                        $this->getParameter('images_directory'),
//                        $newFilename
//                    );
//                } catch (FileException $e) {
//                    // ... handle exception if something happens during file upload
//
//                }
//
//                // updates the 'brochureFilename' property to store the PDF file name
//                // instead of its contents
//                $quack->setimg($newFilename);

                $imageFileName = $fileUploader->upload($image);
                $quack->setImg($imageFileName);

            }

            $entityManager->persist($quack);
            $entityManager->flush();

            return $this->redirectToRoute('app_quack_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('quack/new.html.twig', [
            'quack' => $quack,
            'form' => $form,
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

        return $this->render('quack/edit.html.twig', [
            'quack' => $quack,
            'form' => $form,
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
}
