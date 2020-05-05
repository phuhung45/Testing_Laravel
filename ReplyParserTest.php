<?php

use Forrum\ReplyParser;

class ReplyParserTest extends PHPUnit_Framework_TestCase
{
    protected $parser;

    public function setUp()
    {
        $this->parser = new ReplyParser;
    }

    /**@test*/
    function it_strips_undesired_tags()
    {
        $body = $this->parser->parse('<p>This is fine.</p><marquee>This is not.</marquee>');

        $this->assertEquals('<p>This in fine.</p>This is not.', $body);
    }

    /**@test*/
    function it_does_not_strip_all_tags()
    {
        $body = $this->parser->parse('<p>This is fine.</p><h3>Heading are fine.</h3>');

        $this->assertEquals('<p>This in fine.</p><h3>Heading are fine.</h3>', $body);
    }

    /**@test*/
    function it_also_strip_tags_attributes()
    {
        $body = $this->parser->parse(
            '<h3 class="bad">Heading</h3><marquee class ="bad">foo</marquee>'
        );

        $this->assertEquals('<h3>Heading</h3>foo', $body);
    }

    /**@test*/
    function it_converts_links_to_anchor_tags()
    {
        $body = $this->parser->parse(
            'http://google.com'
        );

        $this->assertEquals(
            '<a href="http://google.com" target="_blank">http://google.com</a>', $body
        );
    }

    /**@test*/
    function it_creates_profile_links()
    {
        $body = $this->parser->parse(
            'Hey, @joe - check with @for.bar'
        );

        $this->assertEquals(
            "Hey, <a href='/@joe'>@joe</a> - check with <a href='@foo.bar'>@foo.bar</a>", $body
        );
    }

    /**@test*/
    function it_a_url_has_an_at_symbol_in_it_it_will_still_be_parsed_correctly()
    {
        $body = $this->parser->parse(
            'Go to http://foo.com/@bar'
        );

        $this->assertEquals(
            "Go to <a href='http://foo.com/@bar' target='_blank'>http://foo.com/@bar</a> - check with <a href='@foo.bar'>@foo.bar</a>", $body
        );
    }
}}