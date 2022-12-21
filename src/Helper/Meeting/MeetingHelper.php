<?php
declare(strict_types=1);


namespace App\Helper\Meeting;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

class MeetingHelper
{
    public const ORDER_BY_PARAMETER_NAME = 'orderBy';
    public const DEFAULT_ORDER_BY_PARAMETER = 'startTime';
    public const ORDER_DIRECTION_PARAMETER_NAME = 'orderDirection';
    public const DEFAULT_DIRECTION_PARAMETER = Criteria::DESC;
    public const ALL_ORDER_PARAMS = [
        self::ORDER_BY_PARAMETER_NAME,
        self::ORDER_DIRECTION_PARAMETER_NAME
    ];

    public static function createFilterArrayCollection(array $queryParameters): ArrayCollection
    {
        $filterCollection = new ArrayCollection();
        foreach ($queryParameters as $queryParameterKey => $queryParameter) {
            if (in_array($queryParameterKey, self::ALL_ORDER_PARAMS, true)) {
                continue;
            }
            $filterCollection->set($queryParameterKey, $queryParameter);
        }

        return $filterCollection;
    }
}
