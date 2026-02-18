<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    public $timestamps = false;
    protected $table = 'login_attempts';
    protected $fillable = [
        'email', 'ip_address', 'success', 'user_agent', 'attempted_at'
    ];
}
