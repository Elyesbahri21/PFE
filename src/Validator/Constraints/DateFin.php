<?php
namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DateFin extends Constraint
{
    public $message = 'La date de fin doit être au moins un an après la date de début.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
