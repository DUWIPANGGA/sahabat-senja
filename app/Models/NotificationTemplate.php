<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'template_code',
        'name',
        'subject',
        'content',
        'variables',
        'description',
        'type',
        'category',
        'target_audience',
        'trigger_conditions',
        'is_active',
        'is_auto_send',
        'delay_minutes',
        'cron_schedule',
        'metadata'
    ];

    protected $casts = [
        'variables' => 'array',
        'trigger_conditions' => 'array',
        'metadata' => 'array',
        'is_active' => 'boolean',
        'is_auto_send' => 'boolean',
        'delay_minutes' => 'integer'
    ];

    /**
     * Get available variables as array
     */
    public function getVariablesArrayAttribute()
    {
        if (empty($this->variables)) {
            return [];
        }
        
        return is_array($this->variables) ? $this->variables : json_decode($this->variables, true);
    }

    /**
     * Get trigger conditions as array
     */
    public function getTriggerConditionsArrayAttribute()
    {
        if (empty($this->trigger_conditions)) {
            return [];
        }
        
        return is_array($this->trigger_conditions) ? $this->trigger_conditions : json_decode($this->trigger_conditions, true);
    }

    /**
     * Scope for active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for auto-send templates
     */
    public function scopeAutoSend($query)
    {
        return $query->where('is_auto_send', true);
    }

    /**
     * Scope by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Generate content with variables
     */
    public function generateContent($variables = [])
    {
        $content = $this->content;
        
        foreach ($variables as $key => $value) {
            $placeholder = '{' . $key . '}';
            $content = str_replace($placeholder, $value, $content);
        }
        
        return $content;
    }

    /**
     * Generate subject with variables
     */
    public function generateSubject($variables = [])
    {
        $subject = $this->subject;
        
        foreach ($variables as $key => $value) {
            $placeholder = '{' . $key . '}';
            $subject = str_replace($placeholder, $value, $subject);
        }
        
        return $subject;
    }

    /**
     * Check if template has all required variables
     */
    public function hasRequiredVariables($providedVariables)
    {
        $requiredVariables = $this->variables_array;
        
        if (empty($requiredVariables)) {
            return true;
        }
        
        foreach ($requiredVariables as $variable) {
            if (!array_key_exists($variable, $providedVariables)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Get missing variables
     */
    public function getMissingVariables($providedVariables)
    {
        $requiredVariables = $this->variables_array;
        $missing = [];
        
        if (empty($requiredVariables)) {
            return $missing;
        }
        
        foreach ($requiredVariables as $variable) {
            if (!array_key_exists($variable, $providedVariables)) {
                $missing[] = $variable;
            }
        }
        
        return $missing;
    }
}