<?php

namespace App\Models;

use App\Exceptions\WidgetInvalidPropertyValueException;
use App\Exceptions\WidgetMissingPropertyException;
use App\Exceptions\WidgetUnknownTypeException;
use App\Widgets\Balance;
use App\Widgets\Spent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Widget extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'type',
        'sorting_index',
        'properties'
    ];

    protected $appends = ['currency_symbol'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessors
     */

    public function getPropertiesAttribute($value)
    {
        return json_decode($value);
    }

    public function getCurrencySymbolAttribute()
    {
        $space = Space::find(session('space_id'));

        return $space->currency->symbol;
    }

    /**
     * Mutators
     */

    public function setPropertiesAttribute($value)
    {
        /**
         * Using accessor and mutator for this attribute because the "object" cast
         * doesn't use JSON_FORCE_OBJECT
         */

        $this->attributes['properties'] = json_encode($value, JSON_FORCE_OBJECT);
    }

    /**
     * @throws WidgetUnknownTypeException
     * @throws WidgetMissingPropertyException
     * @throws WidgetInvalidPropertyValueException
     */
    public function resolve(): Widget
    {
        $model = null;
        switch ($this->type) {
            case "spent":
                $model = new Spent();
                break;
            case "balance":
                $model = new Balance();
                break;
        }
        if (is_null($model)) {
            throw new WidgetUnknownTypeException();
        }

        $model->setRawAttributes($this->getAttributes(), true);

        $requiredProperties = config('widgets.types.' . $model->type . '.properties');

        foreach ($requiredProperties as $requiredPropertyKey => $requiredPropertyOptions) {
            /**
             * Check if property exists
             */

            if (!isset($model->properties->{$requiredPropertyKey})) {
                throw new WidgetMissingPropertyException();
            }

            /**
             * Check if property has valid value
             */

            if (!in_array($model->properties->{$requiredPropertyKey}, $requiredPropertyOptions)) {
                throw new WidgetInvalidPropertyValueException();
            }
        }

        return $model;
    }
}
