<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'amount',
        'expense_date',
        'category_id',
        'partner_id',
        'receipt_url',
        'status',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'status' => 'string',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * Get the category for this expense.
     */
    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    /**
     * Get the partner for this expense.
     */
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Get the user who created this expense.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            self::STATUS_APPROVED => 'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-500',
            self::STATUS_REJECTED => 'bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-500',
            default => 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-500',
        };
    }
}
