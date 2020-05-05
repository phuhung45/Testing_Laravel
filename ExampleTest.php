<?php


class ExampleTest extends PHPUnit_Framework_TestCase
{
    /**@Test*/
    function it_normalizes_a_string_for_the_cache_key()
    {
        $cache = $this->prophesize(RussianCache::class);
        $directive = new BladeDirective($cache->reveal());


        $cache->has('cache-key')->shouldBeCalled();


        $directive->setUp('cache-key');

    }

    /**@Test*/
    function it_normalizes_a_cacheable_model_for_the_cache_key()
    {
        $cache = $this->prophesize(RussianCache::class);
        $directive = new BladeDirective($cache->reveal());


        $cache->has('stub-cache-key')->shouldBeCalled();


        $directive->setUp(new ModelStub);

    }


    /**@Test*/
    function it_normalizes_an_array_for_the_cache_key()
    {
        $cache = $this->prophesize(RussianCache::class);
        $directive = new BladeDirective($cache->reveal());


        $cache->has(md5('foobar'))->shouldBeCalled();


        $directive->setUp(['foo', 'bar']);

    }
}

class ModelStub
{
    public function getCacheKey()
    {
        return 'stub-cache-key';
    }
}
