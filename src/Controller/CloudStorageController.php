<?php

namespace App\Controller;

use App\Service\GoogleCloudStorageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;

use function Symfony\Component\Clock\now;

final class CloudStorageController extends AbstractController
{
    #[Route('/storage/getSignedUrls', name: 'app_cloud_storage')]
    public function index(
        GoogleCloudStorageService $googleCloudStorageService,
        Request $request,
        #[MapQueryParameter] string $folderName,
        #[MapQueryParameter] ?string $fileExtension = 'jpg',
        ): JsonResponse
    {
        $files = $googleCloudStorageService->listFilesWithExtension($folderName, $fileExtension);
        $urls = [];

        $urlExpiration = now('+1 hours');

        foreach ($files as $file) {
            $urls[] = $googleCloudStorageService->getSignedUrl($file, $urlExpiration);
        }

        $formattedUrls = array_map(
            fn($url) => ['url' => $url],
            $urls
        );

        return $this->json($formattedUrls);
    }

}
