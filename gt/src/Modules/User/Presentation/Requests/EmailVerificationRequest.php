<?php

namespace Modules\User\Presentation\Requests;

use Illuminate\Foundation\Auth\EmailVerificationRequest as BaseEmailVerificationRequest;

class EmailVerificationRequest extends BaseEmailVerificationRequest
{
    // Inherit authorize and fulfill methods from framework
}
