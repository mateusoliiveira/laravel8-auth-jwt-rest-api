<?php
namespace App\Http\Controllers;

use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    protected $user;
 
    public function __construct()
    {
        try {
            $this->user = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
                return response()->json([
                'success' => false,
            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ProductRepositoryInterface $model)
    {
        if($this->user->role != 1) {
            return response()->json([
            'success' => false,
            'message' => 'Você não está autorizado a executar essa ação.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $products = $model->all();
        return response()->json([
            'success' => true,
            'data' => $products
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRepositoryInterface $model, Request $request)
    {
        if($this->user->role != 1) {
            return response()->json([
            'success' => false,
            'message' => 'Você não está autorizado a executar essa ação.'
            ], Response::HTTP_UNAUTHORIZED);
        }
        $data = $request->only('category_id', 'name', 'price', 'quantity');
        $validator = Validator::make($data, [
            'category_id' => 'required',
            'name' => 'required|string',
            'price' => 'required',
            'quantity' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        $product = $model->create($data);
        return response()->json([
            'success' => true,
            'message' => 'Produto cadastrado com sucesso',
            'data' => $product
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(ProductRepositoryInterface $model, $id)
    {
        if($this->user->role != 1) {
            return response()->json([
            'success' => false,
            'message' => 'Você não está autorizado a executar essa ação.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $product = $model->find($id);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Desculpe, produto não encontrado.'
            ], Response::HTTP_NOT_FOUND);
        }
        return $product;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRepositoryInterface $model, Request $request, $id)
    {
        if($this->user->role != 1) {
            return response()->json([
            'success' => false,
            'message' => 'Você não está autorizado a executar essa ação.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $productFind = $model->find($id);
        if (!$productFind) {
            return response()->json([
                'success' => false,
                'message' => 'Desculpe, produto nao foi encontrado.'
            ], Response::HTTP_NOT_FOUND);
        }

        $data = $request->only('category_id', 'name', 'price', 'quantity');
        $validator = Validator::make($data, [
            'category_id' => 'required',
            'name' => 'required|string',
            'price' => 'required',
            'quantity' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        $product = $productFind->update($data);
        return response()->json([
            'success' => true,
            'message' => 'Produto atualizado com sucesso.',
            'data' => $product
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductRepositoryInterface $model, $id)
    {     
        if($this->user->role != 1) {
            return response()->json([
            'success' => false,
            'message' => 'Você não está autorizado a executar essa ação.'
            ], Response::HTTP_UNAUTHORIZED);
        }
           
        $productFind = $model->find($id);
        if (!$productFind) {
            return response()->json([
                'success' => false,
                'message' => 'Desculpe, produto nao foi encontrado.'
            ], Response::HTTP_NOT_FOUND);
        }
    
        $model->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Produto deletado com sucesso.'
        ], Response::HTTP_OK);
    }
}