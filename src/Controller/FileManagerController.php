<?php

// src/Controller/FileUploadController.php

namespace App\Controller;

use App\Service\GoogleCloudStorageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

class FileManagerController extends AbstractController
{
    #[Route('/my-files', name: 'user_files')]
    public function listFiles(GoogleCloudStorageService $gcs)
    {
        $userName = $this->getUser()->getUserIdentifier();
        $files = $gcs->listUserFiles($userName);

        return $this->render('file_manager/index.html.twig', ['files' => $files]);
    }

    #[Route('/my-files/delete', name: 'delete_file', methods: ['POST'])]
    public function deleteFile(Request $request, GoogleCloudStorageService $gcs): RedirectResponse
    {
        if ($request->isMethod('POST')) {
            $token = $request->request->get('_csrf_token');
            if (!$this->isCsrfTokenValid('authenticate', $token)) {
                throw $this->createAccessDeniedException('Invalid CSRF token');
            }
        }

        $filePath = $request->request->get('file');
        $userName = $this->getUser()->getUserIdentifier();

        if (str_starts_with($filePath, "$userName/")) {
            $gcs->deleteFile($filePath);
        }

        return $this->redirectToRoute('user_files');
    }

    #[Route('/upload', name: 'file_upload', methods: ['GET', 'POST'])]
    public function upload(Request $request, GoogleCloudStorageService $gcs): Response
    {
        if ($request->isMethod('POST')) {
            $token = $request->request->get('_csrf_token');
            if (!$this->isCsrfTokenValid('authenticate', $token)) {
                throw $this->createAccessDeniedException('Invalid CSRF token');
            }
        }

        $username = $this->getUser()->getUserIdentifier();

        $allowedMimeTypes = ['image/jpeg', 'image/jpg', 'application/pdf'];
        $maxSizeInMb = 8;
        $maxSize = $maxSizeInMb * 1024 * 1024;

        if ($request->isMethod('POST') && $file = $request->files->get('file')) {
            /** @var UploadedFile $file */
            if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
                return new Response('File type not allowed.', 400);
            }

            if ($file->getSize() > $maxSize) {
                return new Response('File size exceeds ' . $maxSizeInMb, 400);
            }

            $localPath = $file->getPathname();
            $filename = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '_', $file->getClientOriginalName());

            $gcs->uploadFile($localPath, $username . '/' . $filename, false);

            return $this->redirectToRoute('user_files');
        }

        return $this->render('file_manager/upload.html.twig');
    }
}
