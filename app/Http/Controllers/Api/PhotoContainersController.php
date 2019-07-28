<?php

namespace App\Http\Controllers\Api;

use App\Repositories\PhotoContainerRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PhotoContainersController extends Controller
{
    protected $photoContainerRepository;

    public function __construct(PhotoContainerRepository $photoContainerRepository)
    {
        $this->photoContainerRepository = $photoContainerRepository;
    }

    public function index()
    {
        $result = $this->photoContainerRepository->getContainersWithTypedProducts();

        dd($result);
    }
}
