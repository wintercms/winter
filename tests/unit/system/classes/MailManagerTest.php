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

        $this->assertEquals($html, $symfonyMessage->getHtmlBody());
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
        $this->assertEquals($plain, $symfonyMessage->getTextBody());
        $this->assertEquals('plain view [test]', $symfonyMessage->getSubject());
    }

    public function testAddContent_Raw()
    {
        $html = $plain = null;
        $raw = new TextPart('my raw content');
        $data = ['mode' => 'test'];

        $result = MailManager::instance()->addContent($this->message, $html, $plain, $raw, $data);
        $symfonyMessage = $this->message->getSymfonyMessage();

        $this->assertTrue($result);
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
        $this->assertEquals('html view [test]', $symfonyMessage->getSubject());
        $this->assertEquals($html, $symfonyMessage->getHtmlBody());

        $this->assertEquals(1, count($parts));
        $this->assertEquals($plain, $symfonyMessage->getTextBody());
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
        $this->assertEquals('html view [test]', $symfonyMessage->getSubject());
        $this->assertEquals($html, $symfonyMessage->getHtmlBody());

        $this->assertEquals(1, count($parts));
        $this->assertEquals($plain, $symfonyMessage->getTextBody());
        $this->assertEquals($raw, $symfonyMessage->getBody());
    }
}
