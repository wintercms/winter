<?php

namespace System\Controllers;

use Backend\Classes\Controller;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use System\Classes\EventStream as EventStreamInstance;

/**
 * Event stream controller.
 *
 * Handles the delivery of event-streaming data to the client.
 */
class EventStream extends Controller
{
    public function register()
    {
        $eventStream = new EventStreamInstance();
        $eventStream->register();

        return Response::json(['id' => $eventStream->getId()]);
    }

    public function subscribe(string $id)
    {
        $eventStream = EventStreamInstance::load($id);

        if (is_null($eventStream)) {
            return Response::make('Event stream not found', 404);
        }

        $response = new StreamedResponse();

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');

        $response->setCallback(function () use ($eventStream) {
            while (true) {
                if ($eventStream->isClosed()) {
                    break;
                }

                echo $eventStream->streamEvent();

                if (ob_get_level() > 0) {
                    ob_flush();
                }

                flush();

                // $eventStream->tick();

                if (connection_aborted()) {
                    break;
                }

                usleep($eventStream->getTicks() * 1000000);
            }
        });

        $response->send();
        $this->responseOverride = $response;
        return $response;
    }
}
