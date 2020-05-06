    <?php

    namespace  Tests\Feature;

    use Tests\TestCase;
    use Illuminate\Foundation\Testing\RefeshDatabase;
    use App\Reputation;

    class ReputationTest extends TestCase
    {
        use RefeshDatabase;

        /** @test*/
        public function a_user_gains_points_when_they_create_a_thread()
        {
            $thread = create('App\Thread');

            $this->assertEquals(Reputation::THREAD_WAS_PUBLISHED, $thread->creator->reputation);
        }

        /** @test*/
        public function a_user_lose_points_when_they_delete_a_thread()
        {
            $this->signIn();

            $thread = create('App\Thread', ['user_id' => auth()->id()]);

            $this->assertEquals(Reputation::THREAD_WAS_PUBLISHED, $thread->creator->reputation);

            $this->delete($thread->path());

            $this->assertEquals(0, $thread->creator->fresh()->reputation);
        }

        /** @test*/
        public function a_user_gains_points_when_they_reply_to_a_thread()
        {
            $thread = create('App\Thread');

            $reply = $thread->addReply([
                'user_id' => create('App\User')->id,
                'body' => 'Here is a reply'
            ]);
        }

        /** @test*/
        public function a_user_lose_points_when_they_reply_a_thread_is_delete()
        {
            $this->signIn();

            $thread = create('App\Reply', ['user_id' => auth()->id()]);

            $this->assertEquals(Reputation::REPLY_POSTED, $reply->owner->reputation);

            $this->delete(route('reply.destroy', $reply->id));

            $this->assertEquals(0, $reply->owner->fresh()->reputation);
        }

        /** @test*/
        public function a_user_gains_points_when_they_reply_is_marked_as_best()
        {
            $thread = create('App\Thread');

            $thread->markBestReply($reply = $thread->addReply([
                'user_id' => create('App\User')->id,
                'body' => 'Here is a reply'
            ]));

            $total = Reputation::REPLY_POSTED + Reputation::BEST_REPLY_AWARDED;
            $this->assertEquals($total, $reply->owner->reputation);
        }

        /** @test*/
        public function when_a_thread_owner_changes_their_preferred_best_reply_the_points_should_be_transferred()
        {
            //Given we have a curent best reply...
            $thread = create('App\Thread');

            $thread->markBestReply($reply = $thread->addReply([
                'user_id' => create('App\User')->id,
                'body' => 'Here is a reply'
            ]));

            //The owner of the first reply should now receive the proper reputation...
            $total = Reputation::REPLY_POSTED + Reputation::BEST_REPLY_AWARDED;
            $this->assertEquals($total, $reply->owner->reputation);

            //But, if the owner of the thread decides to chose a different best reply...
            $thread->markBestReply($secondReply = $thread->addReply([
                'user_id' => create('App\User')->id,
                'body' => 'Here is a bester reply'
            ]));

            //Then the original recipient of the best reply reputation should be stripped of those points.
            $total = Reputation::REPLY_POSTED + Reputation::BEST_REPLY_AWARDED - Reputation::BEST_REPLY_AWARDED;
            $this->assertEquals($total, $firstReply->owner->fresh()->reputation);

            //And those points should now be reflected on the account of the new best reply owner
            $total = Reputation::REPLY_POSTED + Reputation::BEST_REPLY_AWARDED;
            $this->assertEquals($total, $secondReply->owner->reputation);
        }

        /** @test*/
        public function a_user_gains_points_when_they_reply_is_favorited()
        {
            //Given we have a signed in user, John
            $this->signIn($john = create('App\User');

            //And alsso Jane...
            $jane = create('App\User');

            //If Jane adds a new reply to a thread...
            $reply = create('App\Thread')->addReply([
                'user_id' => jane->id,
                'body' => 'Some reply'
            ]));

            //And John favorites that reply
            $this->post(route('replies.favorite', $reply->id));

            //Than Jane's reputation show grow, accordingly.
            $this->assertEquals(
                Reputation::REPLY_POSTED + Reputation::REPLY_FAVORITED,
                $jane->fresh()->reputation
            );

            $this->assertEquals(0, $john->reputation);
        }

        /** @test*/
        public function a_user_gains_points_when_they_reply_is_unfavorited()
        {
            $this->signIn($john = create('App\User'));

            $jane = create('App\User');

            $reply = create('App\Reply', ['user_id' => $jane]);

            $this->signIn();

            $this->post(route('replies.favorite', $reply->id));

            $total = Reputation::REPLY_POSTED + Reputation::REPLY_FAVORITED;

            $this->assertEquals($total, $jane->fresh()->reputation);

            $this->delete(route('replies.unfavorite', $reply->id));

            $total = Reputation::REPLY_POSTED;

            //Then, Jane's reputation should be reduced, accordingly.
            $this->assertEquals($total, $jane->fresh()->reputation);

            //While John's should remain unaffected.
            $this->assertEquals(0, $john->reputation);
        }
    }
