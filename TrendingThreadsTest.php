    <?php

    namespace Tests\Feature;

    use App\Trending;
    use Illuminate\Foundation\Testing\DatabaseMigrations;
    use Illuminate\Support\Facades\Redis;
    use Tests\TestCase;

    class TrendingThreadsTest extends TestCase
    {
        use DatabaseMigrations;

        protected function setup()
        {
            parent::setUp();

            Redis::del('trending_threads');
        }

        /**@test*/
        public function it_increments_a_threads_score_each_time_it_is_read()
        {
            app()->instance(\App\Trending::class, new FakeTrending());

            $trending = app(Trending::class);

            $trending->assertEmpty();

            $thread = create('App\Thread');

            $this->call('GET', $thread->path());

            $trending->assertCount(1);

            $this->assertEquals($thread->title, json_decode($trending->threads[0])->title);
        }
    }

        class FakeTrending extends \App\Trending
        {
            public $threads = [];

            public function push($thread)
            {
                $this->threads[] = $thread;
            }

            public function assertEmpty()
            {
                \PHPUnit_Framework_assert::assertEmpty($this->threads);
            }

            public function assertCount()
            {
                \PHPUnit_Framework_assert::assertCount($this->threads);

            }
    }