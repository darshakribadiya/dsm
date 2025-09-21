<?php

namespace App\Models\Student;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Academic\{AcademicSession, Section, Standard};

class StudentHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'standard_id',
        'section_id',
        'academic_session_id',
        'start_date',
        'end_date',
        'note',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function standard()
    {
        return $this->belongsTo(Standard::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }
}
