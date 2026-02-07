<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'is_required',
        'is_filterable',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_filterable' => 'boolean',
    ];

    /**
     * Get the values for this attribute
     */
    public function values()
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    /**
     * Get attribute type options
     */
    public static function getTypeOptions(): array
    {
        return [
            'text' => 'Text',
            'number' => 'Number',
            'select' => 'Single Select',
            'multiselect' => 'Multiple Select',
            'boolean' => 'Yes/No',
            'date' => 'Date',
        ];
    }
}
