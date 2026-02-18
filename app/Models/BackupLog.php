<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupLog extends Model
{
    public $timestamps = false;
    protected $table = 'backup_logs';
    protected $fillable = [
        'status', 'executado_em'
    ];
}
