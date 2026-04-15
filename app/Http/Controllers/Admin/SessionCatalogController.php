<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SessionCatalogItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SessionCatalogController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type');
        $query = SessionCatalogItem::query()->orderBy('sort_order')->orderBy('name');
        if ($type && in_array($type, SessionCatalogItem::types(), true)) {
            $query->where('type', $type);
        }

        return response()->json(['items' => $query->get()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(SessionCatalogItem::types())],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('session_catalog_items', 'name')->where('type', $request->input('type')),
            ],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $item = SessionCatalogItem::create([
            'type' => $validated['type'],
            'name' => trim($validated['name']),
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Option added.',
            'item' => $item,
        ], 201);
    }

    public function update(Request $request, SessionCatalogItem $session_catalog_item)
    {
        $validated = $request->validate([
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('session_catalog_items', 'name')
                    ->where('type', $session_catalog_item->type)
                    ->ignore($session_catalog_item->id),
            ],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if (isset($validated['name'])) {
            $validated['name'] = trim($validated['name']);
        }

        $session_catalog_item->update($validated);

        return response()->json([
            'message' => 'Option updated.',
            'item' => $session_catalog_item->fresh(),
        ]);
    }

    public function destroy(SessionCatalogItem $session_catalog_item)
    {
        $session_catalog_item->delete();

        return response()->json(['message' => 'Option removed.']);
    }
}
