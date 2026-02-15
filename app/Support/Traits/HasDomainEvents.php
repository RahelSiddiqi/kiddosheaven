<?php

namespace App\Support\Traits;

/**
 * Trait for models that dispatch domain events.
 * Domain events represent important business occurrences.
 */
trait HasDomainEvents
{
    protected array $domainEvents = [];

    public function recordEvent(object $event): void
    {
        $this->domainEvents[] = $event;
    }

    public function releaseEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }

    public function dispatchRecordedEvents(): void
    {
        foreach ($this->releaseEvents() as $event) {
            event($event);
        }
    }
}
