<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Medicine;
use Illuminate\Http\Request;

class MedicineController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id'
        ]);

        $medicine = Medicine::create($request->all());

        return response()->json([
            'message' => 'Medicine added successfully',
            'medicine' => $medicine,
        ], 201);
    }

    public function index()
    {
        $medicines = Medicine::all();
        return response()->json([
            'message' => 'Medicines retrieved successfully',
            'medicines' => $medicines,
        ]);
    }

    public function show($id)
    {
        $medicine = Medicine::findOrFail($id);
        return response()->json(['medicine' => $medicine]);
    }

    public function update(Request $request, $id)
    {
        $medicine = Medicine::findOrFail($id);
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'stock' => 'sometimes|integer|min:0',
            'price' => 'sometimes|numeric|min:0',
        ]);

        $medicine->update($request->all());
        return response()->json([
            'message' => 'Medicine updated successfully',
            'medicine' => $medicine,
        ]);
    }

    public function destroy($id)
    {
        $medicine = Medicine::findOrFail($id);
        $medicine->delete();
        return response()->json(['message' => 'Medicine deleted successfully']);
    }
}
