<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentComplianceProfile extends Model
{
    protected $fillable = ['student_id', 'counselor_user_id', 'institution', 'program', 'intake', 'tuition_fee', 'scholarship', 'paid_amount', 'remaining_amount', 'remaining_amount', 'notes'];


    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function counselor()
    {
        return $this->belongsTo(User::class, 'counselor_user_id');
    }
    public function interviews()
    {
        return $this->hasMany(Interview::class, 'compliance_profile_id');
    }
}
