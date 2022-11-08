<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Validation\Rules;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'verification_token',
        'last_verification_mail_sent_at',
        'stripe_customer_id',
        'plan',
        'language'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $attributes = [
        'plan' => 'standard'
    ];

    //
    public static function getValidationRulesForRegistration(): array
    {
        return [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'currency' => 'required|exists:currencies,id'
        ];
    }

    public static function getValidationRulesForPasswordReset(): array
    {
        return [
            'email' => 'required_without:password|email',
            'password' => 'required_without:email|confirmed'
        ];
    }

    // Accessors
    public function getAvatarAttribute($avatar)
    {
        return $avatar ? '/storage/avatars/' . $avatar : 'https://ui-avatars.com/api/?size=512&background=0D8ABC&color=fff&name=' . $this->name;
    }

    // Relations
    public function spaces()
    {
        return $this->belongsToMany(Space::class, 'user_space')->withPivot('role');
    }

    public function widgets()
    {
        $widgets = $this->hasMany(Widget::class)->orderBy('sorting_index')->get();
        $result = [];
        /** @var Widget $widget */
        foreach ($widgets as $widget) {
            $result[] = $widget->resolve();
        }
        return $result;
    }
}
