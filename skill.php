    <?php

    namespace Laracasts;

    use  Illuminate\Databes\Eloquent\Model;

    class skill extends Model
    {
        /**
         * Fillable fields for a skill
         *
         * @var array
        */
        protected $fillable = [
            'name',
            'description',
            'thumbnail'
        ];

        /**
         * Get the length of all series in the skill.
         */
        public function getLengAttribute()
        {
            return $this->series->sum('length');
        }

        public function completionRate()
        {
            $seriesCompleted = $this->series->filter(function ($series){
                return $series->progress()->isComplete();
            })->count();
            return round($seriesCompleted / $this->series->count() *100)
        }
    }