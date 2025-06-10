<?php

namespace App\Http\Controllers\Admin;

use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SliderController extends AdminController
{
    public function index()
    {
        $sliders = Slider::all();
        return view('admin.sliders.index', compact('sliders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048'
        ]);

        $path = $request->file('image')->store('public/slider');
        Slider::create(['image_path' => str_replace('public/', 'storage/', $path)]);

        return redirect()->back()->with('success', 'Kép sikeresen feltöltve.');
    }

    public function destroy(Slider $slider)
    {
        Storage::delete(str_replace('storage/', 'public/', $slider->image_path));
        $slider->delete();

        return redirect()->back()->with('success', 'Kép törölve.');
    }
}
