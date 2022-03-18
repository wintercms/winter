<?php

use Illuminate\Mail\Message;
use System\Classes\MailManager;
use System\Models\MailTemplate;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\TextPart;

class MailManagerTest extends PluginTestCase
{

    public function setUp() : void
    {
        parent::setUp();

        $this->message = new Message(new Email());

        foreach (['html', 'plain'] as $view) {
            $t = new MailTemplate();
            $t->is_custom = true;
            $t->code = "$view-view";
            $t->subject = "$view view [{{ mode }}]";
            $t->content_html = "my $view view content";
            $t->description = "my $view view description";
            $t->save();
        }
    }

    //
    // Tests
    //

    public function testAddContent_Html()
    {
        $plain = $raw = null;
        $html = new TextPart('html-view', $subtype="html");
        $data = ['mode' => 'test'];

        $result = MailManager::instance()->addContent($this->message, $html, $plain, $raw, $data);
        $symfonyMessage = $this->message->getSymfonyMessage();

        $this->assertEquals('text/html', $symfonyMessage->getPreparedHeaders()->getHeaderParameter('Content-Type'));
        $this->assertEquals('html view [test]', $symfonyMessage->getSubject());
    }

    public function testAddContent_Plain()
    {
        $html = $raw = null;
        $plain = new TextPart('plain-view');
        $data = ['mode' => 'test'];

        $result = MailManager::instance()->addContent($this->message, $html, $plain, $raw, $data);
        $symfonyMessage = $this->message->getSymfonyMessage();

        $this->assertTrue($result);
        $this->assertEquals('text/plain', $symfonyMessage->getPreparedHeaders()->getHeaderParameter('Content-Type'));
        $this->assertEquals('plain view [test]', $symfonyMessage->getSubject());
        $this->assertEquals('my plain view content', $symfonyMessage->getBody());
    }

    public function testAddContent_Raw()
    {
        $html = $plain = null;
        $raw = new TextPart('my raw content');
        $data = ['mode' => 'test'];

        $result = MailManager::instance()->addContent($this->message, $html, $plain, $raw, $data);
        $symfonyMessage = $this->message->getSymfonyMessage();

        $this->assertTrue($result);
        $this->assertEquals('text/plain', $symfonyMessage->getPreparedHeaders()->getHeaderParameter('Content-Type'));
        $this->assertEquals('No subject', $symfonyMessage->getSubject());
        $this->assertEquals($raw, $symfonyMessage->getBody());
    }

    public function testAddContent_Html_Plain()
    {
        $raw = null;
        $html = new TextPart('html-view', $subtype="html");
        $plain = new TextPart('plain-view');
        $data = ['mode' => 'test'];

        $result = MailManager::instance()->addContent($this->message, $html, $plain, $raw, $data);
        $symfonyMessage = $this->message->getSymfonyMessage();
        $parts = $symfonyMessage->getAttachments();

        $this->assertTrue($result);
        $this->assertEquals('text/html', $symfonyMessage->getPreparedHeaders()->getHeaderParameter('Content-Type'));
        $this->assertEquals('html view [test]', $symfonyMessage->getSubject());
        $this->assertTrue(str_contains($symfonyMessage->getBody(), 'my html view content'));

        $this->assertEquals(1, count($parts));
        $this->assertEquals('text/plain', $parts[0]->getPreparedHeaders()->getHeaderParameter('Content-Type'));
        $this->assertEquals('my plain view content', $parts[0]->getBody());
    }

    public function testAddContent_Html_Plain_Raw()
    {
        $html = new TextPart('html-view', $subtype="html");
        $plain = new TextPart('plain-view');
        $raw = new TextPart('my raw content');
        $data = ['mode' => 'test'];

        $result = MailManager::instance()->addContent($this->message, $html, $plain, $raw, $data);
        $symfonyMessage = $this->message->getSymfonyMessage();
        $parts = $symfonyMessage->getAttachments();

        $this->assertTrue($result);
        $this->assertEquals('text/html', $symfonyMessage->getPreparedHeaders()->getHeaderParameter('Content-Type'));
        $this->assertEquals('html view [test]', $symfonyMessage->getSubject());

        $this->assertEquals(1, count($parts));
        $this->assertEquals('text/plain', $parts[0]->getPreparedHeaders()->getHeaderParameter('Content-Type'));
        $this->assertEquals($raw, $parts[0]->getBody());
    }
}
