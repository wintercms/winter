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
    public function __construct(
        protected string $id = '',
        protected array $data = [],
        protected string $event = 'start',
        protected bool $closed = false,
        protected bool $changed = true,
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

        $data = Cache::get($id);

        return new static(
            id: $id,
            data: $data['data'],
            event: $data['event'],
            closed: $data['closed'],
            changed: $data['changed'],
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

    public function set(string|array $key, mixed $value = null): void
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }

        $this->event = 'update';
        $this->changed = true;
        $this->saveEvent();
    }

    public function tick(): void
    {
        $this->event = 'ping';
        $this->changed = false;
        $this->saveEvent();
    }

    public function reconnect(): void
    {
        if ($this->closed) {
            return;
        }
        $this->event = 'reconnect';
        $this->closeStream();
    }

    public function close(): void
    {
        if ($this->closed) {
            return;
        }

        $this->event = 'close';
        $this->closeStream();
    }

    public function isClosed(): bool
    {
        return $this->closed;
    }

    protected function closeStream(): void
    {
        $this->closed = true;
        $this->saveEvent();
    }

    protected function saveEvent(int $ttl = 5): void
    {
        var_dump(Cache::set($this->id, [
            'event' => $this->event,
            'data' => $this->data,
            'changed' => $this->changed,
            'closed' => $this->closed,
            'ticks' => $this->ticks,
        ], now()->addSeconds($ttl)));
    }

    protected function generateId(): string
    {
        $id = '';
        for ($i = 0; $i < 32; ++$i) {
            $id .= substr('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', random_int(0, 61), 1);
        }
        return $id;
    }

    public function streamEvent(): string
    {
        $data = Cache::get($this->id);

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

        $contents = 'event: ' . $data['event'] . PHP_EOL;
        if (count($eventData)) {
            $contents .= 'data: ' . json_encode($eventData) . PHP_EOL;
        }
        $contents .= PHP_EOL;

        return $contents;
    }
}
