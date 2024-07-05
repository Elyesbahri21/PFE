<?php
namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use App\Entity\Contrat;

class DateFinValidator extends ConstraintValidator
{
    public function validate($contrat, Constraint $constraint)
    {
        if (!$constraint instanceof DateFin) {
            throw new UnexpectedTypeException($constraint, DateFin::class);
        }

        if (!$contrat instanceof Contrat) {
            throw new UnexpectedValueException($contrat, Contrat::class);
        }

        if ($contrat->getDateDebut() === null || $contrat->getDateFin() === null) {
            return;
        }

        $dateDebut = $contrat->getDateDebut();
        $dateFin = $contrat->getDateFin();

        $oneYearLater = (clone $dateDebut)->modify('+1 year');

        // Add a debug message
        error_log('DateFinValidator: dateDebut = ' . $dateDebut->format('Y-m-d') . ', dateFin = ' . $dateFin->format('Y-m-d'));

        if ($dateFin < $oneYearLater) {
            $this->context->buildViolation($constraint->message)
                ->atPath('date_fin')
                ->addViolation();
        }
    }
}
