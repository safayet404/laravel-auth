<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentDocument extends Model
{
    protected $fillable = ['student_id','uploaded_by_user_id','original_name','path','mime','size'];
    public function student() {
        return $this->belongsTo(Student::class);
    }
}
