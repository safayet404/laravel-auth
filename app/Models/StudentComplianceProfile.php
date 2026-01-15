<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentComplianceProfile extends Model
{
    protected $fillable = ['student_id','counselor_user_id','institution','program','intake','tution_fee','scholarship','paid_amount','remaining_amount','remaining_amount','notes'];


    public function student(){
        return $this->belongsTo(Student::class);
    }
    public function interviews(){
        return $this->hasMany(Interview::class,'compliance_profile_id');
    }
}
