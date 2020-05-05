    <?php


    namespace Tests\Feature;

    use App\Trending;
    use Tests\TestCase;
    use Illuminate\Foundation\Testing\DatabaseMigrations;

    class TrendingTest extends TestCase
    {
        use DatabaseMigrations;

        public function setUp()
        {
            parent::setUp();

            $this->trending = new Trending();

            $this->trending->reset();
        }

        /**@test */
        public function it_stores_trending_threads_in_redis()
        {

            $this->assertEmpty($this->trending->get());

            $this->trending->push(new FakeThread('Boring Thread'));

            $this->trending->push(new FakeThread('Popular Thread'));
            $this->trending->push(new FakeThread('Popular Thread'));
            $this->trending->push(new FakeThread('Popular Thread'));

            $this->assertCount(2, $trending = $this->trending->get());
            dd($trending);
            $this->assertEquals(['Popular Thread', 'Boring Thread'], array_pluck($trending, 'title'));
        }
    }

    class FakeThread
    {
        public $title;

        public function __construct($title)
        {
            $this->title = $title;
        }

        public function path()
        {
            return 'some/path';
        }
    }