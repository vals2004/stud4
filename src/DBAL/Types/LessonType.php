<?php
namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

final class LessonType extends AbstractEnumType
{
    public const LECTURE = 'LC';
    public const PRACTICAL = 'PR';
    public const LABORATORY = 'LB';

    protected static $choices = [
        self::LECTURE => 'Lecture',
        self::PRACTICAL=> 'Practical',
        self::LABORATORY=> 'Laboratory',
    ];
}
