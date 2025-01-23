<?php

namespace Modules\Importer;

use Modules\Importer\Contracts\ImportInterface;
use Modules\DataTransferObject\DataTransferObject;

abstract class Import implements ImportInterface
{
    public function __construct(
        protected string $parser,
        protected array $collection,
        protected Rule $rules
    )
    {
        
    }

    /**
     * @return void
     */
    public function run(): void
    {
        array_map(fn ($dto) => 
            $this->register(
                $this->getParser($dto, $this->rules)->parse()
            )
        , $this->collection);
    }

    /**
     * @param DataTransferObject $dto
     * 
     * @return Parser
     */
    public function getParser(DataTransferObject $dto): Parser
    {
        return app($this->parser, [
            'dto' => $dto,
            'rules' => $this->rules
        ]);
    }

    /**
     * @return array
     */
    public function getCollection(): array
    {
        return $this->collection;
    }
}