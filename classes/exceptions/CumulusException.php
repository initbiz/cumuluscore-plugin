<?php

declare(strict_types=1);

namespace Initbiz\CumulusCore\Classes\Exceptions;

use October\Rain\Exception\ApplicationException;

/**
 * Exceptions from Cumulus eco-system that are going to be displayed to the user
 * but not logged in the event log
 */
class CumulusException extends ApplicationException
{
}
