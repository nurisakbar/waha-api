<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $templates = Template::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('templates.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string|max:4096',
            'template_type' => 'required|in:message,otp',
            'variables' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Parse variables from comma-separated string to array
        $variables = [];
        if (!empty($validated['variables'])) {
            $variables = array_map('trim', explode(',', $validated['variables']));
        }

        $template = Template::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'content' => $validated['content'],
            'template_type' => $validated['template_type'],
            'variables' => $variables,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('templates.index')
            ->with('success', 'Template berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Template $template)
    {
        $this->authorize('view', $template);

        return view('templates.show', compact('template'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Template $template)
    {
        $this->authorize('update', $template);

        return view('templates.edit', compact('template'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Template $template)
    {
        $this->authorize('update', $template);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string|max:4096',
            'template_type' => 'required|in:message,otp',
            'variables' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Parse variables from comma-separated string to array
        $variables = [];
        if (!empty($validated['variables'])) {
            $variables = array_map('trim', explode(',', $validated['variables']));
        }

        $template->update([
            'name' => $validated['name'],
            'content' => $validated['content'],
            'template_type' => $validated['template_type'],
            'variables' => $variables,
            'is_active' => $request->boolean('is_active', $template->is_active),
        ]);

        return redirect()->route('templates.index')
            ->with('success', 'Template berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Template $template)
    {
        $this->authorize('delete', $template);

        $template->delete();

        return redirect()->route('templates.index')
            ->with('success', 'Template berhasil dihapus.');
    }
}

