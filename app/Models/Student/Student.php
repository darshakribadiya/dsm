<?php

namespace App\Models\Student;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Academic\{AcademicSession, Section, Standard};
use App\Models\User;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admission_number',
        'dob',
        'gender',
        'admission_date',
        'current_standard_id',
        'current_section_id',
        'current_academic_session_id',
        'status',
    ];

    /**
     * Relations
     */

    // A student belongs to a user (optional login account)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Current standard
    public function standard()
    {
        return $this->belongsTo(Standard::class, 'current_standard_id');
    }

    // Current section
    public function section()
    {
        return $this->belongsTo(Section::class, 'current_section_id');
    }

    // Current academic session
    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class, 'current_academic_session_id');
    }

    // Full history of the student
    public function history()
    {
        return $this->hasMany(StudentHistory::class);
    }

    // Electives for this student
    public function electives()
    {
        return $this->hasMany(StudentElective::class);
    }
}
