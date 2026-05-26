<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.categories.index', [
            'categories' => Category::withCount('products')
                ->when($request->search, fn ($query) => $query->where('name', 'like', '%'.$request->search.'%'))
                ->when($request->filled('is_active'), fn ($query) => $query->where('is_active', $request->boolean('is_active')))
                ->orderBy('sort_order')
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'max:120'],
            'description' => ['nullable'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        Category::create($data + ['slug' => Str::slug($data['name']).'-'.substr(md5(microtime()), 0, 5), 'is_active' => $request->boolean('is_active', true)]);
        return back()->with('status', 'Kategori dibuat.');
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => ['required', 'max:120'],
            'description' => ['nullable'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active');
        $category->update($data);
        return back()->with('status', 'Kategori diperbarui.');
    }

    public function destroy(Category $category)
    {
        abort_if(auth()->user()->hasRole('staff'), 403);
        $category->delete();
        return back()->with('status', 'Kategori dihapus.');
    }
}
