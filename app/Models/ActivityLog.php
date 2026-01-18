<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'description',
        'ip_address',
        'user_agent',
        'reference_type',
        'reference_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'iduser');
    }

    /**
     * Helper to log system activity
     */
    public static function log($action, $description = null, $reference = null)
    {
        $log = new self();
        $log->user_id = auth()->id();
        $log->action = $action;
        $log->description = $description;
        $log->ip_address = request()->ip();
        $log->user_agent = request()->userAgent();
        
        if ($reference) {
            $log->reference_type = get_class($reference);
            $log->reference_id = $reference->getKey();
        }

        $log->save();
        return $log;
    }
}
