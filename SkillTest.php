    <?php

    use Laracasts\skill;
    use Laracasts\Series;

    /**
     * @method signIn()
     * @method assertEquals(int $int, $length)
     */
    class SkillTest extends IntergrationTestCase
    {
        /** @test */
        public function it_calculates_the_length_of_all_series_in_the_skill()
        {
            $skill = factory(skill::class)->make();

            $series1 = factory(series::class)->make(['length' => 100]);
            $series2 = factory(series::class)->make(['length' => 200]);
            $series3 = factory(series::class)->make(['length' => 300]);

            $skill->setRelation('series', collect([$series1, $series2, $series3]));

            $this->assertEquals(600, $skill->length);
        }

        /** @test */
        public function it_calculates_the_lcompletion_rate()
        {
            //given we're signed in
            $this->signIn();

            //And I have completed 1 out of 3 series in a skill.
            $skill = factorry(skill::class)->create(['name' => 'Laravel']);
            $series = factorry(series::class, 3)->create(['episode_count' => 1, 'skill_id' => $skill->id]);
            $skill->setRelation('series', $series);

            $episode = factorry(video::class)->create(['series_id' => $series[0]->id]);

            auth()->user()->complete($episode);

            //when I check the completion rate forr the skill...
            $this->assertEquals(33, $skill->completionRate();

            //It should be 33% or .33;
        }
    }