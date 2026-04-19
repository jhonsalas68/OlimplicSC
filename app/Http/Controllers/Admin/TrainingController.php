<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Notifications\TrainingUploaded;
use App\Traits\FileStorageHelper;
use Illuminate\Support\Facades\Notification;

class TrainingController extends Controller
{
    use FileStorageHelper;
    public function index(Request $request)
    {

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
            $path = Storage::disk('r2')->putFile('trainings', $request->file('pdf'));
            $data['file_path_pdf'] = Storage::disk('r2')->url($path);
        }

        $training = Training::create($data);

        // Notificar a todos los usuarios (excepto a quien lo subió, opcionalmente)
        $usersToNotify = User::all();
        $coachName = auth()->user()->name;
        $categoryName = Category::find($validated['category_id'])->nombre ?? '—';
        
        Notification::send($usersToNotify, new TrainingUploaded($training, $coachName, $categoryName));

        \App\Services\ActivityLogger::log(
            'subida_planificacion', 
            "Nueva planificación subida para la categoría {$categoryName}.",
            $training
        );

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
                $this->deleteFile($training->file_path_pdf);
            }
            
            $path = Storage::disk('r2')->putFile('trainings', $request->file('pdf'));
            $data['file_path_pdf'] = Storage::disk('r2')->url($path);
        }

        $training->update($data);

        $categoryName = $training->category->nombre ?? '—';
        \App\Services\ActivityLogger::log(
            'edicion_planificacion', 
            "Planificación actualizada para la categoría {$categoryName} (Fecha: {$training->fecha}).",
            $training
        );

        $route = auth()->user()->hasRole('Coach') ? 'coach.planificaciones' : 'trainings.index';
        return redirect()->route($route)->with('success', 'Planificación actualizada correctamente.');
    }

    public function destroy(Training $training)
    {
        if ($training->file_path_pdf) {
            $this->deleteFile($training->file_path_pdf);
        }
        
        $categoryName = $training->category->nombre ?? '—';
        \App\Services\ActivityLogger::log(
            'eliminacion_planificacion', 
            "Planificación eliminada de la categoría {$categoryName} (Fecha: {$training->fecha}).",
            null,
            ['categoria' => $categoryName, 'fecha' => $training->fecha]
        );

        $training->delete();

        $route = auth()->user()->hasRole('Coach') ? 'coach.planificaciones' : 'trainings.index';
        return redirect()->route($route)->with('success', 'Planificación eliminada.');
    }
}
