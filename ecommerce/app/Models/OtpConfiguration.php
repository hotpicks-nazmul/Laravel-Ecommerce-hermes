<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    public $timestamps = true;

    /**
     * Get OTP configuration value by key
     */
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set OTP configuration value
     */
    public static function set($key, $value, $group = null)
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );
    }

    /**
     * Get all OTP configurations as an array
     */
    public static function getAllConfig()
    {
        $configs = static::all();
        return $configs->pluck('value', 'key')->toArray();
    }

    /**
     * Save multiple configurations at once
     */
    public static function saveMultiple(array $configurations)
    {
        foreach ($configurations as $key => $value) {
            static::set($key, $value);
        }
        return true;
    }

    /**
     * Get configuration group
     */
    public static function getByGroup($group)
    {
        return static::where('group', $group)->get();
    }

    /**
     * Default configuration values
     */
    public static function defaults()
    {
        return [
            'otp_length' => 6,
            'otp_expiry' => 5,
            'otp_max_attempts' => 3,
            'resend_cooldown' => 60,
            'otp_for_login' => 1,
            'otp_for_registration' => 1,
            'otp_for_password_reset' => 1,
            'otp_for_payment' => 0,
            'otp_for_profile_change' => 0,
            'otp_for_order_confirmation' => 0,
            'otp_case_sensitive' => 0,
            'otp_alphanumeric' => 0,
            'max_otp_per_day' => 10,
            'temp_block_duration' => 30,
        ];
    }

    /**
     * Get templates defaults
     */
    public static function templateDefaults()
    {
        return [
            'otp_verification_template' => 'Your verification code is: {otp}. Valid for {expiry} minutes.',
            'registration_template' => 'Welcome to {site_name}! Your account has been created successfully.',
            'password_reset_template' => 'Your password reset OTP is: {otp}. Do not share this code with anyone.',
            'payment_template' => 'Your payment verification code is: {otp}. Amount: {amount}.',
            'order_confirmation_template' => 'Your order OTP is: {otp}. Order ID: {order_id}.',
            'login_notification_template' => 'Your login OTP is: {otp}. If you did not request this, please ignore.',
        ];
    }

    /**
     * Get credentials defaults
     */
    public static function credentialsDefaults()
    {
        return [
            'sms_gateway' => 'custom',
            'custom_api_url' => '',
            'custom_api_key' => '',
            'custom_api_secret' => '',
            'custom_sender_id' => '',
            'custom_api_method' => 'POST',
            'custom_request_body' => '{"api_key": "{{api_key}}", "sender_id": "{{sender_id}}", "phone": "{{phone}}", "message": "{{message}}"}',
        ];
    }
}
