<?php

namespace Modules\Product\Presentation\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Product\Application\DTOs\CreateProductDTO;
use Modules\Product\Application\UseCases\CreateProductUseCase;
use Modules\Product\Infrastructure\Models\Product;
use Modules\Product\Presentation\Requests\StoreProductRequest;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view products')->only(['index', 'show', 'trash']);
        $this->middleware('permission:create products')->only(['create', 'store', 'import']);
        $this->middleware('permission:edit products')->only(['edit', 'update', 'restore']);
        $this->middleware('permission:delete products')->only(['destroy', 'forceDelete']);
    }

    public function index(): Response
    {
        return Inertia::render('Product/Index', [
            'products' => Product::latest()->paginate(15),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Product/Create');
    }

    public function store(StoreProductRequest $request, CreateProductUseCase $useCase): RedirectResponse
    {
        $dto = new CreateProductDTO(
            name: $request->name,
            price: (float) $request->price,
            description: $request->description ?? '',
        );

        $useCase->execute($dto);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product): Response
    {
        return Inertia::render('Product/Edit', [
            'product' => $product,
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $product->update($request->only('name', 'price', 'description', 'is_active'));

        return redirect()->route('products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted.');
    }

    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \Modules\Product\Application\Exports\ProductsExport, 'products.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        \Maatwebsite\Excel\Facades\Excel::import(new \Modules\Product\Application\Imports\ProductsImport, $request->file('file'));

        return redirect()->route('products.index')->with('success', 'Products imported successfully.');
    }

    public function trash(): Response
    {
        return Inertia::render('Product/Trash', [
            'products' => Product::onlyTrashed()->latest()->paginate(15),
        ]);
    }

    public function restore($id): RedirectResponse
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();

        return redirect()->route('products.trash')->with('success', 'Product restored.');
    }

    public function forceDelete($id): RedirectResponse
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->forceDelete();

        return redirect()->route('products.trash')->with('success', 'Product permanently deleted.');
    }
}
