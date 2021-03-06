<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContainerResource;
use App\Models\Container;
use App\Models\Product;
use App\Repositories\PhotoContainerRepository;
use App\Services\ContainerInformationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PhotoContainersController extends Controller
{
    protected $photoContainerRepository;
    protected $сontainerInformationService;

    public function __construct(PhotoContainerRepository $photoContainerRepository, ContainerInformationService $сontainerInformationService)
    {
        $this->photoContainerRepository = $photoContainerRepository;
        $this->сontainerInformationService = $сontainerInformationService;
    }

    public function getOptimalContainers()
    {
        $data = $this->photoContainerRepository->getContainersAndProductTypes();
        if ($data->count() == 0) {
            return response()->json(['data' => [], 'count' => 0], 200);
        }

        $minContainers = $this->сontainerInformationService->getMinContainers($data);

        return response()->json(['data' => $minContainers, 'count' => count($minContainers)], 200);
    }

    public function index(Request $request)
    {
        $containers = Container::with('products')->paginate(100);

        return ContainerResource::collection($containers);
    }

    public function show(Request $request, $id)
    {
        $container = Container::find($id);
        if (!$container) {
            return response()->json(['error' => 'Container not found'], 404);
        }

        return new ContainerResource($container);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|required',
            'products' => 'array|required|between:'.\Config::get('app.product_count_at_container').', '.\Config::get('app.product_count_at_container'),
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

    public function destroy(Request $request, $id)
    {
        $container = Container::find($id);
        if (!$container) {
            return response()->json(['error' => 'Container not found'], 404);
        }

        $container->delete();

        return response()->json(['data' => 'Successful deleted'], 200);
    }
}
