<?php
namespace App\Http\Controllers;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
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
    public function index(CategoryRepositoryInterface $model)
    {   
        if($this->user->role != 1) {
            return response()->json([
            'success' => false,
            'message' => 'Você não está autorizado a executar essa ação.'
            ], Response::HTTP_UNAUTHORIZED);
        }
        $categories = $model->all();
        return response()->json([
            'success' => true,
            'data' => $categories
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRepositoryInterface $model, Request $request)
    {
        if($this->user->role != 1) {
            return response()->json([
            'success' => false,
            'message' => 'Você não está autorizado a executar essa ação.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $data = $request->only('name');
        $validator = Validator::make($data, [
            'name' => 'required|string|unique:categories,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        $category = $model->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Categoria criada com sucesso.',
            'data' => $category
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(CategoryRepositoryInterface $model, $id)
    {   
        if($this->user->role != 1) {
            return response()->json([
            'success' => false,
            'message' => 'Você não está autorizado a executar essa ação.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $category = $model->with('products')->find($id);
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Desculpe, categoria não foi encontrada.'
            ], 400);
        }
        return $category;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryRepositoryInterface $model, Request $request, $id)
    {
        if($this->user->role != 1) {
            return response()->json([
            'success' => false,
            'message' => 'Você não está autorizado a executar essa ação.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $categoryFind = $model->find($id);
        if (!$categoryFind) {
            return response()->json([
                'success' => false,
                'message' => 'Desculpe, a categoria nao foi encontrada.'
            ], Response::HTTP_NOT_FOUND);
        }

        $data = $request->only('name');
        $validator = Validator::make($data, [
            'name' => 'required|string|unique:categories,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        $category = $categoryFind->update($data);
        return response()->json([
            'success' => true,
            'message' => 'Categoria atualizada com sucesso.',
            'data' => $category
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(CategoryRepositoryInterface $model, $id)
    {
        if($this->user->role != 1) {
            return response()->json([
            'success' => false,
            'message' => 'Você não está autorizado a executar essa ação.'
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $categoryFind = $model->find($id);
        if (!$categoryFind) {
            return response()->json([
                'success' => false,
                'message' => 'Desculpe, a categoria nao foi encontrada.'
            ], Response::HTTP_NOT_FOUND);
        }
        
        $categoryFind->delete();
        return response()->json([
            'success' => true,
            'message' => 'Categoria deletada com sucesso.'
        ], Response::HTTP_OK);
    }
}