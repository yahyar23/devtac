<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientPlan extends Model
{
    use HasFactory;
    protected $fillable = [
        'patient_id',
        'patient_name',
        'plan_type',
        'd2_percent',
        'd50_percent',
        'd98_percent',
        'v_ptv_total',
        'v100_percent',
        'v95_percent',
        'v50_percent',
        'hi_index',
        'ci_index',
        'gi_index',
        'ncdi_value',
        'passed_gatekeeper',
        'status_level',
        'status_label',
        'recommendation',
    ];
}
