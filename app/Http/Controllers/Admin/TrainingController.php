<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TrainingController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->hasRole('Coach')) {
            return redirect()->route('coach.planificaciones');
        }

        $query = Training::with(['category', 'coach']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $trainings = $query->latest()->paginate(10);
        $categories = Category::all();
        
        return view('admin.trainings.index', compact('trainings', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.trainings.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'fecha' => 'required|date',
            'pdf' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $data = [
            'category_id' => $validated['category_id'],
            'fecha' => $validated['fecha'],
            'coach_id' => auth()->id(),
        ];

        if ($request->hasFile('pdf')) {
            $data['file_path_pdf'] = $request->file('pdf')->store('trainings', 'public');
        }

        Training::create($data);

        $route = auth()->user()->hasRole('Coach') ? 'coach.planificaciones' : 'trainings.index';
        return redirect()->route($route)->with('success', 'Planificación registrada correctamente.');
    }

    public function edit(Training $training)
    {
        $categories = Category::all();
        return view('admin.trainings.edit', compact('training', 'categories'));
    }

    public function update(Request $request, Training $training)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'fecha' => 'required|date',
            'pdf' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $data = [
            'category_id' => $validated['category_id'],
            'fecha' => $validated['fecha'],
        ];

        if ($request->hasFile('pdf')) {
            if ($training->file_path_pdf) {
                Storage::disk('public')->delete($training->file_path_pdf);
            }
            $data['file_path_pdf'] = $request->file('pdf')->store('trainings', 'public');
        }

        $training->update($data);

        $route = auth()->user()->hasRole('Coach') ? 'coach.planificaciones' : 'trainings.index';
        return redirect()->route($route)->with('success', 'Planificación actualizada correctamente.');
    }

    public function destroy(Training $training)
    {
        if ($training->file_path_pdf) {
            Storage::disk('public')->delete($training->file_path_pdf);
        }
        $training->delete();

        $route = auth()->user()->hasRole('Coach') ? 'coach.planificaciones' : 'trainings.index';
        return redirect()->route($route)->with('success', 'Planificación eliminada.');
    }
}
