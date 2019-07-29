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
        $data = $this->photoContainerRepository->getContainersWithTypedProducts();
        $graph = $this->UCSService->initGraph($data);

        dd($graph, $data);
    }
}
