<?php

namespace App\Http\Controllers\Api;

use App\Repositories\PhotoContainerRepository;
use Illuminate\Http\Request;
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
        $timeStart = microtime(true);
        $data = $this->photoContainerRepository->getContainersAndProductTypes();
        echo 'time work repo: '.(microtime(true) - $timeStart );
        $timeStart = microtime(true);
        $this->UCSService->initGraph($data);
        echo 'time init graph: '.(microtime(true) - $timeStart );
        $timeStart = microtime(true);
        $minContainers = $this->UCSService->getMinContainers();
        echo 'time get containers: '.(microtime(true) - $timeStart );

        return response()->json(['data' => $minContainers, 'count' => count($minContainers)], 200);
    }
}
