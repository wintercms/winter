<?php

namespace System\Classes;

use Illuminate\Support\Facades\Cache;

/**
 * Event stream.
 *
 * Represents a HTML5 event stream that can be interacted with when using the `withEventStream` method in the
 * `System\Traits\ResponseMaker` trait. This class is intended to be used with a front-end library that can interpret
 * the event stream and manage and display real-time information and updates.
 *
 * @author Ben Thomson <git@alfreido.com>
 * @copyright 2024 Winter CMS Maintainers
 */
class EventStream
{
    protected ?string $pendingEvent = null;

    protected bool $closed = false;

    public function __construct(
        protected string $id = '',
        protected array $data = [],
        protected float $ticks = 1.0,
    ) {
    }

    public function register()
    {
        // Generate new ID
        do {
            $this->id = 'event-stream-' . $this->generateId();
        } while (Cache::has($this->id));

        // Store the stream in the cache
        $this->saveEvent(30);
    }

    public static function load(string $id): ?static
    {
        if (!Cache::has($id)) {
            return null;
        }

        $data = (array) Cache::get($id);

        return new static(
            id: $id,
            data: $data['data'],
            ticks: $data['ticks'],
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTicks(): float
    {
        return $this->ticks;
    }

    public function getSignature(): string
    {
        return hash('sha256', ($this->pendingEvent ?? 'ping') . '|' . json_encode($this->data));
    }

    public function set(string|array $key, mixed $value = null): void
    {
        if (is_array($key)) {
            $this->data = array_filter(array_replace($this->data, $key));
        } elseif (is_null($value)) {
            unset($this->data[$key]);
        } else {
            $this->data[$key] = $value;
        }

        $this->saveEvent();
    }

    public function doStream(string $signature): string
    {
        $data = Cache::get($this->id);

        if ($data['signature'] !== $signature) {
            if ($data['closed']) {
                return $this->writeStream('closed');
            }
        }

        if ($data['closed']) {
            return '';
        }

        $eventData = [];

        if ($data['event'] !== 'ping' || $data['changed']) {
            $eventData['time'] = date('c');
        }
        if ($data['changed']) {
            $eventData['contents'] = $data['data'];
        }


    }

    public function reconnect(): void
    {
        if ($this->closed) {
            return;
        }
        $this->pendingEvent = 'reconnect';
        $this->saveEvent();
    }

    public function close(): void
    {
        if ($this->closed) {
            return;
        }

        $this->pendingEvent = 'close';
        $this->saveEvent();
    }

    public function isClosed(): bool
    {
        return $this->closed || ($this->pendingEvent === 'reconnect');
    }

    protected function saveEvent(int $ttl = 5): void
    {
        Cache::set($this->id, [
            'data' => $this->data,
            'pendingEvent' => $this->pendingEvent,
            'signature' => $this->getSignature(),
            'closed' => $this->closed,
            'ticks' => $this->ticks,
        ], now()->addSeconds($ttl));
    }

    protected function generateId(): string
    {
        $id = '';
        for ($i = 0; $i < 32; ++$i) {
            $id .= substr('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', random_int(0, 61), 1);
        }
        return $id;
    }

    protected function getStreamData(): array
    {
        if (!Cache::has($this->getId())) {
            return [
                'data' => [],
                'pendingEvent' => null,
                'signature' => null,
                'closed' => true,
                'tick' => 1.0
            ];
        }

        return (array) Cache::get($this->getId());
    }

    protected function writeStream(string $event)
    {
        $contents = 'event: ' . $data['event'] . PHP_EOL;
        if (count($eventData)) {
            $contents .= 'data: ' . json_encode($eventData) . PHP_EOL;
        }
        $contents .= PHP_EOL;

        return $contents;
    }
}
