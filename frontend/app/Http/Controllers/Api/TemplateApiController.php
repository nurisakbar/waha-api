<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateTemplateRequest;
use App\Http\Requests\Api\UpdateTemplateRequest;
use App\Models\Template;
use App\Services\ApiUsageService;
use App\Services\TemplateService;
use Illuminate\Http\Request;

class TemplateApiController extends Controller
{
    protected TemplateService $templateService;
    protected ApiUsageService $usageService;

    public function __construct(TemplateService $templateService, ApiUsageService $usageService)
    {
        $this->templateService = $templateService;
        $this->usageService = $usageService;
    }

    /**
     * Get all templates for the authenticated user.
     */
    public function index(Request $request)
    {
        $startTime = microtime(true);

        $query = Template::where('user_id', $request->user->id);

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Filter by message type
        if ($request->has('message_type')) {
            $query->where('message_type', $request->message_type);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $templates = $query->latest()->paginate($request->get('per_page', 20));

        $this->usageService->log($request, 200, $startTime);

        return response()->json([
            'success' => true,
            'data' => $templates->items(),
            'pagination' => [
                'current_page' => $templates->currentPage(),
                'per_page' => $templates->perPage(),
                'total' => $templates->total(),
                'last_page' => $templates->lastPage(),
            ],
        ]);
    }

    /**
     * Get a specific template.
     */
    public function show(Request $request, $templateId)
    {
        $startTime = microtime(true);

        $template = Template::where('id', $templateId)
            ->where('user_id', $request->user->id)
            ->first();

        if (!$template) {
            $this->usageService->log($request, 404, $startTime);
            return response()->json([
                'success' => false,
                'error' => 'Template not found',
            ], 404);
        }

        $this->usageService->log($request, 200, $startTime);

        return response()->json([
            'success' => true,
            'data' => $template,
        ]);
    }

    /**
     * Create a new template.
     */
    public function store(CreateTemplateRequest $request)
    {
        $startTime = microtime(true);

        // Extract variables from content if not provided
        $variables = $request->variables;
        if (empty($variables)) {
            $variables = $this->templateService->extractVariables($request->content);
        }

        $template = Template::create([
            'user_id' => $request->user->id,
            'name' => $request->name,
            'content' => $request->content,
            'message_type' => $request->message_type,
            'variables' => $variables,
            'description' => $request->description,
            'is_active' => $request->input('is_active', true),
            'metadata' => $request->metadata,
        ]);

        $this->usageService->log($request, 201, $startTime);

        return response()->json([
            'success' => true,
            'data' => $template,
            'message' => 'Template created successfully',
        ], 201);
    }

    /**
     * Update a template.
     */
    public function update(UpdateTemplateRequest $request, $templateId)
    {
        $startTime = microtime(true);

        $template = Template::where('id', $templateId)
            ->where('user_id', $request->user->id)
            ->first();

        if (!$template) {
            $this->usageService->log($request, 404, $startTime);
            return response()->json([
                'success' => false,
                'error' => 'Template not found',
            ], 404);
        }

        $updateData = $request->only([
            'name',
            'content',
            'message_type',
            'description',
            'is_active',
            'metadata',
        ]);

        // Update variables if content changed or variables explicitly provided
        if ($request->has('content') || $request->has('variables')) {
            if ($request->has('variables')) {
                $updateData['variables'] = $request->variables;
            } else {
                $updateData['variables'] = $this->templateService->extractVariables($request->input('content', $template->content));
            }
        }

        $template->update($updateData);

        $this->usageService->log($request, 200, $startTime);

        return response()->json([
            'success' => true,
            'data' => $template->fresh(),
            'message' => 'Template updated successfully',
        ]);
    }

    /**
     * Delete a template.
     */
    public function destroy(Request $request, $templateId)
    {
        $startTime = microtime(true);

        $template = Template::where('id', $templateId)
            ->where('user_id', $request->user->id)
            ->first();

        if (!$template) {
            $this->usageService->log($request, 404, $startTime);
            return response()->json([
                'success' => false,
                'error' => 'Template not found',
            ], 404);
        }

        $template->delete();

        $this->usageService->log($request, 200, $startTime);

        return response()->json([
            'success' => true,
            'message' => 'Template deleted successfully',
        ]);
    }

    /**
     * Preview template with variables replaced.
     */
    public function preview(Request $request, $templateId)
    {
        $startTime = microtime(true);

        $template = Template::where('id', $templateId)
            ->where('user_id', $request->user->id)
            ->first();

        if (!$template) {
            $this->usageService->log($request, 404, $startTime);
            return response()->json([
                'success' => false,
                'error' => 'Template not found',
            ], 404);
        }

        $variables = $request->input('variables', []);
        
        // Validate variables
        $validation = $this->templateService->validateVariables($template, $variables);
        if (!$validation['valid']) {
            $this->usageService->log($request, 400, $startTime);
            return response()->json([
                'success' => false,
                'error' => 'Missing required variables',
                'missing_variables' => $validation['missing'],
            ], 400);
        }

        $processed = $this->templateService->processTemplate($template, $variables);

        $this->usageService->log($request, 200, $startTime);

        return response()->json([
            'success' => true,
            'data' => [
                'original' => [
                    'content' => $template->content,
                    'metadata' => $template->metadata,
                ],
                'processed' => $processed,
            ],
        ]);
    }
}
