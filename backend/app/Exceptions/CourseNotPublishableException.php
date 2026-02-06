<?php

namespace App\Exceptions;

class CourseNotPublishableException extends ApiException
{
    public function __construct()
    {
        parent::__construct(
            message: 'Course cannot be published. It must have at least one section with a video lesson, a thumbnail, and a description of at least 100 characters.',
            errorCode: 'COURSE_NOT_PUBLISHABLE',
            statusCode: 422
        );
    }
}
