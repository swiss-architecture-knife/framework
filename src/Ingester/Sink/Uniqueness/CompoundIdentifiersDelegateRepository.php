<?php
declare(strict_types=1);

namespace Swark\Ingester\Sink\Uniqueness;

class CompoundIdentifiersDelegateRepository implements CompoundIdentifiersRepository
{
    /** @var CompoundIdentifiersRepository[] */
    private array $delegates = [];

    public function delegateTo(CompoundIdentifiersRepository $compoundIdentifiersRepository): CompoundIdentifiersDelegateRepository
    {
        $this->delegates[] = $compoundIdentifiersRepository;
        return $this;
    }

    private ?array $identifiersLoaded = null;

    private function add(CompoundIdentifier $compoundIdentifier, CompoundIdentifiersRepository $source) {
        if (!isset($this->identifiersLoaded[$compoundIdentifier->getId()])) {
            $this->identifiersLoaded[$compoundIdentifier->getId()] = $compoundIdentifier;
        }

        $this->identifiersLoaded[$compoundIdentifier->getId()]->fromSource($source);
    }

    public function findCompoundIdentifiers(): array
    {
        if ($this->identifiersLoaded === null) {
            $this->identifiersLoaded = [];

            foreach ($this->delegates as $delegate) {
                foreach ($delegate->findCompoundIdentifiers() as $compoundIdentifier) {
                    $this->add($compoundIdentifier, $delegate);
                }
            }
        }

        return array_values($this->identifiersLoaded);
    }
}
