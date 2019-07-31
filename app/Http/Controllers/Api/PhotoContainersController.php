<?php

namespace App\Http\Controllers\Api;

use App\Repositories\PhotoContainerRepository;
use App\Http\Controllers\Controller;
use App\Services\UCSService;

class PhotoContainersController extends Controller
{
    protected $photoContainerRepository;
    protected $UCSService;

    public function __construct(PhotoContainerRepository $photoContainerRepository, UCSService $UCSService)
    {
        $this->photoContainerRepository = $photoContainerRepository;
        $this->UCSService = $UCSService;
    }

    public function index()
    {
        $data = $this->photoContainerRepository->getContainersAndProductTypes();
        $minContainers = $this->UCSService->getMinContainers($data);

        return response()->json(['data' => $minContainers, 'count' => count($minContainers)], 200);
    }
}
