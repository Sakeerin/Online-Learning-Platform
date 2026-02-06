<?php

namespace App\Exceptions;

class CourseNotFoundException extends ApiException
{
    public function __construct(string $courseId = '')
    {
        parent::__construct(
            message: "Course not found" . ($courseId ? ": {$courseId}" : ''),
            errorCode: 'COURSE_NOT_FOUND',
            statusCode: 404
        );
    }
}
