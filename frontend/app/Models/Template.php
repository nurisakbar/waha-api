<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Template extends Model
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
            
            // Auto-add kode_otp variable for OTP templates
            if ($model->template_type === 'otp') {
                $variables = $model->variables ?? [];
                if (!in_array('kode_otp', $variables)) {
                    $variables[] = 'kode_otp';
                    $model->variables = $variables;
                }
            }
        });
        
        static::updating(function ($model) {
            // Auto-add kode_otp variable for OTP templates on update
            if ($model->template_type === 'otp') {
                $variables = $model->variables ?? [];
                if (!in_array('kode_otp', $variables)) {
                    $variables[] = 'kode_otp';
                    $model->variables = $variables;
                }
            }
        });
    }

    protected $fillable = [
        'user_id',
        'name',
        'content',
        'message_type',
        'template_type',
        'variables',
        'description',
        'is_active',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'variables' => 'array',
            'metadata' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the user that owns the template.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Extract variables from content.
     * Variables are in format {{variable_name}}
     */
    public function extractVariables(): array
    {
        $variables = [];
        preg_match_all('/\{\{(\w+)\}\}/', $this->content, $matches);
        
        if (!empty($matches[1])) {
            $variables = array_unique($matches[1]);
        }
        
        return $variables;
    }

    /**
     * Replace variables in content with provided values.
     */
    public function replaceVariables(array $values): string
    {
        $content = $this->content;
        
        foreach ($values as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }
        
        return $content;
    }

    /**
     * Get processed content with variables replaced.
     */
    public function getProcessedContent(array $variables = []): string
    {
        if (empty($variables)) {
            return $this->content;
        }
        
        return $this->replaceVariables($variables);
    }

    /**
     * Check if template is OTP type.
     */
    public function isOtpTemplate(): bool
    {
        return $this->template_type === 'otp';
    }

    /**
     * Check if template is message type.
     */
    public function isMessageTemplate(): bool
    {
        return $this->template_type === 'message' || empty($this->template_type);
    }
}
