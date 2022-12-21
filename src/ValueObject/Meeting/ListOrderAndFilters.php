<?php
declare(strict_types=1);


namespace App\ValueObject\Meeting;


use App\Enum\Meeting\Filters;
use App\Enum\Meeting\OrderBy;
use App\Exception\Meeting\UnsupportedFilterFieldException;
use App\Exception\Meeting\UnsupportedOrderByFieldException;
use App\Exception\Meeting\UnsupportedOrderDirectionFieldException;
use App\ValueObject\Validate;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

class ListOrderAndFilters implements Validate
{
    public function __construct(
        public readonly ArrayCollection $filters,
        public readonly ?string          $orderBy,
        public readonly ?string          $orderDirection)
    {
    }

    public function validate(): void
    {
        foreach ($this->filters as $filterName => $filterValue) {
            if (!in_array($filterName, Filters::allStatus(), true)) {
                throw new UnsupportedFilterFieldException(sprintf('Unsupported filter, supported only: %s',
                    implode(', ', Filters::allStatus())));
            }
        }

        if (!in_array($this->orderBy, OrderBy::allStatus(), true)) {
            throw new UnsupportedOrderByFieldException(sprintf('Unsupported OrderBy, supported only: %s',
                implode(', ', OrderBy::allStatus())));
        }

        if ($this->orderDirection !== Criteria::DESC && $this->orderDirection !== Criteria::ASC) {
            throw new UnsupportedOrderDirectionFieldException(sprintf('Unsupported OrderDirection, supported only 
                "%s" and "%s"', Criteria::DESC, Criteria::ASC));
        }
    }
}
