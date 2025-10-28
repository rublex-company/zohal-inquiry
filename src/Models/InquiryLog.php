<?php

namespace Inquiry\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InquiryLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'inquiry_logs';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'method',
        'endpoint',
        'request_data',
        'response_data',
        'status_code',
        'response_status',
        'error_message',
        'response_time_ms',
        'ip_address',
        'user_agent',
        'request_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
        'status_code' => 'integer',
        'response_time_ms' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope for successful requests
     */
    public function scopeSuccessful($query)
    {
        return $query->where('response_status', 'success');
    }

    /**
     * Scope for failed requests
     */
    public function scopeFailed($query)
    {
        return $query->where('response_status', 'error');
    }

    /**
     * Scope for specific method
     */
    public function scopeByMethod($query, string $method)
    {
        return $query->where('method', $method);
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get the request data as a formatted string
     */
    public function getFormattedRequestDataAttribute(): string
    {
        return json_encode($this->request_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get the response data as a formatted string
     */
    public function getFormattedResponseDataAttribute(): string
    {
        return json_encode($this->response_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get response time in seconds
     */
    public function getResponseTimeSecondsAttribute(): float
    {
        return $this->response_time_ms ? $this->response_time_ms / 1000 : 0;
    }
}
