<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Form\MediaType;
use App\Repository\MediaRepository;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/media")
 */
class MediaController extends AbstractController
{

    private $em;
    private $uploader;

    public function __construct(EntityManagerInterface $em,UploaderHelper $uploader)
    {
        $this->em = $em;
        $this->uploader = $uploader;

    }
    /**
     * @Route("/", name="app_media_index", methods={"GET"})
     */
    public function index(MediaRepository $mediaRepository): Response
    {
        return $this->render('media/index.html.twig', [
            'media' => $mediaRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_media_new", methods={"GET", "POST"})
     */
    public function new(Request $request, MediaRepository $mediaRepository): Response
    {
        $media = new Media();
        $form = $this->createForm(MediaType::class, $media);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $pathImage = "/";
            $imageFile = $request->files->get('media')['filename'];
            if ($imageFile) {
                $imageName = $this->uploader->uploadFile($imageFile, $pathImage);
                $media->setFileName('uploads' . $imageName);
            }

            $this->em->persist($media);
            $this->em->flush();

            return $this->redirectToRoute('app_media_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('media/new.html.twig', [
            'medium' => $media,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_media_show", methods={"GET"})
     */
    public function show(Media $medium): Response
    {
        return $this->render('media/index.html.twig', [
            'medium' => $medium,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_media_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Media $media, MediaRepository $mediaRepository): Response
    {
        $form = $this->createForm(MediaType::class, $media);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pathImage = "/";
            $imageFile = $request->files->get('media')['filename'];
            if ($imageFile) {
                $imageName = $this->uploader->uploadFile($imageFile, $pathImage);
                $media->setFileName('uploads' . $imageName);
            }
            $this->em->persist($media);
            $this->em->flush();

            return $this->redirectToRoute('app_media_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('media/edit.html.twig', [
            'medium' => $media,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_media_delete", methods={"POST"})
     */
    public function delete(Request $request, Media $medium, MediaRepository $mediaRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$medium->getId(), $request->request->get('_token'))) {
            $mediaRepository->remove($medium, true);
        }

        return $this->redirectToRoute('app_media_index', [], Response::HTTP_SEE_OTHER);
    }
}
