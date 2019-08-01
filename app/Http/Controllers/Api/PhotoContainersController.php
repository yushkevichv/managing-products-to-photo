<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ContainerResource;
use App\Models\Container;
use App\Models\Product;
use App\Repositories\PhotoContainerRepository;
use App\Http\Controllers\Controller;
use App\Services\UCSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PhotoContainersController extends Controller
{
    protected $photoContainerRepository;
    protected $UCSService;

    public function __construct(PhotoContainerRepository $photoContainerRepository, UCSService $UCSService)
    {
        $this->photoContainerRepository = $photoContainerRepository;
        $this->UCSService = $UCSService;
    }

    public function getOptimContainers()
    {
        $data = $this->photoContainerRepository->getContainersAndProductTypes();
        if($data->count() == 0) {
            return response()->json(['data' => [], 'count' => 0], 200);
        }

        $minContainers = $this->UCSService->getMinContainers($data);

        return response()->json(['data' => $minContainers, 'count' => count($minContainers)], 200);
    }

    public function index(Request $request)
    {
        $containers = Container::with('products')->get();

        return ContainerResource::collection($containers);
    }

    public function show(Request $request, $id)
    {
        $container = Container::find($id);
        if(!$container) {
            return response()->json(['error' => 'Container not found'], 404);
        }

        return new ContainerResource($container) ;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|required',
            'products' => 'array|required|between:'.Container::PRODUCT_COUNT_AT_CONTAINER.', '.Container::PRODUCT_COUNT_AT_CONTAINER,
            'products.*.id' => 'required|exists:products',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->toJson()], 422);
        }

        $container = new Container();
        $container->name = $request->name;
        $productIds = array_column($request->products, 'id');
        $productTypes = Product::select('type_id')->distinct()->whereIn('id', $productIds)->get();


        DB::transaction(function () use ($container, $productIds, $productTypes) {
            $container->save();
            $container->products()->attach($productIds);
            $container->product_types()->attach($productTypes->pluck('type_id'));
        });





        return response()->json(['data' => 'Successful created'], 201);
    }
}
