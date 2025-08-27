<?php

namespace App\Filters;

use App\Base\Filters\AbstractFilters;
use App\Filters\Pipelines\WalletTransactionPipeline;

class WalletTransactionFilters extends AbstractFilters
{
    protected function getPipelines(): array
    {
        return [
            new WalletTransactionPipeline($this->request->all()),
        ];
    }
}
